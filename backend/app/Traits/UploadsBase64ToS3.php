<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait UploadsBase64ToS3
{
    /**
     * Uploads a base64 encoded image to S3 and returns the public URL.
     * If the string is already a URL, it returns it directly.
     *
     * @param string|null $base64String
     * @param string $folderPath
     * @param string $fileNamePrefix
     * @return string|null
     */
    public function uploadBase64ToS3(?string $base64String, string $folderPath, string $fileNamePrefix = 'img_')
    {
        if (empty($base64String)) {
            return null;
        }

        // If it's already an HTTP URL, no need to upload
        if (Str::startsWith($base64String, ['http://', 'https://'])) {
            return $base64String;
        }

        $extension = 'png'; // default
        $base64Data = $base64String;

        // Extract extension and data if it has a data URI scheme
        if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $matches)) {
            $extension = strtolower($matches[1]);
            if ($extension === 'jpeg') $extension = 'jpg';
            
            $base64Data = substr($base64String, strpos($base64String, ',') + 1);
        }

        $decodedImage = base64_decode($base64Data);

        if ($decodedImage === false) {
            return null;
        }

        $fileName = $fileNamePrefix . time() . '_' . Str::random(8) . '.' . $extension;
        $path = rtrim($folderPath, '/') . '/' . $fileName;

        // Upload to S3
        Storage::disk('s3')->put($path, $decodedImage, 'public');

        return Storage::disk('s3')->url($path);
    }
}
