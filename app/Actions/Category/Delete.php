<?php
namespace App\Actions\Category;
use App\Models\Category;

class Delete
{
  public function execute(Category $category): bool
  {
    // Delete all registers (children) first
    $category->registers()->delete();
    
    // Handle records - set category_id to null for orphaned records
    $category->records()->update(['category_id' => null]);
    
    // Delete the category
    return $category->delete();
  }
}