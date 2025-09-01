<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Archive;
use App\Models\Category;
use App\Actions\Category\Get as GetCategoriesAction;
use App\Actions\Category\UpdateOrCreate as UpdateOrCreateCategoryAction;
use App\Actions\Category\Delete as DeleteCategoryAction;
use App\Actions\Category\DeleteRegister as DeleteRegisterAction;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
  /**
   * Get the categories of an archive
   * 
   * @param Request $request
   * @param Archive $archive
   * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
   */
  public function get(Request $request, Archive $archive)
  {
    return CategoryResource::collection(
      (new GetCategoriesAction())->execute($archive)
    );
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

  /**
   * Delete a category
   * 
   * @param Category $category
   * @return \Illuminate\Http\Response
   */
  public function destroy(Category $category)
  {
    (new DeleteCategoryAction())->execute($category);
    return response()->noContent();
  }

  /**
   * Delete a register (category with parent_id)
   * 
   * @param Category $category
   * @return \Illuminate\Http\Response
   */
  public function destroyRegister(Category $category)
  {
    (new DeleteRegisterAction())->execute($category);
    return response()->noContent();
  }
}