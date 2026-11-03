<?php

use App\Models\Archive;
use App\Models\Category;
use App\Models\Media;
use App\Models\Record;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/*
 * Two tenants: "own" (admin + member) and "other" (admin), each with one archive.
 */

function makeUser(array $attributes = []): User
{
  return User::create(array_merge([
    'firstname' => 'Test',
    'name' => Str::random(6),
    'email' => strtolower(Str::random(10)) . '@example.test',
    'password' => 'secret-password',
    'email_verified_at' => now(),
  ], $attributes));
}

function makeAdminWithArchive(): array
{
  $admin = makeUser();
  $admin->assignRole('Admin');
  $archive = Archive::create(['name' => 'Kartei ' . Str::random(4), 'acronym' => 'KA']);
  $admin->archives()->attach($archive->id);
  return [$admin, $archive];
}

function grantArchivePermission(User $user, Archive $archive, string $permission): void
{
  $user->givePermissionTo(Permission::firstOrCreate([
    'name' => $permission . '.' . $archive->id,
    'guard_name' => 'web',
  ], ['archive_id' => $archive->id, 'publish' => false]));
}

function addMember(Archive $archive, Role $role, User $addedBy, array $permissions = []): User
{
  $member = makeUser(['created_by' => null]);
  $member->forceFill(['created_by' => $addedBy->id])->save();
  $member->archives()->attach($archive->id, ['role_id' => $role->id, 'added_by' => $addedBy->id, 'added_at' => now()]);
  foreach ($permissions as $permission) {
    grantArchivePermission($member, $archive, $permission);
  }
  return $member->fresh();
}

beforeEach(function () {
  app(PermissionRegistrar::class)->forgetCachedPermissions();

  foreach (['Super Admin', 'Admin', 'Manager', 'Archivist', 'Viewer'] as $name) {
    Role::create(['name' => $name, 'guard_name' => 'web']);
  }
  foreach (['edit.card', 'create.card', 'delete.card', 'edit.users'] as $name) {
    Permission::create(['name' => $name, 'guard_name' => 'web', 'publish' => $name !== 'edit.users']);
  }

  [$this->admin, $this->archive] = makeAdminWithArchive();
  [$this->otherAdmin, $this->otherArchive] = makeAdminWithArchive();

  $this->category = Category::create(['number' => '1', 'name' => 'Cat', 'custom_id' => 'CA', 'archive_id' => $this->archive->id]);
  $this->otherCategory = Category::create(['number' => '1', 'name' => 'Other', 'custom_id' => 'OT', 'archive_id' => $this->otherArchive->id]);
  $this->otherRecord = Record::create(['category_id' => $this->otherCategory->id, 'archive_id' => $this->otherArchive->id]);
  $this->otherTag = Tag::create(['name' => 'secret', 'archive_id' => $this->otherArchive->id]);
});

// ---------------------------------------------------------------------------
// Archive data
// ---------------------------------------------------------------------------

it('lets members read their own archive', function () {
  Sanctum::actingAs($this->admin);

  $this->getJson("/api/archive/{$this->archive->uuid}")->assertOk();
  $this->getJson("/api/archive/meta/{$this->archive->uuid}")->assertOk();
  $this->getJson("/api/records/{$this->archive->uuid}")->assertOk();
});

it('blocks reading another tenant\'s archive data', function (string $route) {
  Sanctum::actingAs($this->admin);

  $url = str_replace(
    ['{archive}', '{record}'],
    [$this->otherArchive->uuid, $this->otherRecord->uuid],
    $route
  );

  $this->getJson($url)->assertForbidden();
})->with([
  '/api/archive/{archive}',
  '/api/archive/meta/{archive}',
  '/api/archive/settings/{archive}',
  '/api/categories/{archive}',
  '/api/records/{archive}',
  '/api/record/{record}',
  '/api/tags/{archive}',
  '/api/archive/users/{archive}',
]);

it('blocks changing or deleting another tenant\'s archive data', function () {
  Sanctum::actingAs($this->admin);

  $this->putJson("/api/archive/settings/{$this->otherArchive->uuid}", ['has_images' => false, 'record_fields' => []])->assertForbidden();
  $this->putJson("/api/tags/{$this->otherArchive->uuid}", ['tags' => []])->assertForbidden();
  $this->deleteJson("/api/tag/{$this->otherTag->uuid}")->assertForbidden();
  $this->deleteJson("/api/category/{$this->otherCategory->uuid}")->assertForbidden();
  $this->putJson("/api/record/{$this->otherRecord->uuid}", [])->assertForbidden();
  $this->deleteJson("/api/record/{$this->otherRecord->uuid}")->assertForbidden();
  $this->deleteJson("/api/archive/{$this->otherArchive->uuid}")->assertForbidden();

  expect(Archive::find($this->otherArchive->id))->not->toBeNull();
  expect(Record::find($this->otherRecord->id))->not->toBeNull();
  expect(Tag::find($this->otherTag->id))->not->toBeNull();
});

