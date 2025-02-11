<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Actions\Image\Store as StoreImageAction;

class UploadController extends Controller
{
	public function store(Request $request)
	{
    $result = (new StoreImageAction())->execute($request);
        
    return response()->json([
        'success' => $result['success'],
        'files' => $result['files'] ?? null,
        'message' => $result['message'] ?? null,
        'error' => $result['error'] ?? null
      ],
      $result['status']
    );
	}

}