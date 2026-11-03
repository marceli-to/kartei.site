<?php

use App\Models\Archive;
use App\Models\Category;
use App\Models\Media;
use App\Models\Record;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

function cleanupAdmin(Archive $archive): User
{
  $user = User::create([
    'firstname' => 'Test',
    'name' => Str::random(6),
    'email' => strtolower(Str::random(10)) . '@example.test',
    'password' => 'secret-password',
    'email_verified_at' => now(),
  ]);
  $user->assignRole('Admin');
  $user->archives()->attach($archive->id);
  return $user;
}

function storedImage($owner, Archive $archive, string $name): Media
{
  $dir = "archives/{$archive->uuid}";
  Storage::disk('public')->put("$dir/{$name}_resized.jpg", 'x');
  Storage::disk('public')->put("$dir/{$name}_original.jpg", 'x');

  $media = new Media([
    'name' => $name, 'original_name' => "{$name}_original.jpg", 'resized_name' => "{$name}_resized.jpg",
    'resized_width' => 1, 'resized_height' => 1, 'aspect_ratio' => 'square', 'mime_type' => 'image/jpeg', 'size' => 1,
  ]);
  $media->mediable()->associate($owner);
  $media->save();
  return $media;
}

beforeEach(function () {
  Storage::fake('public');
  app(PermissionRegistrar::class)->forgetCachedPermissions();
  Role::create(['name' => 'Admin', 'guard_name' => 'web']);
  Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);

  $this->archive = Archive::create(['name' => 'Kartei', 'acronym' => 'KA']);
  $this->admin = cleanupAdmin($this->archive);
  $this->category = Category::create(['number' => '1', 'name' => 'Cat', 'custom_id' => 'CA', 'archive_id' => $this->archive->id]);
  $this->register = Category::create(['number' => '1', 'name' => 'Reg', 'custom_id' => 'RE', 'archive_id' => $this->archive->id, 'parent_id' => $this->category->id]);
  $this->dir = "archives/{$this->archive->uuid}";
});

it('deletes a record\'s images when the record is deleted', function () {
  $record = Record::create(['category_id' => $this->category->id, 'archive_id' => $this->archive->id]);
  $media = storedImage($record, $this->archive, 'photo');
  $other = Record::create(['category_id' => $this->category->id, 'archive_id' => $this->archive->id]);
  storedImage($other, $this->archive, 'keep');

  Sanctum::actingAs($this->admin);
  $this->deleteJson("/api/record/{$record->uuid}")->assertOk();

  expect(Media::find($media->id))->toBeNull();
  Storage::disk('public')->assertMissing("{$this->dir}/photo_resized.jpg");
  Storage::disk('public')->assertMissing("{$this->dir}/photo_original.jpg");
  Storage::disk('public')->assertExists("{$this->dir}/keep_resized.jpg");
  expect(Record::withTrashed()->find($record->id)->trashed())->toBeTrue();
});

it('deletes images of all records in a category and its registers', function () {
  $inCategory = Record::create(['category_id' => $this->category->id, 'archive_id' => $this->archive->id]);
  $inRegister = Record::create(['category_id' => $this->register->id, 'archive_id' => $this->archive->id]);
  storedImage($inCategory, $this->archive, 'cat');
  storedImage($inRegister, $this->archive, 'reg');

  Sanctum::actingAs($this->admin);
  $this->deleteJson("/api/category/{$this->category->uuid}")->assertNoContent();

  expect(Media::count())->toBe(0);
  Storage::disk('public')->assertMissing("{$this->dir}/cat_resized.jpg");
  Storage::disk('public')->assertMissing("{$this->dir}/reg_original.jpg");
});

it('deletes the whole image folder and all media rows with the archive', function () {
  $record = Record::create(['category_id' => $this->category->id, 'archive_id' => $this->archive->id]);
  storedImage($record, $this->archive, 'photo');
  storedImage($this->archive, $this->archive, 'cover');

  $otherArchive = Archive::create(['name' => 'Other', 'acronym' => 'OT']);
  storedImage($otherArchive, $otherArchive, 'othercover');

  Sanctum::actingAs($this->admin);
  $this->deleteJson("/api/archive/{$this->archive->uuid}")->assertOk();

  expect(Archive::find($this->archive->id))->toBeNull();
  expect(Media::count())->toBe(1);
  Storage::disk('public')->assertMissing($this->dir);
  Storage::disk('public')->assertExists("archives/{$otherArchive->uuid}/othercover_resized.jpg");
});

it('purges abandoned temp uploads older than 24 hours', function () {
  $disk = Storage::disk('public');
  $disk->put('temp/old_resized.jpg', 'x');
  $disk->put('temp/new_resized.jpg', 'x');
  touch($disk->path('temp/old_resized.jpg'), now()->subHours(25)->getTimestamp());

  $this->artisan('media:purge-temp')->assertSuccessful();

  $disk->assertMissing('temp/old_resized.jpg');
  $disk->assertExists('temp/new_resized.jpg');
});

it('only deletes orphans with --force', function () {
  $record = Record::create(['category_id' => $this->category->id, 'archive_id' => $this->archive->id]);
  storedImage($record, $this->archive, 'live');
  $deletedRecord = Record::create(['category_id' => $this->category->id, 'archive_id' => $this->archive->id]);
  $orphan = storedImage($deletedRecord, $this->archive, 'orphan');
  $deletedRecord->delete();
  Storage::disk('public')->put("{$this->dir}/stray_resized.jpg", 'x');
  Storage::disk('public')->put('archives/00000000-0000-0000-0000-000000000000/gone_resized.jpg', 'x');

  $this->artisan('media:prune-orphans')->assertSuccessful();
  expect(Media::find($orphan->id))->not->toBeNull();
  Storage::disk('public')->assertExists("{$this->dir}/stray_resized.jpg");

  $this->artisan('media:prune-orphans --force')->assertSuccessful();
  expect(Media::find($orphan->id))->toBeNull();
  Storage::disk('public')->assertMissing("{$this->dir}/orphan_resized.jpg");
  Storage::disk('public')->assertMissing("{$this->dir}/stray_resized.jpg");
  Storage::disk('public')->assertMissing('archives/00000000-0000-0000-0000-000000000000');
  Storage::disk('public')->assertExists("{$this->dir}/live_resized.jpg");
  Storage::disk('public')->assertExists("{$this->dir}/live_original.jpg");
});
