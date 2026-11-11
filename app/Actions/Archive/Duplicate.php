<?php
namespace App\Actions\Archive;
use App\Models\Archive;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class Duplicate
{
  /**
   * Copy an archive's setup into a new archive named "<name> (Kopie)" ("(Kopie 2)", … when taken):
   * basic information, structure (categories and registers), card template, tags and members with
   * their per-archive permissions. Records and the title image are not copied.
   */
  public function execute(Archive $source, User $user): Archive
  {
    $copy = DB::transaction(function () use ($source, $user) {
      $copy = Archive::create([
        'name' => $this->copyName($source->name, $user),
        'acronym' => $source->acronym,
        'company_id' => $source->company_id,
        'settings' => $this->settings($source->settings),
      ]);

      $this->copyStructure($source, $copy);
      $this->copyTags($source, $copy);
      $this->copyMembers($source, $copy, $user);
      $this->copyPermissions($source, $copy);

      return $copy;
    });

    app(PermissionRegistrar::class)->forgetCachedPermissions();
    $user->flushArchiveMemberships();

    return $copy;
  }

  /**
   * "Name (Kopie)", or the next free "Name (Kopie N)" among the user's archives.
   * A copy of a copy counts on from the original name.
   */
  private function copyName(string $name, User $user): string
  {
    $base = preg_replace('/ \(Kopie(?: \d+)?\)$/u', '', $name);
    $taken = $user->archives()->pluck('name')->all();

    $candidate = "{$base} (Kopie)";
    for ($n = 2; in_array($candidate, $taken, true); $n++) {
      $candidate = "{$base} (Kopie {$n})";
    }
    return $candidate;
  }

  // Card template: same fields with new uuids (the uuids belong to one archive)
  private function settings(?array $settings): array
  {
    $settings ??= ['has_images' => true, 'record_fields' => []];
    $settings['record_fields'] = collect($settings['record_fields'] ?? [])
      ->map(fn (array $field) => array_merge($field, ['uuid' => Str::uuid()->toString()]))
      ->values()
      ->all();
    return $settings;
  }

  // Categories first, then their registers pointing at the copied categories
  private function copyStructure(Archive $source, Archive $copy): void
  {
    $all = Category::where('archive_id', $source->id)->orderBy('order')->orderBy('id')->get();
    $newIds = [];

    foreach ($all->whereNull('parent_id') as $category) {
      $newIds[$category->id] = $this->replicate($category, ['archive_id' => $copy->id])->id;
    }

    foreach ($all->whereNotNull('parent_id') as $register) {
      if (!isset($newIds[$register->parent_id])) continue;
      $this->replicate($register, ['archive_id' => $copy->id, 'parent_id' => $newIds[$register->parent_id]]);
    }
  }

  private function copyTags(Archive $source, Archive $copy): void
  {
    Tag::where('archive_id', $source->id)->get()
      ->each(fn (Tag $tag) => $this->replicate($tag, ['archive_id' => $copy->id]));
  }

  // Same members with the same roles, added now by the user who duplicated
  private function copyMembers(Archive $source, Archive $copy, User $user): void
  {
    $rows = DB::table('archive_user')->where('archive_id', $source->id)->get()
      ->map(fn ($row) => [
        'archive_id' => $copy->id,
        'user_id' => $row->user_id,
        'role_id' => $row->role_id,
        'added_by' => $row->role_id ? $user->id : null,
        'added_at' => $row->role_id ? now() : null,
      ]);

    DB::table('archive_user')->insert($rows->all());
  }

  // "<permission>.<source id>" becomes "<permission>.<copy id>", held by the same users
  private function copyPermissions(Archive $source, Archive $copy): void
  {
    $pivot = config('permission.table_names.model_has_permissions');
    $key = config('permission.column_names.permission_pivot_key') ?? 'permission_id';

    foreach (Permission::where('archive_id', $source->id)->get() as $permission) {
      $name = preg_replace('/\.' . $source->id . '$/', '.' . $copy->id, $permission->name);
      $new = $permission->replicate();
      $new->name = $name;
      $new->archive_id = $copy->id;
      $new->save();

      $holders = DB::table($pivot)->where($key, $permission->id)->get()
        ->map(fn ($row) => array_merge((array) $row, [$key => $new->id]));
      DB::table($pivot)->insert($holders->all());
    }
  }

  private function replicate($model, array $attributes)
  {
    $copy = $model->replicate(['uuid']);
    $copy->forceFill($attributes);
    $copy->save();
    return $copy;
  }
}
