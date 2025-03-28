<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use \App\Models\User;

class UserSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    User::create([
      'firstname' => 'Marcel',
      'name' => 'Stadelmann',
      'email' => 'm@marceli.to',
      'password' => \Hash::make('7aq31rr23'),
      'email_verified_at' => '2025-01-22 18:11:24'
    ]);

    User::create([
      'firstname' => 'Benedikt',
      'name' => 'Flüeler',
      'email' => 'bf@wbg.ch',
      'password' => \Hash::make('9xv42tt45'),
      'email_verified_at' => '2025-01-22 18:11:24'
    ]);
  }
}
