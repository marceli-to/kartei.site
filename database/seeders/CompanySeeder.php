<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use \App\Models\Company;

class CompanySeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
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
