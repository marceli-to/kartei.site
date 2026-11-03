<?php

use App\Models\Archive;
use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/*
 * Behaviour that used to live in Http/Kernel, RouteServiceProvider,
 * Authenticate/RedirectIfAuthenticated and Exceptions/Handler and now
 * lives in bootstrap/app.php and AppServiceProvider.
 */

function structureUser(): User
{
  return User::create([
    'firstname' => 'Test', 'name' => 'User', 'email' => strtolower(Str::random(10)) . '@example.test',
    'password' => 'secret-password', 'email_verified_at' => now(),
  ]);
}

beforeEach(function () {
  app(PermissionRegistrar::class)->forgetCachedPermissions();
  Role::create(['name' => 'Admin', 'guard_name' => 'web']);
  Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
});

it('redirects guests to the auth.login route', function () {
  $this->get('/archiv')->assertRedirect(route('auth.login'));
});

it('redirects logged-in users away from guest pages to the home path', function () {
  $this->actingAs(structureUser())->get('/login')->assertRedirect('/archiv/meine-karteien');
});

it('sends users home after login', function () {
  $user = structureUser();
  $this->post('/login', ['email' => $user->email, 'password' => 'secret-password'])
    ->assertRedirect(config('app.home'));
});

it('returns the JSON message for authorization failures', function () {
  $archive = Archive::create(['name' => 'Kartei', 'acronym' => 'KA']);
  Sanctum::actingAs(structureUser());

  $this->getJson("/api/archive/{$archive->uuid}")
    ->assertForbidden()
    ->assertExactJson(['message' => 'You are not authorized to perform this action.']);
});

it('throttles the API with the 600/minute limiter', function () {
  Sanctum::actingAs(structureUser());
  $this->getJson('/api/user/permissions')->assertOk()->assertHeader('X-RateLimit-Limit', 600);
});

it('requires authentication on the API', function () {
  $this->getJson('/api/user/permissions')->assertUnauthorized();
});

it('applies policies and the Super Admin gate', function () {
  $archive = Archive::create(['name' => 'Kartei', 'acronym' => 'KA']);
  $super = structureUser();
  $super->assignRole('Super Admin');
  Sanctum::actingAs($super);

  $this->getJson("/api/archive/{$archive->uuid}")->assertOk();
});

it('schedules the temp upload cleanup', function () {
  $this->artisan('schedule:list')->expectsOutputToContain('media:purge-temp')->assertSuccessful();
});

it('exposes the health check', function () {
  $this->get('/up')->assertOk();
});
