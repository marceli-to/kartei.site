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
    
    // Move records up to the parent category before deleting the register
    $register->records()->update(['category_id' => $register->parent_id]);
    
    // Delete the register
    return $register->delete();
  }
}