it('blocks creating a record in another tenant\'s archive', function () {
  Sanctum::actingAs($this->admin);

  $this->postJson('/api/record', [
    'archive_id' => $this->otherArchive->uuid,
    'category' => $this->otherCategory->uuid,
  ])->assertForbidden();
});

it('does not attach another archive\'s category or tags to a record', function () {
  Sanctum::actingAs($this->admin);

  // Category from another archive is not found in this archive
  $this->postJson('/api/record', [
    'archive_id' => $this->archive->uuid,
    'category' => $this->otherCategory->uuid,
  ])->assertNotFound();

  $this->postJson('/api/record', [
    'archive_id' => $this->archive->uuid,
    'category' => $this->category->uuid,
    'tags' => [$this->otherTag->uuid],
  ])->assertOk();

  $record = Record::where('archive_id', $this->archive->id)->latest('id')->first();
  expect($record->tags)->toHaveCount(0);
});

it('does not move a record into another archive on update', function () {
  Sanctum::actingAs($this->admin);
  $record = Record::create(['category_id' => $this->category->id, 'archive_id' => $this->archive->id]);

  $this->putJson("/api/record/{$record->uuid}", ['archive_id' => $this->otherArchive->uuid])->assertOk();

  expect($record->fresh()->archive_id)->toBe($this->archive->id);
});

it('uses per-archive permissions for members', function () {
  $viewer = addMember($this->archive, Role::findByName('Viewer', 'web'), $this->admin);
  $archivist = addMember($this->archive, Role::findByName('Archivist', 'web'), $this->admin, ['edit.card', 'delete.card']);
  $record = Record::create(['category_id' => $this->category->id, 'archive_id' => $this->archive->id]);

  Sanctum::actingAs($viewer);
  $this->getJson("/api/record/{$record->uuid}")->assertOk();
  $this->putJson("/api/record/{$record->uuid}", [])->assertForbidden();
  $this->deleteJson("/api/record/{$record->uuid}")->assertForbidden();
  $this->putJson("/api/tags/{$this->archive->uuid}", ['tags' => []])->assertForbidden();

  Sanctum::actingAs($archivist);
  $this->putJson("/api/record/{$record->uuid}", [])->assertOk();
  $this->getJson("/api/archive/users/{$this->archive->uuid}")->assertForbidden();
  $this->deleteJson("/api/record/{$record->uuid}")->assertOk();
});

it('does not apply a permission for one archive to another archive', function () {
  // Member of the other archive with edit.card there, but only viewer here
  $member = addMember($this->otherArchive, Role::findByName('Archivist', 'web'), $this->otherAdmin, ['edit.card']);
  $member->archives()->attach($this->archive->id, ['role_id' => Role::findByName('Viewer', 'web')->id]);
  $record = Record::create(['category_id' => $this->category->id, 'archive_id' => $this->archive->id]);

  Sanctum::actingAs($member->fresh());
  $this->putJson("/api/record/{$record->uuid}", [])->assertForbidden();
  $this->putJson("/api/record/{$this->otherRecord->uuid}", [])->assertOk();
});

it('lets a super admin access any archive', function () {
  $super = makeUser();
  $super->assignRole('Super Admin');
  Sanctum::actingAs($super);

  $this->getJson("/api/archive/{$this->otherArchive->uuid}")->assertOk();
  $this->getJson("/api/records/{$this->otherArchive->uuid}")->assertOk();
});

// ---------------------------------------------------------------------------
// Users, roles and permissions
// ---------------------------------------------------------------------------

it('blocks assigning a role on an archive the actor does not administer', function () {
  $target = makeUser();
  Sanctum::actingAs($this->admin);

  $this->putJson("/api/user/permissions/{$target->uuid}", [
    'archive' => $this->otherArchive->uuid,
    'role' => Role::findByName('Manager', 'web')->id,
    'permissions' => [],
  ])->assertForbidden();
});

it('never lets the API hand out the Admin or Super Admin role', function (string $roleName) {
  Sanctum::actingAs($this->admin);

  // Includes granting it to oneself
  $this->putJson("/api/user/permissions/{$this->admin->uuid}", [
    'archive' => $this->archive->uuid,
    'role' => Role::findByName($roleName, 'web')->id,
    'permissions' => [],
  ])->assertUnprocessable();

  expect($this->admin->fresh()->hasRole('Super Admin'))->toBeFalse();
})->with(['Admin', 'Super Admin']);

