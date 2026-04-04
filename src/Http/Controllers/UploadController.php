<?php

namespace Evanrthompson\NovaTiptapWysiwyg\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;

class UploadController
{
    /**
     * Handle an image upload request from the Tiptap editor.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $maxKb = (int) config('nova-tiptap-wysiwyg.max_upload_size', 10240);

        $request->validate([
            'file' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:'.$maxKb],
        ]);

        $disk = config('nova-tiptap-wysiwyg.upload_disk', 'public');
        $path = config('nova-tiptap-wysiwyg.upload_path', 'wysiwyg');
        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());
        $filename = Str::uuid().'.'.$ext;

        $maxWidth = config('nova-tiptap-wysiwyg.max_image_width', 1280);
        $maxHeight = config('nova-tiptap-wysiwyg.max_image_height', 1280);

        if (class_exists(ImageManager::class)) {
            $storedPath = $this->processWithIntervention($file, $disk, $path, $filename, $ext, $maxWidth, $maxHeight);
        } elseif (function_exists('exif_read_data') && in_array($ext, ['jpg', 'jpeg'])) {
            $processed = $this->orientWithGd($file->getRealPath(), $maxWidth, $maxHeight);
            $storedPath = $path.'/'.$filename;
            Storage::disk($disk)->put($storedPath, $processed);
        } else {
            $storedPath = $file->storeAs($path, $filename, $disk);
        }

        return response()->json(['url' => Storage::disk($disk)->url($storedPath)]);
    }

    /**
     * Process image with Intervention Image: fix orientation, resize, and strip EXIF.
     */
    private function processWithIntervention(
        UploadedFile $file,
        string $disk,
        string $path,
        string $filename,
        string $ext,
        ?int $maxWidth,
        ?int $maxHeight,
    ): string {

        $driver = extension_loaded('imagick')
            ? new Driver
            : new \Intervention\Image\Drivers\Gd\Driver;

        $manager = new ImageManager($driver);
        $image = $manager->read($file->getRealPath());

        // Fix EXIF orientation — rotates pixels and resets orientation tag
        $image->orient();

        // Resize if exceeding configured max dimensions
        if ($maxWidth && $maxHeight && ($image->width() > $maxWidth || $image->height() > $maxHeight)) {
            $image->scaleDown($maxWidth, $maxHeight);
        }

        // Encode to the correct format, stripping all EXIF metadata
        $encoded = match ($ext) {
            'jpg', 'jpeg' => $image->toJpeg(90),
            'png' => $image->toPng(),
            'gif' => $image->toGif(),
            'webp' => $image->toWebp(90),
            default => $image->encodeByPath($filename),
        };

        $storedPath = $path.'/'.$filename;
        Storage::disk($disk)->put($storedPath, $encoded->toString());

        return $storedPath;
    }

    /**
     * Fix EXIF orientation and optionally resize using native GD functions.
     */
    private function orientWithGd(string $filePath, ?int $maxWidth, ?int $maxHeight): string
    {
        $exif = @exif_read_data($filePath);
        $image = imagecreatefromjpeg($filePath);

        if ($exif && isset($exif['Orientation'])) {
            $image = match ((int) $exif['Orientation']) {
                3 => imagerotate($image, 180, 0),
                6 => imagerotate($image, -90, 0),
                8 => imagerotate($image, 90, 0),
                default => $image,
            };
        }

        // Resize if exceeding max dimensions
        if ($maxWidth && $maxHeight) {
            $width = imagesx($image);
            $height = imagesy($image);

            if ($width > $maxWidth || $height > $maxHeight) {
                $scale = min($maxWidth / $width, $maxHeight / $height);
                $newWidth = (int) round($width * $scale);
                $newHeight = (int) round($height * $scale);
                $resized = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($image);
                $image = $resized;
            }
        }

        ob_start();
        imagejpeg($image, null, 90);
        imagedestroy($image);

        return ob_get_clean();
    }
}
