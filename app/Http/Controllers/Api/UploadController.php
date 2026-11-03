<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUploadRequest;
use App\Actions\Image\Store as StoreImageAction;
use App\Actions\Image\Delete as DeleteImageAction;
use App\Actions\Image\Order as OrderImageAction;
use Illuminate\Http\Request;

class UploadController extends Controller
{
  public function store(StoreUploadRequest $request) 
  {
    $files = (new StoreImageAction())->execute($request);
    return response()->json(['files' => $files]);
  }

  public function destroy(Request $request, $url) 
  {
    $deleted = (new DeleteImageAction())->execute($url, $request->user());
    return response()->json($deleted ? 200 : 422, $deleted ? 200 : 422);
  }

  public function order(Request $request)
  {
    $request->validate([
      'positions' => 'required|array',
      'positions.*.uuid' => 'nullable|string',
      'positions.*.position' => 'required|integer',
    ]);
    (new OrderImageAction())->execute($request->positions, $request->user());
    return response()->noContent();
  }
}