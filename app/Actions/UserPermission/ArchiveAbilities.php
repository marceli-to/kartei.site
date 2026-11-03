<?php
namespace App\Actions\UserPermission;
use App\Models\Archive;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class ArchiveAbilities
{
  /**
   * ArchivePolicy abilities the SPA uses to show or hide archive actions.
   */
  public const ABILITIES = [
    'view',
    'update',
    'delete',
    'manageStructure',
    'manageTemplate',
    'manageTags',
    'manageUsers',
    'createRecord',
    'updateRecord',
    'deleteRecord',
  ];

  /**
   * The user's abilities per archive, keyed by archive uuid, evaluated
   * through ArchivePolicy so the UI matches what the API allows.
   *
   * @return array<string, array<int, string>>
   */
  public function execute(User $user): array
  {
    $gate = Gate::forUser($user);

    return $user->archives()->get()
      ->mapWithKeys(fn (Archive $archive) => [
        $archive->uuid => array_values(array_filter(
          self::ABILITIES,
          fn (string $ability) => $gate->allows($ability, $archive)
        )),
      ])
      ->all();
  }
}
