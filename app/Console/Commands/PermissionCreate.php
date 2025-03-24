<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Actions\Permission\Create as CreatePermissionAction;

class PermissionCreate extends Command
{
  protected $signature = 'permission:create';

  protected $description = 'Test the permission creation action';

  public function handle()
  {
    $data = [
      'name' => $this->ask('Enter name'),
      'guard_name' => $this->ask('Enter guard name') ?? 'web'
    ];

    $permission = (new CreatePermissionAction())->execute($data);
    $this->info('Permission created: ' . $permission->name);
  }
}