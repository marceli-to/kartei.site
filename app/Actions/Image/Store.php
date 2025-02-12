<?php
namespace App\Actions\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Actions\Image\Resize as ResizeImageAction;

class Store
{
  protected string $storagePath = 'temp';

  public function execute(Request $request): array
  {
    return $this->processFiles($request->file('files'));
  }

  protected function processFiles($files): array
  {
    if (!is_array($files)) {
      $files = [$files];
    }
    return array_values(array_filter(array_map(fn($file) => $this->validateAndProcessFile($file), $files)));
  }

  protected function validateAndProcessFile($file): ?array
  {
    return $file->isValid() ? $this->processFile($file) : null;
  }

  protected function processFile($file): array
  {
    $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    $cleanName = $this->sanitizeFilename($baseName);
    $uniqueId = now()->timestamp . '_' . Str::random(5);
    $extension = $file->getClientOriginalExtension();

    $originalFilename = $this->generateFilename($cleanName, $uniqueId, $extension, '_original');
    $resizedFilename = $this->generateFilename($cleanName, $uniqueId, $extension, '_resized');

    $originalPath = $this->storeFile($file, $originalFilename);
    $resizedImageData = $this->resizeAndStoreFile($originalPath, $resizedFilename);

    return [
      'original' => $this->getFileDetails($file, $originalFilename, $originalPath),
      'resized' => $this->getFileDetails($file, $resizedFilename, $resizedImageData['url']) + $resizedImageData,
    ];
  }
  
  protected function generateFilename(string $cleanName, string $uniqueId, string $extension, string $suffix = ''): string
  {
    return "{$cleanName}_{$uniqueId}{$suffix}.{$extension}";
  }

  protected function storeFile($file, string $filename): string
  {
    return $file->storeAs($this->storagePath, $filename, 'public');
  }

  protected function resizeAndStoreFile(string $originalPath, string $resizedFilename): array
  {
    $resizedPath = "$this->storagePath/$resizedFilename";

    $resizedImageData = (new ResizeImageAction())->execute(
      Storage::disk('public')->path($originalPath),
      Storage::disk('public')->path($resizedPath)
    );

    // Ensure we return the correct public URL
    $resizedImageData['url'] = Storage::url($resizedPath);

    return $resizedImageData;
  }

  protected function getFileDetails($file, string $filename, string $path): array
  {
    return [
      'original_name' => $file->getClientOriginalName(),
      'name' => $filename,
      'url' => $path,
      'mime_type' => $file->getMimeType(),
    ];
  }

  protected function sanitizeFilename(string $filename): string
  {
    return Str::slug($filename, '-');
  }
}
