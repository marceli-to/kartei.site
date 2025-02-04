<?php
namespace App\Console\Commands;
use App\Actions\User\Delete as DeleteUserAction;
use App\Models\User;
use Illuminate\Console\Command;

class UserDelete extends Command
{
  protected $signature = 'user:delete';

  protected $description = 'Test the user delete action';

  public function handle()
  {
    $users = User::all();
    $email = $this->choice('Which user do you want to delete?', $users->pluck('email')->toArray());
    $user = User::where('email', $email)->first();
    (new DeleteUserAction())->execute($user);
    $this->info('User deleted: ' . $user->email);
  }
}