it('rejects unpublished permissions such as edit.users', function () {
  $target = makeUser();
  Sanctum::actingAs($this->admin);

  $this->putJson("/api/user/permissions/{$target->uuid}", [
    'archive' => $this->archive->uuid,
    'role' => Role::findByName('Manager', 'web')->id,
    'permissions' => [Permission::findByName('edit.users', 'web')->id],
  ])->assertUnprocessable();
});

it('lets an archive admin grant access to their own archive', function () {
  $target = makeUser();
  Sanctum::actingAs($this->admin);

  $this->putJson("/api/user/permissions/{$target->uuid}", [
    'archive' => $this->archive->uuid,
    'role' => Role::findByName('Archivist', 'web')->id,
    'permissions' => [Permission::findByName('edit.card', 'web')->id],
  ])->assertOk();

  $target = $target->fresh();
  expect($target->isMemberOf($this->archive))->toBeTrue();
  expect($target->hasArchivePermission($this->archive, 'edit.card'))->toBeTrue();
  expect($target->hasArchivePermission($this->otherArchive, 'edit.card'))->toBeFalse();
});

it('lists a user\'s role and permissions per archive the actor administers', function () {
  $viewer = Role::findByName('Viewer', 'web');
  $target = addMember($this->archive, $viewer, $this->admin, ['edit.card']);
  // A membership on another tenant's archive stays hidden
  $target->archives()->attach($this->otherArchive->id, ['role_id' => $viewer->id, 'added_by' => $this->otherAdmin->id, 'added_at' => now()]);
  // A member without any permission is still listed
  $bare = addMember($this->archive, $viewer, $this->admin);
  Sanctum::actingAs($this->admin);

  $this->getJson("/api/permissions/user/{$target->uuid}")
    ->assertOk()
    ->assertExactJson([
      $this->archive->uuid => [
        'role' => $viewer->id,
        'permissions' => [Permission::findByName('edit.card', 'web')->id],
      ],
    ]);

  $this->getJson("/api/permissions/user/{$bare->uuid}")
    ->assertOk()
    ->assertExactJson([$this->archive->uuid => ['role' => $viewer->id, 'permissions' => []]]);
});

it('rejects changing a user\'s email to one that is taken', function () {
  $member = addMember($this->archive, Role::findByName('Viewer', 'web'), $this->admin);
  Sanctum::actingAs($this->admin);

  $this->putJson("/api/archive/user/{$member->uuid}", ['firstname' => 'Renamed', 'name' => $member->name, 'email' => $member->email])
    ->assertOk();
  $deleted = makeUser(['email' => 'gone@example.test']);
  $deleted->delete();
  foreach ([$this->otherAdmin->email, 'gone@example.test'] as $email) {
    $this->putJson("/api/archive/user/{$member->uuid}", ['firstname' => 'Renamed', 'name' => $member->name, 'email' => $email])
      ->assertUnprocessable()
      ->assertJsonValidationErrors('email');
  }

  expect($member->fresh()->firstname)->toBe('Renamed');
});

it('blocks editing or deleting users of another tenant', function () {
  $foreign = addMember($this->otherArchive, Role::findByName('Viewer', 'web'), $this->otherAdmin);
  Sanctum::actingAs($this->admin);

  $this->getJson("/api/archive/user/{$foreign->uuid}")->assertForbidden();
  $this->putJson("/api/archive/user/{$foreign->uuid}", [
    'firstname' => 'Evil', 'name' => 'Evil', 'email' => 'attacker@example.test',
  ])->assertForbidden();
  $this->deleteJson("/api/archive/user/{$foreign->uuid}")->assertForbidden();
  $this->getJson("/api/permissions/user/{$foreign->uuid}")->assertForbidden();
  $this->postJson("/api/user/invite/{$foreign->uuid}", ['archive' => $this->otherArchive->uuid])->assertForbidden();

  expect($foreign->fresh()->email)->not->toBe('attacker@example.test');
  expect(User::find($foreign->id))->not->toBeNull();
});

it('blocks managing other account admins', function () {
  Sanctum::actingAs($this->admin);

  $this->putJson("/api/archive/user/{$this->otherAdmin->uuid}", [
    'firstname' => 'Evil', 'name' => 'Evil', 'email' => 'attacker@example.test',
  ])->assertForbidden();
  $this->deleteJson("/api/archive/user/{$this->otherAdmin->uuid}")->assertForbidden();
});

