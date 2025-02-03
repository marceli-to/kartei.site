<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Archive;

class ArchiveController extends Controller
{
  public function get(): \Illuminate\Http\JsonResponse
  {
    return response()->json(Archive::get());
  }

  public function create(): \Illuminate\Http\JsonResponse
  {
  }

  public function store(Request $request): \Illuminate\Http\JsonResponse
  {
  }

  public function show(string $id): \Illuminate\Http\JsonResponse
  {
  }

  public function edit(string $id): \Illuminate\Http\JsonResponse
  {
  }

  public function update(Request $request, string $id): \Illuminate\Http\JsonResponse
  {
  }

  public function destroy(string $id): \Illuminate\Http\JsonResponse
  {
  }
}
