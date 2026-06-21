<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class R2StorageController extends Controller
{
    /**
     * Get all files from R2 and calculate metrics.
     */
    public function index()
    {
        try {
            $disk = Storage::disk('s3');
            $files = $disk->allFiles('');
            
            $fileList = [];
            $totalSize = 0;
            
            foreach ($files as $file) {
                // Get file metadata
                $size = $disk->size($file);
                $lastModified = $disk->lastModified($file);
                $url = $disk->url($file);
                
                $totalSize += $size;
                
                $fileList[] = [
                    'name' => $file,
                    'size' => $size,
                    'lastModified' => $lastModified,
                    'url' => $url,
                    'isImage' => preg_match('/\.(jpg|jpeg|png|gif|webp|svg)$/i', $file)
                ];
            }
            
            // Sort by last modified DESC
            usort($fileList, function($a, $b) {
                return $b['lastModified'] <=> $a['lastModified'];
            });

            // Límite estricto de 10 GB según petición del usuario
            $limitBytes = 10 * 1024 * 1024 * 1024; // 10 GB
            
            return response()->json([
                'success' => true,
                'metrics' => [
                    'used_bytes' => $totalSize,
                    'limit_bytes' => $limitBytes,
                    'total_files' => count($files)
                ],
                'files' => $fileList
            ]);

        } catch (\Exception $e) {
            Log::error("Error reading from R2: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error connecting to R2 Storage: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload a new file to R2.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file'
        ]);

        try {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            
            // Subir archivo al bucket S3 (R2) usando 'public' visibility si es necesario
            $path = Storage::disk('s3')->putFileAs('', $file, $originalName, 'public');
            
            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'path' => $path
            ]);

        } catch (\Exception $e) {
            Log::error("Error uploading to R2: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error uploading file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a file from R2.
     */
    public function delete(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        try {
            $name = $request->input('name');
            $disk = Storage::disk('s3');
            
            if ($disk->exists($name)) {
                $disk->delete($name);
                return response()->json([
                    'success' => true,
                    'message' => 'File deleted successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error("Error deleting from R2: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting file: ' . $e->getMessage()
            ], 500);
        }
    }
}
