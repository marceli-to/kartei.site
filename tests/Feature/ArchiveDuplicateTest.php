<?php

use App\Models\Archive;
use App\Models\Category;
use App\Models\Record;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/*
 * Stackr #151: "Kartei duplizieren" copies the setup of an archive the user owns
 * (basic information, structure, card template, tags, members with their permissions),
 * never the records or the title image.
 */

function duplicateTestUser(?string $role = null): User
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
  app(PermissionRegistrar::class)->forgetCachedPermissions();
  Role::create(['name' => 'Admin', 'guard_name' => 'web']);
  $this->viewer = Role::create(['name' => 'Viewer', 'guard_name' => 'web']);

  $this->admin = duplicateTestUser('Admin');
  $this->archive = Archive::create([
    'name' => 'Sammlung',
    'acronym' => 'SA',
    'settings' => [
      'has_images' => false,
      'record_fields' => [
        ['uuid' => 'field-a', 'placeholder' => 'Künstler/in', 'order' => 0],
        ['uuid' => 'field-b', 'placeholder' => 'Jahr', 'order' => 1],
      ],
    ],
  ]);
  $this->admin->archives()->attach($this->archive->id);

  $this->category = Category::create(['number' => '1', 'name' => 'Malerei', 'custom_id' => 'MA', 'archive_id' => $this->archive->id, 'order' => 0]);
  Category::create(['number' => '1', 'name' => 'Abstrakt', 'custom_id' => 'AB', 'archive_id' => $this->archive->id, 'parent_id' => $this->category->id, 'order' => 0]);
  Tag::create(['name' => 'Gerahmt', 'order' => 0, 'archive_id' => $this->archive->id]);
  Record::create(['category_id' => $this->category->id, 'archive_id' => $this->archive->id]);

  // A member with a per-archive permission
  $this->member = duplicateTestUser();
  $this->member->archives()->attach($this->archive->id, ['role_id' => $this->viewer->id, 'added_by' => $this->admin->id, 'added_at' => now()]);
  $this->member->givePermissionTo(Permission::create([
    'name' => 'edit.card.' . $this->archive->id,
    'guard_name' => 'web',
    'archive_id' => $this->archive->id,
    'publish' => false,
  ]));
});

it('copies the setup of an archive, without records', function () {
  Sanctum::actingAs($this->admin);

  $response = $this->postJson("/api/archive/{$this->archive->uuid}/duplicate")->assertCreated();

  $copy = Archive::where('uuid', $response->json('uuid'))->firstOrFail();
  expect($copy->name)->toBe('Sammlung (Kopie)')
    ->and($copy->acronym)->toBe('SA')
    ->and($copy->records()->count())->toBe(0)
    ->and($copy->media)->toBeNull();

  // Card template: same fields, new uuids
  expect($copy->settings['has_images'])->toBeFalse()
    ->and(collect($copy->settings['record_fields'])->pluck('placeholder')->all())->toBe(['Künstler/in', 'Jahr'])
    ->and(collect($copy->settings['record_fields'])->pluck('uuid')->intersect(['field-a', 'field-b'])->all())->toBe([]);

  // Structure: the register hangs under the copied category
  $category = $copy->categories()->first();
  expect($category->name)->toBe('Malerei')
    ->and($category->uuid)->not->toBe($this->category->uuid)
    ->and($category->registers()->pluck('name')->all())->toBe(['Abstrakt']);

  expect($copy->tags()->pluck('name')->all())->toBe(['Gerahmt']);
});

it('takes the members along with their roles and permissions', function () {
  Sanctum::actingAs($this->admin);

  $uuid = $this->postJson("/api/archive/{$this->archive->uuid}/duplicate")->assertCreated()->json('uuid');
  $copy = Archive::where('uuid', $uuid)->firstOrFail();

  $admin = $this->admin->fresh();
  $member = $this->member->fresh();
  app(PermissionRegistrar::class)->forgetCachedPermissions();

  expect($admin->isAdminOf($copy))->toBeTrue();
  expect(DB::table('archive_user')->where('archive_id', $copy->id)->where('user_id', $member->id)->value('role_id'))
    ->toBe($this->viewer->id);
  expect($member->hasPermissionTo('edit.card.' . $copy->id))->toBeTrue()
    ->and($member->hasPermissionTo('edit.card.' . $this->archive->id))->toBeTrue();
  expect(Permission::where('name', 'edit.card.' . $copy->id)->value('archive_id'))->toBe($copy->id);
});

it('numbers further copies, also from a copy', function () {
  Sanctum::actingAs($this->admin);

  $names = collect(range(1, 2))->map(fn () => $this->postJson("/api/archive/{$this->archive->uuid}/duplicate")->json('name'));
  $copy = Archive::where('name', 'Sammlung (Kopie)')->firstOrFail();
  $names->push($this->postJson("/api/archive/{$copy->uuid}/duplicate")->json('name'));

  expect($names->all())->toBe(['Sammlung (Kopie)', 'Sammlung (Kopie 2)', 'Sammlung (Kopie 3)']);
});

it('lets only the archive admin duplicate', function () {
  Sanctum::actingAs($this->member);
  $this->postJson("/api/archive/{$this->archive->uuid}/duplicate")->assertForbidden();

  Sanctum::actingAs(duplicateTestUser('Admin'));
  $this->postJson("/api/archive/{$this->archive->uuid}/duplicate")->assertForbidden();

  expect(Archive::count())->toBe(1);
});
