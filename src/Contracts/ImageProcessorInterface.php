<?php

namespace Viettuans\FileManager\Contracts;

use Illuminate\Http\UploadedFile;

interface ImageProcessorInterface
{
    /**
     * Process an uploaded image
     */
    public function process(UploadedFile $file, array $options = []): string;

    /**
     * Resize an image
     */
    public function resize(string $path, int $width, int $height = null, bool $maintainAspectRatio = true): string;

    /**
     * Create a thumbnail
     */
    public function createThumbnail(string $path, int $width, int $height = null): string;

    /**
     * Optimize image quality and size
     */
    public function optimize(string $path, int $quality = null): string;

    /**
     * Check if file is an image
     */
    public function isImage(UploadedFile $file): bool;

    /**
     * Get image dimensions
     */
    public function getDimensions(string $path): array;
}
