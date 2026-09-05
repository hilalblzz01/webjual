<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageCompressor
{
    /**
     * Compress & resize uploaded image automatically.
     * Reduces 3MB-5MB screenshots down to ~50KB-150KB while keeping crisp quality.
     */
    public static function compressAndStore(UploadedFile $file, string $folder = "uploads", int $maxDimension = 1000, int $quality = 75): string
    {
        $mime = strtolower($file->getMimeType());
        $realPath = $file->getRealPath();

        // Create GD image resource
        $srcImage = match (true) {
            str_contains($mime, "png")  => @imagecreatefrompng($realPath),
            str_contains($mime, "webp") => @imagecreatefromwebp($realPath),
            default                     => @imagecreatefromjpeg($realPath),
        };

        // Fallback if GD fails
        if (!$srcImage) {
            return $file->store($folder, "public");
        }

        $origWidth  = imagesx($srcImage);
        $origHeight = imagesy($srcImage);

        // Calculate aspect ratio resize
        if ($origWidth > $maxDimension || $origHeight > $maxDimension) {
            if ($origWidth >= $origHeight) {
                $newWidth  = $maxDimension;
                $newHeight = (int) round(($origHeight / $origWidth) * $maxDimension);
            } else {
                $newHeight = $maxDimension;
                $newWidth  = (int) round(($origWidth / $origHeight) * $maxDimension);
            }
        } else {
            $newWidth  = $origWidth;
            $newHeight = $origHeight;
        }

        $dstImage = imagecreatetruecolor($newWidth, $newHeight);

        if (str_contains($mime, "png")) {
            imagealphablending($dstImage, false);
            imagesavealpha($dstImage, true);
            $transparent = imagecolorallocatealpha($dstImage, 255, 255, 255, 127);
            imagefilledrectangle($dstImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

        ob_start();
        if (str_contains($mime, "png")) {
            imagepng($dstImage, null, 6);
        } else {
            imagejpeg($dstImage, null, $quality);
        }
        $imageData = ob_get_clean();

        imagedestroy($srcImage);
        imagedestroy($dstImage);

        $ext = str_contains($mime, "png") ? ".png" : ".jpg";
        $filename = Str::random(40) . $ext;
        $path = $folder . "/" . $filename;

        Storage::disk("public")->put($path, $imageData);

        return $path;
    }
}

