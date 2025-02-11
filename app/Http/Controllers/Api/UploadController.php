<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUploadRequest;
use App\Actions\Image\Store as StoreImageAction;

class UploadController extends Controller
{
  public function store(StoreUploadRequest $request)
  {
    $files = (new StoreImageAction())->execute($request);
    return response()->json($files);
  }
}