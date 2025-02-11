<?php
namespace App\Actions\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Actions\Image\Resize as ResizeImageAction;

class Store
{
  public function execute(Request $request)
  {
    try {

      if (!$request->hasFile('files')) {
        return [
          'success' => false,
          'message' => 'No files uploaded',
          'status' => 400
        ];
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

      return [
        'success' => true,
        'files' => $uploadedFiles,
        'status' => 200
      ];
    } 
    catch (\Exception $e) {
      return [
        'success' => false,
        'message' => 'File upload failed',
        'error' => $e->getMessage(),
        'status' => 500
      ];
    }
  }

  protected function processFile($file)
  {
    // Get original filename and extension
    $originalName = $file->getClientOriginalName();
    $extension = $file->getClientOriginalExtension();

    // Clean up the filename
    $cleanName = $this->sanitizeFilename(
      pathinfo($originalName, PATHINFO_FILENAME)
    );
    
    // Generate unique ID
    $uniqueId = Str::random(8);
    
    // Construct filenames for both original and resized
    $originalFilename = $cleanName . '_' . $uniqueId . '.' . $extension;
    $resizedFilename = $cleanName . '_' . $uniqueId . '_resized.' . $extension;
    
    // Store the original file
    $originalPath = $file->storeAs('temp', $originalFilename, 'public');

    // Copy the file for resizing
    Storage::disk('public')->copy($originalPath, 'temp/' . $resizedFilename);
    $resizedPath = 'temp/' . $resizedFilename;

    // Get the full filesystem path for the image manipulation
    $fullPath = Storage::disk('public')->path($resizedPath);

    // Resize the copied image
    (new ResizeImageAction())->execute($fullPath);

    // Get size of resized file
    $resizedSize = Storage::disk('public')->size($resizedPath);

    return [
      'original' => [
        'original_name' => $originalName,
        'name' => $originalFilename,
        'path' => Storage::url($originalPath),
        'size' => $file->getSize(),
        'mime_type' => $file->getMimeType()
      ],
      'resized' => [
        'original_name' => $originalName,
        'name' => $resizedFilename,
        'path' => Storage::url($resizedPath),
        'size' => $resizedSize,
        'mime_type' => $file->getMimeType()
      ]
    ];
  }

  protected function sanitizeFilename($filename)
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