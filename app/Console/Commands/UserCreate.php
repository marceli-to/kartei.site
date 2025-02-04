<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Actions\User\Create as CreateUserAction;

class UserCreate extends Command
{
  protected $signature = 'user:create';

  protected $description = 'Test the user creation action';

  public function handle()
  {
    $faker = Faker::create();
    $data = [
      'firstname' => $this->ask('Enter firstname') ?? $faker->firstName,
      'name' => $this->ask('Enter lastname') ?? $faker->lastName,
      'email' => $this->ask('Enter email') ?? $faker->unique()->safeEmail,
      'password' => Str::random(10),
      'email_verified_at' => now()->toDateTimeString(),
    ];

    $user = (new CreateUserAction())->execute($data);
    $this->info('User created: ' . $user->email);
  }
}