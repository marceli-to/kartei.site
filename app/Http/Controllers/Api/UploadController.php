<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUploadRequest;
use App\Actions\Image\Store as StoreImageAction;
use App\Actions\Image\Delete as DeleteImageAction;

class UploadController extends Controller
{
  public function store(StoreUploadRequest $request) 
  {
    $files = (new StoreImageAction())->execute($request);
    return response()->json(['files' => $files]);
  }

  public function destroy($url) 
  {
    (new DeleteImageAction())->execute($url);
    return response()->json(200);
  }
}