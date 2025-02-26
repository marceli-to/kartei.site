<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Address;
use App\Models\User;
use Illuminate\Support\Str;

class AddressSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    Address::create([
      'uuid' => Str::uuid(),
      'street' => 'Letzigraben',
      'street_number' => '149',
      'zip' => '8047',
      'city' => 'Zürich',
      'country' => 'Schweiz',
      'addressable_id' => 1,
      'addressable_type' => User::class,
      'is_billing' => false
    ]);

    Address::create([
      'uuid' => Str::uuid(),
      'company' => 'marceli.to',
      'street' => 'Binzstrasse',
      'street_number' => '39',
      'zip' => '8045',
      'city' => 'Zürich',
      'country' => 'Schweiz',
      'addressable_id' => 1,
      'addressable_type' => User::class,
      'is_billing' => true
    ]);
  }
}
