<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Faker\Factory as Faker;
use App\Actions\Archive\Create as CreateArchiveAction;

class ArchiveCreate extends Command
{
  protected $signature = 'archive:create';

  protected $description = 'Test the archive creation action';

  public function handle()
  {
    $faker = Faker::create();
    $data = [
      'title' => $this->ask('Enter title') ?? $faker->company,
      'acronym' => $this->ask('Enter acronym') ?? $faker->unique()->word,
      'media_id' => $this->ask('Enter media id') ?? null,
      'company_id' => $this->ask('Enter company id') ?? null
    ];

    $archive = (new CreateArchiveAction())->execute($data);
    $this->info('Archive created: ' . $archive->title);
  }
}