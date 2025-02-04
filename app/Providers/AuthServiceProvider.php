<?php
namespace App\Providers;
// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Archive;
use App\Policies\ArchivePolicy;
// use App\Models\Record;
// use App\Policies\RecordPolicy;

class AuthServiceProvider extends ServiceProvider
{
  /**
   * The model to policy mappings for the application.
   *
   * @var array<class-string, class-string>
   */
  protected $policies = [
    Archive::class => ArchivePolicy::class,
    //Record::class => RecordPolicy::class,
  ];

  /**
   * Register any authentication / authorization services.
   */
  public function boot(): void
  {
    //
  }
}
