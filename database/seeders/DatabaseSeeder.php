<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use \App\Models\User;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    // add a user with the following data:
    // firstname: Marcel
    // name: Stadelmann
    // email: m@marcel.to
    // password: 7aq31rr23 (hashed)
    // email_verified_at: 2025-01-22 18:11:24

    User::create([
      'uuid' => \Str::uuid(),
      'firstname' => 'Marcel',
      'name' => 'Stadelmann',
      'email' => 'm@marcel.to',
      'password' => \Hash::make('7aq31rr23'),
      'email_verified_at' => '2025-01-22 18:11:24'
    ]);
  }
}