it('only lets admins update name and email of users they created', function () {
  $member = addMember($this->archive, Role::findByName('Viewer', 'web'), $this->admin);
  $originalPassword = $member->password;
  Sanctum::actingAs($this->admin);

  $this->putJson("/api/archive/user/{$member->uuid}", [
    'firstname' => 'New',
    'name' => 'Name',
    'email' => $member->email,
    'password' => 'hijacked-password',
    'email_verified_at' => null,
    'company_id' => 999,
  ])->assertOk();

  $member = $member->fresh();
  expect($member->firstname)->toBe('New');
  expect($member->password)->toBe($originalPassword);
  expect($member->email_verified_at)->not->toBeNull();
  expect($member->company_id)->toBeNull();
});

it('only removes a user from own archives when the actor did not create the account', function () {
  // Created by the other admin, but also member of our archive
  $shared = addMember($this->otherArchive, Role::findByName('Viewer', 'web'), $this->otherAdmin);
  $shared->archives()->attach($this->archive->id, ['role_id' => Role::findByName('Viewer', 'web')->id, 'added_by' => $this->admin->id]);
  grantArchivePermission($shared, $this->archive, 'edit.card');
  Sanctum::actingAs($this->admin);

  // Can't rename an account someone else created
  $this->putJson("/api/archive/user/{$shared->uuid}", [
    'firstname' => 'Evil', 'name' => 'Evil', 'email' => 'attacker@example.test',
  ])->assertForbidden();

  $this->deleteJson("/api/archive/user/{$shared->uuid}")->assertNoContent();

  $shared = User::find($shared->id);
  expect($shared)->not->toBeNull();
  expect($shared->isMemberOf($this->archive))->toBeFalse();
  expect($shared->isMemberOf($this->otherArchive))->toBeTrue();
  expect($shared->hasArchivePermission($this->archive, 'edit.card'))->toBeFalse();
});

it('does not overwrite an existing account when creating a user with its email', function () {
  $existing = addMember($this->otherArchive, Role::findByName('Viewer', 'web'), $this->otherAdmin);
  Sanctum::actingAs($this->admin);

  $this->postJson('/api/archive/user', [
    'firstname' => 'Overwritten',
    'name' => 'Overwritten',
    'email' => $existing->email,
  ])->assertOk();

  $existing = $existing->fresh();
  expect($existing->firstname)->not->toBe('Overwritten');
  expect((int) $existing->created_by)->toBe($this->otherAdmin->id);
});

it('ignores password and other non-profile fields on own profile update', function () {
  $originalPassword = $this->admin->password;
  Sanctum::actingAs($this->admin);

  $this->putJson('/api/user/profile', [
    'firstname' => 'Me',
    'name' => 'Myself',
    'email' => $this->admin->email,
    'password' => 'no-current-password-needed',
  ])->assertOk();

  expect($this->admin->fresh()->password)->toBe($originalPassword);
});

it('no longer exposes the /permissions debug route', function () {
  $this->get('/permissions')->assertNotFound();
});

// ---------------------------------------------------------------------------
// Images
// ---------------------------------------------------------------------------

it('blocks deleting or reordering another tenant\'s images', function () {
  Storage::fake('public');
  $dir = "archives/{$this->otherArchive->uuid}";
  Storage::disk('public')->put("$dir/photo_abc_resized.jpg", 'x');
  Storage::disk('public')->put("$dir/photo_abc_original.jpg", 'x');

  $media = new Media([
    'name' => 'photo', 'original_name' => 'photo_abc_original.jpg', 'resized_name' => 'photo_abc_resized.jpg',
    'resized_width' => 1, 'resized_height' => 1, 'aspect_ratio' => 'square', 'mime_type' => 'image/jpeg', 'size' => 1,
  ]);
  $media->mediable()->associate($this->otherRecord);
  $media->save();

  Sanctum::actingAs($this->admin);

  $this->deleteJson("/api/upload/storage/$dir/photo_abc_resized.jpg")->assertForbidden();
  $this->putJson('/api/upload/order', ['positions' => [['uuid' => $media->uuid, 'position' => 9]]])->assertForbidden();

  Storage::disk('public')->assertExists("$dir/photo_abc_resized.jpg");
  expect(Media::find($media->id))->not->toBeNull();
  expect((int) $media->fresh()->position)->not->toBe(9);
});

