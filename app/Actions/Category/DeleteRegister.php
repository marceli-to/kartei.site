<?php
namespace App\Actions\Category;
use App\Models\Category;

class DeleteRegister
{
  public function execute(Category $register): bool
  {
    // Ensure this is actually a register (has parent_id)
    if (!$register->isRegister()) {
      return false;
    }
    
    // Handle records - set category_id to null for orphaned records
    $register->records()->update(['category_id' => null]);
    
    // Delete the register
    return $register->delete();
  }
}