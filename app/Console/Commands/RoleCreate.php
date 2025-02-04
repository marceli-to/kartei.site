<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Actions\Role\Create as CreateRoleAction;

class RoleCreate extends Command
{
  protected $signature = 'role:create';

  protected $description = 'Test the role creation action';

  public function handle()
  {
    $data = [
      'name' => $this->ask('Enter name'),
      'guard_name' => $this->ask('Enter guard name') ?? 'web'
    ];

    $role = (new CreateRoleAction())->execute($data);
    $this->info('Role created: ' . $role->name);
  }
}