<?php

use App\Models\Address;
use App\Models\Archive;
use App\Models\Category;
use App\Models\Company;
use App\Models\Favorite;
use App\Models\Record;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

function bugfixUser(): User
{
  $user = User::create([
    'firstname' => 'Test',
    'name' => Str::random(6),
    'email' => strtolower(Str::random(10)) . '@example.test',
    'password' => 'secret-password',
    'email_verified_at' => now(),
  ]);
  $user->assignRole('Admin');
  return $user;
}

function subscribe(User $user, SubscriptionPlan $plan, array $attributes = []): UserSubscription
{
  return UserSubscription::create(array_merge([
    'user_id' => $user->id,
    'subscription_plan_id' => $plan->id,
    'payment_interval' => 'monthly',
    'payment_method' => 'card',
    'starts_at' => now()->subMonth(),
  ], $attributes));
}

beforeEach(function () {
  app(PermissionRegistrar::class)->forgetCachedPermissions();
  Role::create(['name' => 'Admin', 'guard_name' => 'web']);
  Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
  $this->plan = SubscriptionPlan::create(['title' => 'Basic', 'max_users' => 1, 'rate_monthly' => 10, 'rate_yearly' => 100]);
});

it('returns the user\'s own subscription, not the first one in the table', function () {
  $first = bugfixUser();
  subscribe($first, $this->plan, ['payment_interval' => 'yearly']);
  $second = bugfixUser();
  $own = subscribe($second, $this->plan, ['payment_interval' => 'monthly']);

  Sanctum::actingAs($second);
  $this->getJson('/api/user/subscription')
    ->assertOk()
    ->assertJsonPath('data.uuid', $own->uuid);
});

it('treats users without subscription as inactive and compares dates as dates', function () {
  $user = bugfixUser();
  expect($user->hasActiveSubscription())->toBeFalse();

  subscribe($user, $this->plan, ['ends_at' => now()->subDay()]);
  expect($user->fresh()->hasActiveSubscription())->toBeFalse();

  $user->fresh()->subscription->update(['ends_at' => now()->addYear()]);
  expect($user->fresh()->hasActiveSubscription())->toBeTrue();
  expect($user->fresh()->subscription->ends_at)->toBeInstanceOf(\Carbon\CarbonInterface::class);
});

it('does not list deleted records as favorites', function () {
  $user = bugfixUser();
  $archive = Archive::create(['name' => 'Kartei', 'acronym' => 'KA']);
  $category = Category::create(['number' => '1', 'name' => 'Cat', 'custom_id' => 'CA', 'archive_id' => $archive->id]);
  $kept = Record::create(['category_id' => $category->id, 'archive_id' => $archive->id]);
  $deleted = Record::create(['category_id' => $category->id, 'archive_id' => $archive->id]);
  Favorite::create(['user_id' => $user->id, 'record_id' => $kept->id]);
  Favorite::create(['user_id' => $user->id, 'record_id' => $deleted->id]);
  $deleted->delete();

  expect($user->getFavorites()->all())->toBe([$kept->uuid]);
});

it('lets users create and update their address and billing address', function () {
  $user = bugfixUser();
  Sanctum::actingAs($user);

  $address = ['street' => 'Weg', 'street_number' => '1', 'zip' => '8000', 'city' => 'Zürich', 'country' => 'Schweiz'];

  $this->putJson('/api/user/address', $address)->assertSuccessful()->assertJsonPath('data.city', 'Zürich');
  $this->putJson('/api/user/billing-address', ['city' => 'Bern'] + $address)->assertSuccessful()->assertJsonPath('data.city', 'Bern');
  $this->putJson('/api/user/billing-address', ['city' => 'Basel'] + $address)->assertSuccessful()->assertJsonPath('data.city', 'Basel');

  expect(Address::where('addressable_id', $user->id)->count())->toBe(2);
  expect($user->fresh()->address->city)->toBe('Zürich');
  expect($user->fresh()->billingAddress->city)->toBe('Basel');
});

it('lets company members update their company, but not other companies', function () {
  $user = bugfixUser();
  $company = Company::create(['name' => 'Own', 'street' => 'Weg', 'zip' => '8000', 'city' => 'Zürich', 'country' => 'CH']);
  $user->companies()->attach($company->id);
  $other = Company::create(['name' => 'Other', 'street' => 'Weg', 'zip' => '8000', 'city' => 'Zürich', 'country' => 'CH']);

  Sanctum::actingAs($user);
  $payload = ['name' => 'Renamed', 'street' => 'Weg', 'zip' => '8000', 'city' => 'Zürich', 'country' => 'CH'];

  $this->putJson("/api/user/company/{$company->uuid}", $payload)->assertOk();
  $this->putJson("/api/user/company/{$other->uuid}", $payload)->assertForbidden();

  expect($company->fresh()->name)->toBe('Renamed');
  expect($other->fresh()->name)->toBe('Other');
});

it('keeps archive template fields when settings are saved without them', function () {
  $user = bugfixUser();
  $archive = Archive::create(['name' => 'Kartei', 'acronym' => 'KA']);
  $user->archives()->attach($archive->id);
  Sanctum::actingAs($user);

  $this->putJson("/api/archive/settings/{$archive->uuid}", [
    'has_images' => true,
    'record_fields' => [['placeholder' => 'Titel', 'order' => 0]],
  ])->assertOk();

  $this->putJson("/api/archive/settings/{$archive->uuid}", ['has_images' => false])->assertOk();

  $settings = $archive->fresh()->settings;
  expect($settings['has_images'])->toBeFalse();
  expect($settings['record_fields'])->toHaveCount(1);
});

it('accepts image uploads up to the configured limit', function () {
  \Illuminate\Support\Facades\Storage::fake('public');
  Sanctum::actingAs(bugfixUser());
  $max = config('media.max_upload_mb');
  expect($max)->toBeGreaterThanOrEqual(32);

  $this->postJson('/api/upload', [
    'files' => [\Illuminate\Http\UploadedFile::fake()->image('big.jpg', 800, 600)->size(($max - 1) * 1024)],
  ])->assertOk()->assertJsonCount(1, 'files');

  $this->postJson('/api/upload', [
    'files' => [\Illuminate\Http\UploadedFile::fake()->image('too-big.jpg', 800, 600)->size(($max + 1) * 1024)],
  ])->assertUnprocessable()->assertJsonValidationErrors('files.0');
});

it('rejects an unknown payment interval with a validation error', function () {
  Sanctum::actingAs(bugfixUser());
  $this->putJson('/api/user/subscription', [
    'subscription' => $this->plan->uuid,
    'payment_interval' => 'weekly',
    'payment_method' => 'card',
  ])->assertUnprocessable()->assertJsonValidationErrors('payment_interval');
});
