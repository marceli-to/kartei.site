<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register and become account admins', function () {
    Role::create(['name' => 'Admin', 'guard_name' => 'web']);

    $response = $this->post('/register', [
        'firstname' => 'Test',
        'name' => 'User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(config('app.home'));

    $user = User::where('email', 'test@example.com')->first();
    expect($user->firstname)->toBe('Test');
    expect($user->hasRole('Admin'))->toBeTrue();
});

test('registration requires a first name', function () {
    $this->post('/register', [
        'name' => 'User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('firstname');

    $this->assertGuest();
});
