<?php
namespace App\Actions\Category;
use App\Models\Category;

class Delete
{
  public function execute(Category $category): bool
  {
    // Delete the category — FK cascade handles records and registers
    return $category->delete();
  }
}