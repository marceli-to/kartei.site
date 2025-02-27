<?php
namespace App\Providers;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use App\Models\Archive;
use App\Policies\ArchivePolicy;

use App\Models\Address;
use App\Policies\AddressPolicy;

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
    Address::class => AddressPolicy::class,
  ];

  /**
   * Register any authentication / authorization services.
   */
  public function boot(): void
  {
    // Implicitly grant "Super Admin" role all permissions
    Gate::before(function ($user, $ability) {
      return $user->hasRole('Super Admin') ? true : null;
    });
  }
}
