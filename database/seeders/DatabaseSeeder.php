<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use \App\Models\User;
use \App\Models\Archive;
use \App\Models\Company;

class DatabaseSeeder extends Seeder
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

    // create an archive
    Archive::create([
      'title' => 'Sammlung Bert Fiefelstein',
      'acronym' => 'BF'
    ]);

    // add another archive
    Archive::create([
      'title' => 'Bildarchiv Kretschmann',
      'acronym' => 'BK'
    ]);

    // create a company
    Company::create([
      'name' => 'WBG AG',
      'street' => 'Binzstrasse',
      'street_number' => '39',
      'zip' => '8045',
      'city' => 'Zürich',
    ]);
  }
}
