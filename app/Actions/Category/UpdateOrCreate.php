<?php
namespace App\Actions\Category;
use App\Models\Archive;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class UpdateOrCreate
{
  public function execute(array $categories, Archive $archive)
  {
    return DB::transaction(function () use ($categories, $archive) {
      foreach ($categories as $categoryData) {
        $category = $this->updateOrCreateCategory($categoryData, $archive);

        foreach ($categoryData['registers'] ?? [] as $registerData) {
          $this->updateOrCreateRegister($registerData, $archive, $category);
        }
      }

      return Category::where('archive_id', $archive->id)
        ->orderBy('order')
        ->get();
    });
  }

  private function updateOrCreateCategory(array $data, Archive $archive): Category
  {
    $category = null;

    if (!empty($data['uuid'])) {
      $category = Category::where('archive_id', $archive->id)
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
      $category = Category::create([
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

  private function updateOrCreateRegister(array $data, Archive $archive, Category $category): Category
  {
    $register = null;

    if (!empty($data['uuid'])) {
      $register = Category::where('archive_id', $archive->id)
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
      $register = Category::create([
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
