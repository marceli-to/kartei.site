<?php

use App\Models\Archive;
use App\Models\Category;
use App\Models\Media;
use App\Models\Record;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

function seedRecords(Archive $archive, Category $category, int $count): array
{
  $records = [];
  for ($i = 0; $i < $count; $i++) {
    $record = Record::create(['category_id' => $category->id, 'archive_id' => $archive->id]);
    // Distinct, increasing creation times
    $record->forceFill(['created_at' => now()->subMinutes(1000 - count($records))])->save();
    $media = new Media([
      'name' => "img$i", 'original_name' => "img{$i}_original.jpg", 'resized_name' => "img{$i}_resized.jpg",
      'resized_width' => 1, 'resized_height' => 1, 'aspect_ratio' => 'square', 'mime_type' => 'image/jpeg', 'size' => 1,
    ]);
    $media->mediable()->associate($record);
    $media->save();
    $records[] = $record;
  }
  return $records;
}

function countQueries(callable $callback): int
{
  DB::flushQueryLog();
  DB::enableQueryLog();
  $callback();
  $count = count(DB::getQueryLog());
  DB::disableQueryLog();
  return $count;
}

beforeEach(function () {
  app(PermissionRegistrar::class)->forgetCachedPermissions();
  Role::create(['name' => 'Admin', 'guard_name' => 'web']);
  Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);

  $this->archive = Archive::create(['name' => 'Kartei', 'acronym' => 'KA']);
  $this->category = Category::create(['number' => '1', 'name' => 'Cat', 'custom_id' => 'CA', 'archive_id' => $this->archive->id]);
  $this->register = Category::create(['number' => '1.1', 'name' => 'Reg', 'custom_id' => 'RE', 'archive_id' => $this->archive->id, 'parent_id' => $this->category->id]);

  $this->admin = User::create([
    'firstname' => 'Test', 'name' => 'Admin', 'email' => strtolower(Str::random(10)) . '@example.test',
    'password' => 'secret-password', 'email_verified_at' => now(),
  ]);
  $this->admin->assignRole('Admin');
  $this->admin->archives()->attach($this->archive->id);
});

it('lists records with a constant number of queries', function () {
  seedRecords($this->archive, $this->category, 3);
  seedRecords($this->archive, $this->register, 2);
  Sanctum::actingAs($this->admin);
  // Warm-up: the first request also loads the permission cache
  $this->getJson("/api/records/{$this->archive->uuid}")->assertOk();
  Sanctum::actingAs($this->admin->fresh());
  $few = countQueries(fn () => $this->getJson("/api/records/{$this->archive->uuid}")->assertOk());

  seedRecords($this->archive, $this->category, 20);
  seedRecords($this->archive, $this->register, 20);
  Sanctum::actingAs($this->admin->fresh());
  $many = countQueries(fn () => $this->getJson("/api/records/{$this->archive->uuid}")->assertOk()->assertJsonCount(45, 'data'));

  expect($many)->toBe($few);
  expect($many)->toBeLessThan(20);
});

it('keeps positional display numbers that shift after a deletion', function () {
  [$first, $second, $third] = seedRecords($this->archive, $this->category, 3);
  [$reg1] = seedRecords($this->archive, $this->register, 1);
  Sanctum::actingAs($this->admin);

  $numbers = fn () => collect($this->getJson("/api/records/{$this->archive->uuid}")->json('data'))
    ->pluck('display_number', 'uuid');

  $before = $numbers();
  expect($before[$first->uuid])->toBe('1.0001 / KA_CA');
  expect($before[$third->uuid])->toBe('1.0003 / KA_CA');
  expect($before[$reg1->uuid])->toBe('1.1.0001 / KA_CA_RE');

  $second->delete();
  $after = $numbers();
  expect($after[$third->uuid])->toBe('1.0002 / KA_CA');

  // A single record (edit view) computes the same number
  $this->getJson("/api/record/{$third->uuid}")->assertJsonPath('display_number', '1.0002 / KA_CA');
});

// ---------------------------------------------------------------------------
// Pagination, filters and sorting (server side)
// ---------------------------------------------------------------------------

function listRecords($test, array $query = []): \Illuminate\Testing\TestResponse
{
  return $test->getJson("/api/records/{$test->archive->uuid}?" . http_build_query($query))->assertOk();
}

