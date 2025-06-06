<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Database\Seeders\ArchiveSeeder;
use Database\Seeders\CompanySeeder;
use Database\Seeders\CompanyUserSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\SubscriptionPlanSeeder;
use Database\Seeders\AddressSeeder;
use Database\Seeders\ArchiveUserSeeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    $this->call([
      UserSeeder::class,
      ArchiveSeeder::class,
      CompanySeeder::class,
      CompanyUserSeeder::class,
      PermissionSeeder::class,
      SubscriptionPlanSeeder::class,
      AddressSeeder::class,
      ArchiveUserSeeder::class
    ]);

  }
}
