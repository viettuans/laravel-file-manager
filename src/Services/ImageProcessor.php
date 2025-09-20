<?php

namespace Viettuans\FileManager\Services;

use Viettuans\FileManager\Contracts\ImageProcessorInterface;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Exception;

class ImageProcessor implements ImageProcessorInterface
{
    protected array $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];

    /**
     * Process an uploaded image
     */
    public function process(UploadedFile $file, array $options = []): string
    {
        if (!$this->isImage($file)) {
            throw new Exception('File is not a valid image');
        }

        $image = Image::make($file);
        
        // Resize if dimensions are provided
        if (!empty($options['width']) || !empty($options['height'])) {
            $image = $this->resize(
                $image, 
                $options['width'] ?? null, 
                $options['height'] ?? null,
                $options['maintain_aspect_ratio'] ?? true
            );
        }

        // Auto-optimize large images
        if ($options['auto_optimize'] ?? true) {
            $image = $this->optimize($image, $options['quality'] ?? null);
        }

        // Save to temporary location
        $tempPath = tempnam(sys_get_temp_dir(), 'processed_image_') . '.' . $file->getClientOriginalExtension();
        $image->save($tempPath, $options['quality'] ?? 85);

        return $tempPath;
    }

    /**
     * Resize an image (works with Image instance or path)
     */
    public function resize($imageOrPath, int $width = null, int $height = null, bool $maintainAspectRatio = true): string
    {
        $image = is_string($imageOrPath) ? Image::make($imageOrPath) : $imageOrPath;

        if ($width || $height) {
            $image->resize($width, $height, function ($constraint) use ($maintainAspectRatio) {
                if ($maintainAspectRatio) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                }
            });
        }

        if (is_string($imageOrPath)) {
            $image->save($imageOrPath);
            return $imageOrPath;
        }

        return $image;
    }

    /**
     * Create a thumbnail
     */
    public function createThumbnail(string $path, int $width, int $height = null): string
    {
        $image = Image::make($path);
        $thumbnailPath = $this->generateThumbnailPath($path);

        $image->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $image->save($thumbnailPath);
        return $thumbnailPath;
    }

    /**
     * Optimize image quality and size
     */
    public function optimize($imageOrPath, int $quality = null): string
    {
        $image = is_string($imageOrPath) ? Image::make($imageOrPath) : $imageOrPath;
        
        // Auto-determine quality based on file size
        if ($quality === null) {
            $fileSize = $image->filesize();
            if ($fileSize > 2000000) { // 2MB
                $quality = 70;
            } elseif ($fileSize > 1000000) { // 1MB
                $quality = 80;
            } else {
                $quality = 85;
            }
        }

        if (is_string($imageOrPath)) {
            $image->save($imageOrPath, $quality);
            return $imageOrPath;
        }

        return $image;
    }

    /**
     * Check if file is an image
     */
    public function isImage(UploadedFile $file): bool
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getMimeType();

        return in_array($extension, $this->imageExtensions) && 
               str_starts_with($mimeType, 'image/');
    }

    /**
     * Get image dimensions
     */
    public function getDimensions(string $path): array
    {
        try {
            $image = Image::make($path);
            return [
                'width' => $image->width(),
                'height' => $image->height()
            ];
        } catch (Exception $e) {
            return ['width' => 0, 'height' => 0];
        }
    }

    /**
     * Generate thumbnail path
     */
    protected function generateThumbnailPath(string $originalPath): string
    {
        $pathInfo = pathinfo($originalPath);
        return $pathInfo['dirname'] . '/thumbnails/thumb_' . $pathInfo['basename'];
    }
}
