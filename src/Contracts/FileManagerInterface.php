<?php

namespace Viettuans\FileManager\Contracts;

use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Collection;
use Viettuans\FileManager\Models\Media;

interface FileManagerInterface
{
    /**
     * Get all media files
     */
    public function getAll(array $filters = []): Collection;

    /**
     * Upload and store a file
     */
    public function upload(UploadedFile $file, array $options = []): Media;

    /**
     * Delete a media file
     */
    public function delete(int|string $identifier): bool;

    /**
     * Find a media file by ID
     */
    public function find(int $id): ?Media;

    /**
     * Get media files with pagination
     */
    public function paginate(int $perPage = 15, array $filters = []);

    /**
     * Generate thumbnail for an image
     */
    public function generateThumbnail(Media $media, array $options = []): ?string;

    /**
     * Validate uploaded file
     */
    public function validateFile(UploadedFile $file): array;
}
