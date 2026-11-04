<?php

use App\Models\Archive;
use App\Models\Category;
use App\Models\Record;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Actions\User\Delete as DeleteUserAction;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

function accountUser(?string $role = null): User
{
  $user = User::create([
    'firstname' => 'Test',
    'name' => Str::random(6),
    'email' => strtolower(Str::random(10)) . '@example.test',
    'password' => 'secret-password',
    'email_verified_at' => now(),
  ]);
  if ($role) $user->assignRole($role);
  return $user;
}

beforeEach(function () {
  Storage::fake('public');
  app(PermissionRegistrar::class)->forgetCachedPermissions();
  Role::create(['name' => 'Admin', 'guard_name' => 'web']);
  $this->memberRole = Role::create(['name' => 'Viewer', 'guard_name' => 'web']);
});

it('deletes the archives an account owns, with their records', function () {
  $admin = accountUser('Admin');
  $owned = Archive::create(['name' => 'Eigene', 'acronym' => 'EI']);
  $admin->archives()->attach($owned->id);
  $category = Category::create(['number' => '1', 'name' => 'Cat', 'custom_id' => 'CA', 'archive_id' => $owned->id]);
  $record = Record::create(['category_id' => $category->id, 'archive_id' => $owned->id]);
  Storage::disk('public')->put("archives/{$owned->uuid}/x.jpg", 'x');

  (new DeleteUserAction())->execute($admin);

  expect(Archive::find($owned->id))->toBeNull();
  expect(Record::withTrashed()->find($record->id))->toBeNull();
  Storage::disk('public')->assertMissing("archives/{$owned->uuid}/x.jpg");
  expect(User::find($admin->id))->toBeNull();
});

it('keeps archives the account is only a member of, or that have another admin', function () {
  $admin = accountUser('Admin');

  $foreign = Archive::create(['name' => 'Fremd', 'acronym' => 'FR']);
  $admin->archives()->attach($foreign->id, ['role_id' => $this->memberRole->id]);

  $shared = Archive::create(['name' => 'Geteilt', 'acronym' => 'GE']);
  $admin->archives()->attach($shared->id);
  accountUser('Admin')->archives()->attach($shared->id);

  (new DeleteUserAction())->execute($admin);

  expect(Archive::find($foreign->id))->not->toBeNull();
  expect(Archive::find($shared->id))->not->toBeNull();
});

it('never deletes archives when a member account is deleted', function () {
  $member = accountUser();
  $archive = Archive::create(['name' => 'Kartei', 'acronym' => 'KA']);
  $member->archives()->attach($archive->id);

  (new DeleteUserAction())->execute($member);

  expect(Archive::find($archive->id))->not->toBeNull();
});

it('lists the archives that would be deleted with the account', function () {
  $admin = accountUser('Admin');

  $owned = Archive::create(['name' => 'Eigene', 'acronym' => 'EI']);
  $admin->archives()->attach($owned->id);
  $category = Category::create(['number' => '1', 'name' => 'Cat', 'custom_id' => 'CA', 'archive_id' => $owned->id]);
  Record::create(['category_id' => $category->id, 'archive_id' => $owned->id]);
  Record::create(['category_id' => $category->id, 'archive_id' => $owned->id]);

  $foreign = Archive::create(['name' => 'Fremd', 'acronym' => 'FR']);
  $admin->archives()->attach($foreign->id, ['role_id' => $this->memberRole->id]);

  \Laravel\Sanctum\Sanctum::actingAs($admin);
  $this->getJson('/api/user/owned-archives')
    ->assertOk()
    ->assertExactJson(['data' => [
      ['uuid' => $owned->uuid, 'name' => 'Eigene', 'records_count' => 2],
    ]]);
});
