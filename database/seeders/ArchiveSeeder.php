<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use \App\Models\Archive;
use \App\Models\User;

class ArchiveSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    $archives = [
      [
        'slug' => Str::slug('Sammlung Bert Fiefelstein'),
        'title' => 'Sammlung Bert Fiefelstein',
        'acronym' => 'BF'
      ],
      [
        'slug' => Str::slug('Bildarchiv Kretschmann'),
        'title' => 'Bildarchiv Kretschmann',
        'acronym' => 'BK'
      ]
    ];

    foreach ($archives as $archive)
    {
      Archive::create($archive);
    }
  }
}
