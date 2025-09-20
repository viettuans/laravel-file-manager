<?php

namespace Viettuans\FileManager;

use Viettuans\FileManager\Contracts\FileManagerInterface;
use Viettuans\FileManager\Contracts\ImageProcessorInterface;
use Viettuans\FileManager\Models\Media;
use Viettuans\FileManager\Exceptions\FileValidationException;
use Viettuans\FileManager\Exceptions\FileUploadException;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Exception;

class FileManager implements FileManagerInterface
{
    protected ImageProcessorInterface $imageProcessor;
    protected array $config;

    public function __construct(ImageProcessorInterface $imageProcessor)
    {
        $this->imageProcessor = $imageProcessor;
        $this->config = config('filemanager');
    }

    /**
     * Get all media files with optional filters
     */
    public function getAll(array $filters = []): Collection
    {
        $query = Media::query();

        if (!empty($filters['type'])) {
            $query->where('mime_type', 'like', $filters['type'] . '%');
        }

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->latest()->get(['id', 'name', 'path', 'size', 'mime_type', 'created_at']);
    }

    /**
     * Upload and store a file
     */
    public function upload(UploadedFile $file, array $options = []): Media
    {
        // Validate the file
        $validation = $this->validateFile($file);
        if (!empty($validation['errors'])) {
            throw new FileValidationException(implode(', ', $validation['errors']));
        }

        try {
            $disk = $options['disk'] ?? $this->config['disk'];
            $uploadPath = $options['upload_path'] ?? $this->config['upload_path'];
            
            // Generate unique filename
            $filename = $this->generateFilename($file);
            $filePath = $uploadPath . '/' . $filename;

            // Process image if it's an image file
            if ($this->imageProcessor->isImage($file)) {
                $processedPath = $this->imageProcessor->process($file, array_merge([
                    'width' => $this->config['image']['max_width'],
                    'height' => $this->config['image']['max_height'],
                    'quality' => $this->config['image']['quality'],
                ], $options));
                
                Storage::disk($disk)->put($filePath, file_get_contents($processedPath));
                
                // Create thumbnail if enabled
                $thumbnailPath = null;
                if ($this->config['image']['create_thumbnails']) {
                    $thumbnailPath = $this->generateThumbnail(
                        new Media(['path' => $filePath]), 
                        $options
                    );
                }
            } else {
                // Store non-image files directly
                Storage::disk($disk)->putFileAs($uploadPath, $file, $filename);
            }

            // Save to database
            return Media::create([
                'name' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'path' => $filePath,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension(),
                'disk' => $disk,
                'thumbnail_path' => $thumbnailPath ?? null,
            ]);

        } catch (Exception $e) {
            throw new FileUploadException('Failed to upload file: ' . $e->getMessage());
        }
    }

    /**
     * Delete a media file
     */
    public function delete(int|string $identifier): bool
    {
        try {
            $media = is_numeric($identifier) 
                ? Media::find($identifier)
                : Media::where('name', $identifier)->first();

            if (!$media) {
                return false;
            }

            // Delete files from storage
            $disk = $media->disk ?? $this->config['disk'];
            Storage::disk($disk)->delete($media->path);
            
            if ($media->thumbnail_path) {
                Storage::disk($disk)->delete($media->thumbnail_path);
            }

            // Delete from database
            $media->delete();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Find a media file by ID
     */
    public function find(int $id): ?Media
    {
        return Media::find($id);
    }

    /**
     * Get media files with pagination
     */
    public function paginate(int $perPage = 15, array $filters = [])
    {
        $query = Media::query();

        if (!empty($filters['type'])) {
            $query->where('mime_type', 'like', $filters['type'] . '%');
        }

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Generate thumbnail for an image
     */
    public function generateThumbnail(Media $media, array $options = []): ?string
    {
        if (!$this->imageProcessor->isImage(new UploadedFile($media->path, $media->name))) {
            return null;
        }

        $thumbnailPath = $this->config['thumbnail_path'] . '/' . 'thumb_' . $media->name;
        
        $this->imageProcessor->createThumbnail(
            Storage::disk($media->disk ?? $this->config['disk'])->path($media->path),
            $options['thumbnail_width'] ?? $this->config['image']['thumbnail_width'],
            $options['thumbnail_height'] ?? $this->config['image']['thumbnail_height']
        );

        return $thumbnailPath;
    }

    /**
     * Validate uploaded file
     */
    public function validateFile(UploadedFile $file): array
    {
        $errors = [];
        $config = $this->config['validation'];

        // Check file size
        if ($file->getSize() > ($config['max_file_size'] * 1024)) {
            $errors[] = 'File size exceeds maximum allowed size of ' . $config['max_file_size'] . 'KB';
        }

        // Check file extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $config['allowed_extensions'])) {
            $errors[] = 'File extension "' . $extension . '" is not allowed';
        }

        // Check mime type
        if (!in_array($file->getMimeType(), $config['allowed_mime_types'])) {
            $errors[] = 'File type "' . $file->getMimeType() . '" is not allowed';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Generate unique filename
     */
    protected function generateFilename(UploadedFile $file): string
    {
        if ($this->config['security']['hash_filenames']) {
            return Str::random(32) . '.' . $file->getClientOriginalExtension();
        }

        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        
        return Str::slug($name) . '_' . time() . '.' . $extension;
    }
}