it('lets editors delete images of their own records', function () {
  Storage::fake('public');
  $record = Record::create(['category_id' => $this->category->id, 'archive_id' => $this->archive->id]);
  $dir = "archives/{$this->archive->uuid}";
  Storage::disk('public')->put("$dir/photo_abc_resized.jpg", 'x');
  Storage::disk('public')->put("$dir/photo_abc_original.jpg", 'x');

  $media = new Media([
    'name' => 'photo', 'original_name' => 'photo_abc_original.jpg', 'resized_name' => 'photo_abc_resized.jpg',
    'resized_width' => 1, 'resized_height' => 1, 'aspect_ratio' => 'square', 'mime_type' => 'image/jpeg', 'size' => 1,
  ]);
  $media->mediable()->associate($record);
  $media->save();

  Sanctum::actingAs($this->admin);
  $this->deleteJson("/api/upload/storage/$dir/photo_abc_resized.jpg")->assertOk();

  Storage::disk('public')->assertMissing("$dir/photo_abc_resized.jpg");
  Storage::disk('public')->assertMissing("$dir/photo_abc_original.jpg");
  expect(Media::find($media->id))->toBeNull();
});

it('refuses to move files with path traversal in the name', function () {
  Storage::fake('public');
  Storage::disk('public')->put("archives/{$this->otherArchive->uuid}/secret_resized.jpg", 'x');

  $moved = (new \App\Actions\Image\Move())->execute(
    "../archives/{$this->otherArchive->uuid}/secret_resized.jpg",
    $this->archive->uuid
  );

  expect($moved)->toBeFalse();
  Storage::disk('public')->assertExists("archives/{$this->otherArchive->uuid}/secret_resized.jpg");
});

it('returns per-archive abilities that match the policy', function () {
  $archivist = addMember($this->archive, Role::findByName('Archivist', 'web'), $this->admin, ['edit.card']);

  Sanctum::actingAs($archivist);
  $abilities = $this->getJson('/api/user/permissions')->assertOk()->json('archives');
  expect($abilities)->toHaveKey($this->archive->uuid)->not->toHaveKey($this->otherArchive->uuid);
  expect($abilities[$this->archive->uuid])->toBe(['view', 'updateRecord']);

  Sanctum::actingAs($this->admin);
  $abilities = $this->getJson('/api/user/permissions')->json('archives');
  expect($abilities[$this->archive->uuid])->toBe(\App\Actions\UserPermission\ArchiveAbilities::ABILITIES);
});

// ---------------------------------------------------------------------------
// Favorites and per-archive roles
// ---------------------------------------------------------------------------

it('toggles favorites only with POST and only for readable records', function () {
  $record = Record::create(['category_id' => $this->category->id, 'archive_id' => $this->archive->id]);
  Sanctum::actingAs($this->admin);

  $this->getJson("/api/favorite/toggle/{$record->uuid}")->assertMethodNotAllowed();
  expect($this->admin->favorites()->count())->toBe(0);

  $this->postJson("/api/favorite/toggle/{$record->uuid}")->assertNoContent();
  expect($this->admin->favorites()->count())->toBe(1);

  $this->postJson("/api/favorite/toggle/{$record->uuid}")->assertNoContent();
  expect($this->admin->favorites()->count())->toBe(0);

  $this->postJson("/api/favorite/toggle/{$this->otherRecord->uuid}")->assertForbidden();
});

function archiveRoleNames(User $user, Archive $archive): array
{
  return $user->roles()->wherePivot('archive_id', $archive->id)->pluck('name')->all();
}

it('changes a role on one archive without touching the user\'s other archives', function () {
  $secondArchive = Archive::create(['name' => 'Zweite', 'acronym' => 'ZW']);
  $this->admin->archives()->attach($secondArchive->id);
  $target = makeUser();
  Sanctum::actingAs($this->admin);

  $grant = fn (Archive $archive, string $role) => $this->putJson(
    "/api/user/permissions/{$target->uuid}",
    ['archive' => $archive->uuid, 'role' => Role::findByName($role, 'web')->id, 'permissions' => []]
  )->assertOk();

  // The same role on two archives
  $grant($this->archive, 'Viewer');
  $grant($secondArchive, 'Viewer');
  expect(archiveRoleNames($target, $this->archive))->toBe(['Viewer']);
  expect(archiveRoleNames($target, $secondArchive))->toBe(['Viewer']);

  // Changing it on one archive leaves the other alone
  $grant($this->archive, 'Manager');
  expect(archiveRoleNames($target, $this->archive))->toBe(['Manager']);
  expect(archiveRoleNames($target, $secondArchive))->toBe(['Viewer']);

  // The archive user list shows the role for that archive
  $this->getJson("/api/archive/users/{$secondArchive->uuid}")
    ->assertOk()
    ->assertJsonFragment(['uuid' => $target->uuid, 'role' => __('roles.Viewer')]);
});