it('pages through the records and reports the totals', function () {
  seedRecords($this->archive, $this->category, 5);
  Sanctum::actingAs($this->admin);

  $first = listRecords($this, ['per_page' => 2]);
  $first->assertJsonCount(2, 'data')
    ->assertJsonPath('meta.total', 5)
    ->assertJsonPath('meta.archive_total', 5)
    ->assertJsonPath('meta.last_page', 3)
    ->assertJsonPath('data.0.display_number', '1.0001 / KA_CA');

  listRecords($this, ['per_page' => 2, 'page' => 3])
    ->assertJsonCount(1, 'data')
    ->assertJsonPath('data.0.display_number', '1.0005 / KA_CA');
});

it('rejects invalid list parameters', function () {
  Sanctum::actingAs($this->admin);
  $this->getJson("/api/records/{$this->archive->uuid}?per_page=500")->assertUnprocessable();
  $this->getJson("/api/records/{$this->archive->uuid}?sort=nonsense")->assertUnprocessable();
});

it('filters by category including its registers, by register and by tag', function () {
  $other = Category::create(['number' => '2', 'name' => 'Other', 'custom_id' => 'OT', 'archive_id' => $this->archive->id]);
  [$inCategory] = seedRecords($this->archive, $this->category, 1);
  [$inRegister] = seedRecords($this->archive, $this->register, 1);
  [$inOther] = seedRecords($this->archive, $other, 1);
  $tag = \App\Models\Tag::create(['name' => 'rot', 'archive_id' => $this->archive->id]);
  $inOther->tags()->attach($tag->id);
  Sanctum::actingAs($this->admin);

  $uuids = fn (array $query) => collect(listRecords($this, $query)->json('data'))->pluck('uuid')->sort()->values()->all();

  expect($uuids(['categories' => [$this->category->uuid]]))
    ->toBe(collect([$inCategory->uuid, $inRegister->uuid])->sort()->values()->all());
  expect($uuids(['registers' => [$this->register->uuid]]))->toBe([$inRegister->uuid]);
  expect($uuids(['tags' => [$tag->uuid]]))->toBe([$inOther->uuid]);

  listRecords($this, ['tags' => [$tag->uuid]])
    ->assertJsonPath('meta.total', 1)
    ->assertJsonPath('meta.archive_total', 3);
});

it('searches display numbers and field contents', function () {
  [, $second] = seedRecords($this->archive, $this->category, 2);
  [$inRegister] = seedRecords($this->archive, $this->register, 1);
  $second->fields()->create(['uuid' => (string) Str::uuid(), 'placeholder' => 'Titel', 'content' => 'Alte Mühle 50%']);
  Sanctum::actingAs($this->admin);

  $uuids = fn (string $search) => collect(listRecords($this, ['search' => $search])->json('data'))->pluck('uuid')->all();

  expect($uuids('mühle'))->toBe([$second->uuid]);
  expect($uuids('50%'))->toBe([$second->uuid]);
  expect($uuids('0%'))->toBe([$second->uuid]); // % is literal, not a wildcard
  expect($uuids('KA_CA_RE'))->toBe([$inRegister->uuid]);
  expect($uuids('1.0002 / ka_ca'))->toBe([$second->uuid]);
  expect($uuids('nichts'))->toBe([]);
});

it('sorts by archive order or by date', function () {
  $this->category->update(['global_order' => 2]);
  $this->register->update(['global_order' => 1]);
  [$cat1, $cat2] = seedRecords($this->archive, $this->category, 2);
  [$reg1] = seedRecords($this->archive, $this->register, 1);
  $cat1->forceFill(['created_at' => now()->subDays(3), 'updated_at' => now()->addDay()])->saveQuietly();
  $cat2->forceFill(['created_at' => now()->subDays(2)])->saveQuietly();
  $reg1->forceFill(['created_at' => now()->subDays(1)])->saveQuietly();
  Sanctum::actingAs($this->admin);

  $uuids = fn (string $sort) => collect(listRecords($this, ['sort' => $sort])->json('data'))->pluck('uuid')->all();

  expect($uuids('name'))->toBe([$reg1->uuid, $cat1->uuid, $cat2->uuid]);
  expect($uuids('created_at'))->toBe([$reg1->uuid, $cat2->uuid, $cat1->uuid]);
  expect($uuids('updated_at')[0])->toBe($cat1->uuid);
});
