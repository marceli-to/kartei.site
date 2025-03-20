<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use \App\Models\Company;
use \App\Models\User;

class CompanyUserSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    $user = User::first();
    $companies = Company::all();
    $user->companies()->attach($companies);
  }
}
