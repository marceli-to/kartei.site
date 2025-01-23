<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class FileUploadController extends Controller
{
	public function store(Request $request)
	{
		try {
			if (!$request->hasFile('files')) {
				return response()->json([
					'success' => false,
					'message' => 'No files uploaded'
				], 400);
			}

			$uploadedFiles = [];
			$files = $request->file('files');
			
			// Handle both single and multiple file uploads
			if (!is_array($files))
      {
				$files = [$files];
			}

			foreach ($files as $file)
      {
				if (!$file->isValid())
        {
					continue;
				}
				$uploadedFiles[] = $this->processFile($file);
			}

			return response()->json([
				'success' => true,
				'files' => $uploadedFiles
			]);
		}
    catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => 'File upload failed',
				'error' => $e->getMessage()
			], 500);
		}
	}

	protected function processFile($file)
	{
		// Get original filename and extension
		$originalName = $file->getClientOriginalName();
		$extension = $file->getClientOriginalExtension();

		// Clean up the filename
		$cleanName = $this->sanitizeFilename(pathinfo($originalName, PATHINFO_FILENAME));
		
		// Generate unique ID
		$uniqueId = Str::random(8);
		
		// Construct new filename
		$newFilename = $cleanName . '_' . $uniqueId . '.' . $extension;
		
		// Store the file
		$path = $file->storeAs('uploads', $newFilename, 'public');

		return [
			'original_name' => $originalName,
			'stored_name' => $newFilename,
			'path' => Storage::url($path),
			'size' => $file->getSize(),
			'mime_type' => $file->getMimeType()
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