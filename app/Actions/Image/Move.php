<?php
namespace App\Actions\Image;

use Illuminate\Support\Facades\Storage;

class Move
{
  protected string $disk = 'public';

  public function execute(string $imageName, string $archiveUuid): bool
  {
    $sourcePath = "temp/{$imageName}";
    $destinationDir = "archives/{$archiveUuid}";
    $destinationPath = "{$destinationDir}/{$imageName}";

    // Check if source file exists
    if (!Storage::disk($this->disk)->exists($sourcePath)) {
      return false;
    }

    // Ensure the destination directory exists
    Storage::disk($this->disk)->makeDirectory($destinationDir);

    // Move the file
    return Storage::disk($this->disk)->move($sourcePath, $destinationPath);
  }
}
