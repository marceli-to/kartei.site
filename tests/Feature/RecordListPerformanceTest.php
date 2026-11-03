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
  $many = countQueries(fn () => $this->getJson("/api/records/{$this->archive->uuid}")->assertOk()->assertJsonCount(45));

  expect($many)->toBe($few);
  expect($many)->toBeLessThan(20);
});

it('keeps positional display numbers that shift after a deletion', function () {
  [$first, $second, $third] = seedRecords($this->archive, $this->category, 3);
  [$reg1] = seedRecords($this->archive, $this->register, 1);
  Sanctum::actingAs($this->admin);

  $numbers = fn () => collect($this->getJson("/api/records/{$this->archive->uuid}")->json())
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
