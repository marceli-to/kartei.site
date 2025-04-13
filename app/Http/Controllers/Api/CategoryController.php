<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Archive;
use App\Models\Category;
use App\Actions\Category\UpdateOrCreate as UpdateOrCreateCategoryAction;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
  /**
   * Get the categories of an archive
   * 
   * @param Archive $archive
   * @return void
   */
  public function get(Request $request, Archive $archive)
  {
    $categories = $archive->categories()
      ->with('registers')
      ->get();
    return CategoryResource::collection($categories);
  }

  /**
   * Store the category of an archive
   * 
   * @param Archive $archive
   * @return void
   */
  public function store(Request $request, Archive $archive)
  {
    (new UpdateOrCreateCategoryAction())->execute($request->all(), $archive);
    $category = $archive->categories()
      ->with('registers')
      ->get();
    return CategoryResource::collection($category);
  }
}



