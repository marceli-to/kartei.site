<?php
namespace App\Actions\ArchiveStructure;

use App\Models\Archive;
use App\Models\ArchiveStructure;
use Illuminate\Support\Facades\DB;

class UpdateOrCreate
{
  public function execute(array $structure, Archive $archive)
  {
    return DB::transaction(function () use ($structure, $archive) {
      foreach ($structure as $categoryData) {
        $category = $this->updateOrCreateCategory($categoryData, $archive);

        foreach ($categoryData['registers'] ?? [] as $registerData) {
          $this->updateOrCreateRegister($registerData, $archive, $category);
        }
      }

      return ArchiveStructure::where('archive_id', $archive->id)
        ->orderBy('order')
        ->get();
    });
  }

  private function updateOrCreateCategory(array $data, Archive $archive): ArchiveStructure
  {
    $category = null;

    if (!empty($data['uuid'])) {
      $category = ArchiveStructure::where('archive_id', $archive->id)
        ->where('uuid', $data['uuid'])
        ->first();
    }

    if ($category) {
      if (
        $category->number !== $data['number'] ||
        $category->title !== $data['name'] ||
        $category->custom_id !== $data['custom_id'] ||
        $category->order !== $data['order']
      ) {
        $category->update([
          'number' => $data['number'],
          'name' => $data['name'],
          'custom_id' => $data['custom_id'],
          'numeral_type' => $data['numeral_type'],
          'custom_id_type' => $data['custom_id_type'],
          'order' => $data['order'],
        ]);
      }
    } 
    else {
      $category = ArchiveStructure::create([
        'archive_id' => $archive->id,
        'number' => $data['number'],
        'name' => $data['name'],
        'custom_id' => $data['custom_id'],
        'numeral_type' => $data['numeral_type'],
        'custom_id_type' => $data['custom_id_type'],
        'order' => $data['order'],
      ]);
    }
    return $category;
  }

  private function updateOrCreateRegister(array $data, Archive $archive, ArchiveStructure $category): ArchiveStructure
  {
    $register = null;

    if (!empty($data['uuid'])) {
      $register = ArchiveStructure::where('archive_id', $archive->id)
        ->where('uuid', $data['uuid'])
        ->first();
    }

    if ($register) {
      if (
        $register->number !== $data['number'] ||
        $register->title !== $data['name'] ||
        $register->custom_id !== $data['custom_id'] ||
        $register->order !== $data['order'] ||
        $register->parent_id !== $category->id
      ) {
        $register->update([
          'name' => $data['name'],
          'number' => $data['number'],
          'custom_id' => $data['custom_id'],
          'numeral_type' => $data['numeral_type'],
          'custom_id_type' => $data['custom_id_type'],
          'order' => $data['order'],
          'parent_id' => $category->id,
        ]);
      }
    } 
    else {
      $register = ArchiveStructure::create([
        'archive_id' => $archive->id,
        'number' => $data['number'],
        'name' => $data['name'],
        'custom_id' => $data['custom_id'],
        'numeral_type' => $data['numeral_type'],
        'custom_id_type' => $data['custom_id_type'],
        'order' => $data['order'],
        'parent_id' => $category->id,
      ]);
    }

    return $register;
  }
}
