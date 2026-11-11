<?php

use App\Models\Archive;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

// Tags are sorted by drag + drop in the settings (Stackr #127): the list order is saved
beforeEach(function () {
  app(PermissionRegistrar::class)->forgetCachedPermissions();
  Role::create(['name' => 'Admin', 'guard_name' => 'web']);

  $this->archive = Archive::create(['name' => 'Kartei', 'acronym' => 'KA']);
  $this->admin = User::create([
    'firstname' => 'Test', 'name' => 'Admin', 'email' => strtolower(Str::random(10)) . '@example.test',
    'password' => 'secret-password', 'email_verified_at' => now(),
  ]);
  $this->admin->assignRole('Admin');
  $this->admin->archives()->attach($this->archive->id);
  Sanctum::actingAs($this->admin);
});

it('saves and returns tags in the order they were sent', function () {
  $a = Tag::create(['name' => 'A', 'archive_id' => $this->archive->id]);
  $b = Tag::create(['name' => 'B', 'archive_id' => $this->archive->id]);

  $this->putJson("/api/tags/{$this->archive->uuid}", ['tags' => [
    ['uuid' => null, 'name' => 'C'],
    ['uuid' => $b->uuid, 'name' => 'B'],
    ['uuid' => $a->uuid, 'name' => 'A'],
  ]])
    ->assertOk()
    ->assertJsonPath('data.*.name', ['C', 'B', 'A']);

  $this->getJson("/api/tags/{$this->archive->uuid}")
    ->assertOk()
    ->assertJsonPath('data.*.name', ['C', 'B', 'A']);
});
