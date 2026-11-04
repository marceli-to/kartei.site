<?php

use App\Models\User;

it('sends guests from the home page to the login', function () {
  $this->get('/')->assertRedirect(route('auth.login'));
});

it('sends logged-in users from the home page into the app', function () {
  $user = User::create([
    'firstname' => 'Test',
    'name' => 'User',
    'email' => 'home@example.test',
    'password' => 'secret-password',
    'email_verified_at' => now(),
  ]);

  $this->actingAs($user)->get('/')->assertRedirect(config('app.home'));
});
