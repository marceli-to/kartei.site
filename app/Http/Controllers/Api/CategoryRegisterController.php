<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Archive;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\RegisterResource;

class CategoryRegisterController extends Controller
{
  /**
   * Get all categories for an archive
   * 
   * @param Archive $archive
   * @return void
   */

   public function get(Request $request, Archive $archive)
   {
     return response()->json([
       'categories' => CategoryResource::collection(
         $archive->categories()->get()
       ),
       'registers' => RegisterResource::collection(
         $archive->registers()->get()
       ),
     ]);
   }
}