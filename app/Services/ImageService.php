<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageService
{
    protected ?ImageManager $manager = null;

    public function __construct()
    {
        if (extension_loaded('gd')) {
            try {
                $this->manager = new ImageManager(new Driver());
            } catch (\Throwable $e) {
                $this->manager = null;
            }
        }
    }

    /**
     * Upload an image file, generate thumb and medium versions, and return array of paths.
     *
     * @param  UploadedFile  $file
     * @param  string  $directory
     * @return array{original: string, thumb: string, medium: string}
     */
    public function uploadProductImage(UploadedFile $file, string $directory = 'products'): array
    {
        $filename = Str::random(32) . '.' . $file->getClientOriginalExtension();
        $relativeOriginal = "{$directory}/{$filename}";
        $relativeMedium = "{$directory}/medium_{$filename}";
        $relativeThumb = "{$directory}/thumb_{$filename}";

        // Save original file to public disk
        Storage::disk('public')->putFileAs($directory, $file, $filename);

        // If GD extension is available with Intervention Image v3, resize copies
        if ($this->manager) {
            try {
                // Medium version (max width 900px)
                $imgMedium = $this->manager->read($file->getRealPath());
                $imgMedium->scaleDown(width: 900);
                Storage::disk('public')->put($relativeMedium, (string) $imgMedium->encode());

                // Thumbnail version (max width 400px)
                $imgThumb = $this->manager->read($file->getRealPath());
                $imgThumb->scaleDown(width: 400);
                Storage::disk('public')->put($relativeThumb, (string) $imgThumb->encode());

                return [
                    'original' => $relativeOriginal,
                    'medium' => $relativeMedium,
                    'thumb' => $relativeThumb,
                ];
            } catch (\Throwable $e) {
                // Graceful fallback if image encoding fails
            }
        }

        // Fallback: copy original as medium and thumb
        Storage::disk('public')->copy($relativeOriginal, $relativeMedium);
        Storage::disk('public')->copy($relativeOriginal, $relativeThumb);

        return [
            'original' => $relativeOriginal,
            'medium' => $relativeMedium,
            'thumb' => $relativeThumb,
        ];
    }

    /**
     * Delete an image and its resized variants.
     */
    public function deleteImage(string $path): void
    {
        if (empty($path)) {
            return;
        }

        Storage::disk('public')->delete($path);

        $dirname = dirname($path);
        $basename = basename($path);
        Storage::disk('public')->delete("{$dirname}/medium_{$basename}");
        Storage::disk('public')->delete("{$dirname}/thumb_{$basename}");
    }
}
