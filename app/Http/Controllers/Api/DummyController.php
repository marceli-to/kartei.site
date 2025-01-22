<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DummyController extends Controller
{
  public function index()
  {
    return response()->json();
  }

  public function update(Request $request)
  {
    return response()->json();
  }

  public function destroy()
  {
    return response()->json(['message' => 'Record deleted successfully']);
  }
}