<?php
namespace App\Actions\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Actions\Image\Resize as ResizeImageAction;

class Store
{
  public function execute(Request $request): array
  {
    if (!$request->hasFile('files')) {
      throw new \Exception('No files uploaded', 400);
    }

    $uploadedFiles = [];
    $files = $request->file('files');

    // Handle both single and multiple file uploads
    if (!is_array($files)) {
      $files = [$files];
    }

    foreach ($files as $file) {
      if (!$file->isValid()) {
        continue;
      }
      $uploadedFiles[] = $this->processFile($file);
    }

      return $uploadedFiles;
  }

  protected function processFile($file): array
  {
    $originalName = $file->getClientOriginalName();
    $extension = $file->getClientOriginalExtension();
    $cleanName = $this->sanitizeFilename(pathinfo($originalName, PATHINFO_FILENAME));
    $uniqueId = Str::random(8);

    // Generate filenames for original and resized versions
    $originalFilename = $this->generateFilename($cleanName, $uniqueId, $extension, '_original');
    $resizedFilename = $this->generateFilename($cleanName, $uniqueId, $extension, '_resized');

    // Store the original file
    $originalPath = $this->storeFile($file, $originalFilename);

    // Create a resized version of the original file
    $resizedPath = $this->resizeAndStoreFile($originalPath, $resizedFilename);

    return [
      'original' => $this->getFileDetails($file, $originalFilename, $originalPath),
      'resized' => $this->getFileDetails($file, $resizedFilename, $resizedPath),
    ];
  }

  protected function generateFilename($cleanName, $uniqueId, $extension, $suffix = ''): string
  {
    return $cleanName . '_' . $uniqueId . $suffix . '.' . $extension;
  }

  protected function storeFile($file, $filename): string
  {
    return $file->storeAs('temp', $filename, 'public');
  }

  protected function resizeAndStoreFile($originalPath, $resizedFilename): string
  {
    // Define the path for the resized file
    $resizedPath = 'temp/' . $resizedFilename;

    // Resize the original file and save it to the resized path
    (new ResizeImageAction())->execute(
      Storage::disk('public')->path($originalPath), // Source path (original file)
      Storage::disk('public')->path($resizedPath)  // Destination path (resized file)
    );

    return $resizedPath;
  }

  protected function getFileDetails($file, $filename, $path): array
  {
    return [
      'original_name' => $file->getClientOriginalName(),
      'name' => $filename,
      'path' => Storage::url($path),
      'mime_type' => $file->getMimeType(),
    ];
  }

  protected function sanitizeFilename($filename): string
  {
    // Convert to lowercase
    $clean = strtolower($filename);

    // Replace spaces and special characters with dashes
    $clean = preg_replace('/[^a-z0-9]/', '-', $clean);

    // Remove multiple consecutive dashes
    $clean = preg_replace('/-+/', '-', $clean);

    // Remove leading and trailing dashes
    $clean = trim($clean, '-');

    // Ensure we have at least one character
    if (empty($clean)) {
      $clean = 'file';
    }

    return $clean;
  }
}