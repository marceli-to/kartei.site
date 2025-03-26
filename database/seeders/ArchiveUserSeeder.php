<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use \App\Models\Archive;
use \App\Models\User;
use Spatie\Permission\Models\Role;

class ArchiveUserSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    $users = User::all();
    $archives = Archive::all();

    // Add user to all archives
    foreach ($users as $user) {
      $user->archives()->attach($archives);
    }
    // Create a user with role 'Viewer'
    // $viewer = User::create([
    //   'firstname' => 'Gabi',
    //   'name' => 'Morf',
    //   'email' => 'gabi.morf@gmail.com',
    //   'password' => \Hash::make('7aq31rr23'),
    //   'email_verified_at' => '2025-01-22 18:11:24'
    // ]);

    // // Add role 'Viewer'
    // $viewer->roles()->attach(Role::where('name', 'Viewer')->first());

    // // Add viewer to all archives
    // $viewer->archives()->attach($archives, ['role_id' => Role::where('name', 'Viewer')->first()->id, 'added_by' => $user->id, 'added_at' => now()]);

    // // Create a user with role 'Archivist'
    // $archivist = User::create([
    //   'firstname' => 'Peter',
    //   'name' => 'Muster',
    //   'email' => 'editor@kartei.app',
    //   'password' => \Hash::make('7aq31rr23'),
    //   'email_verified_at' => '2025-01-22 18:11:24'
    // ]);

    // // Add role 'Archivist'
    // $archivist->roles()->attach(Role::where('name', 'Archivist')->first());

    // // Add editor to all archives
    // $archivist->archives()->attach($archives, ['role_id' => Role::where('name', 'Archivist')->first()->id, 'added_by' => $user->id, 'added_at' => now()]);

    // // Create another user with role 'Viewer'
    // $viewer2 = User::create([
    //   'firstname' => 'Lena',
    //   'name' => 'Muster',
    //   'email' => 'viewer2@kartei.app',
    //   'password' => \Hash::make('7aq31rr23'),
    //   'email_verified_at' => '2025-01-22 18:11:24'
    // ]);

    // // Add role 'Viewer'
    // $viewer2->roles()->attach(Role::where('name', 'Viewer')->first());

    // // Add viewer2 to all archives
    // $viewer2->archives()->attach($archives, ['role_id' => Role::where('name', 'Viewer')->first()->id, 'added_by' => $user->id, 'added_at' => now()]);
  }
}