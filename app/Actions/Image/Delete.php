<?php
namespace App\Actions\Image;
use Illuminate\Support\Facades\Storage;
use App\Models\Media;

class Delete
{
  public function execute(string $url): bool
  {
    try {
      // Normalize the path: remove starting slashes and "storage/" if present
      $relativePath = ltrim($url, '/');
      $relativePath = preg_replace('#^storage/#', '', $relativePath); // remove "storage/" if at beginning

      $directory = dirname($relativePath); // temp or archives
      $filename = basename($relativePath);

      if (!str_contains($filename, '_resized')) {
        \Log::warning("Invalid image format: $filename");
        return false;
      }

      $resized = $directory . '/' . $filename;
      $original = $directory . '/' . str_replace('_resized', '_original', $filename);


      // Delete both resized and original
      foreach ([$resized, $original] as $imagePath) {
        if (Storage::disk('public')->exists($imagePath)) {
          Storage::disk('public')->delete($imagePath);
        }
        else {
          \Log::warning("File not found for deletion: $imagePath");
        }
      }

      // Delete _resized and _original from database
      $resized = $filename;
      $original = str_replace('_resized', '_original', $filename);
      Media::where('resized_name', $resized)->delete();
      Media::where('original_name', $original)->delete();
      return true;
    } 
    catch (\Exception $e) {
      \Log::error('Failed to delete image(s): ' . $e->getMessage());
      return false;
    }
  }
}