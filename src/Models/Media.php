<?php

namespace Viettuans\FileManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'original_name',
        'path',
        'size',
        'mime_type',
        'extension',
        'disk',
        'thumbnail_path',
        'alt_text',
        'description',
    ];

    protected $casts = [
        'size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'url',
        'thumbnail_url',
        'human_readable_size',
        'is_image',
    ];

    /**
     * Get the table associated with the model.
     */
    public function getTable()
    {
        return config('filemanager.table_name', 'media');
    }

    /**
     * Get the full URL for the media file
     */
    public function getUrlAttribute(): string
    {
        if (empty($this->path)) {
            return '';
        }

        $disk = $this->disk ?? config('filemanager.disk', 'public');
        
        if (config('filemanager.url.generate_absolute', true)) {
            return Storage::disk($disk)->url($this->path);
        }

        return config('filemanager.url.prefix', '/storage') . '/' . $this->path;
    }

    /**
     * Get the thumbnail URL
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if (empty($this->thumbnail_path)) {
            return null;
        }

        $disk = $this->disk ?? config('filemanager.disk', 'public');
        
        if (config('filemanager.url.generate_absolute', true)) {
            return Storage::disk($disk)->url($this->thumbnail_path);
        }

        return config('filemanager.url.prefix', '/storage') . '/' . $this->thumbnail_path;
    }

    /**
     * Get human readable file size
     */
    public function getHumanReadableSizeAttribute(): string
    {
        if (!$this->size) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = $this->size;
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if the file is an image
     */
    public function getIsImageAttribute(): bool
    {
        return str_starts_with($this->mime_type ?? '', 'image/');
    }

    /**
     * Scope to filter by file type
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('mime_type', 'like', $type . '%');
    }

    /**
     * Scope to filter images only
     */
    public function scopeImages(Builder $query): Builder
    {
        return $query->where('mime_type', 'like', 'image/%');
    }

    /**
     * Scope to filter documents only
     */
    public function scopeDocuments(Builder $query): Builder
    {
        return $query->whereIn('extension', ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv']);
    }

    /**
     * Scope to search by name
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('original_name', 'like', "%{$search}%");
        });
    }

    /**
     * Scope to filter by size range
     */
    public function scopeSizeRange(Builder $query, int $minSize = null, int $maxSize = null): Builder
    {
        if ($minSize) {
            $query->where('size', '>=', $minSize);
        }
        
        if ($maxSize) {
            $query->where('size', '<=', $maxSize);
        }

        return $query;
    }

    /**
     * Check if file exists in storage
     */
    public function exists(): bool
    {
        $disk = $this->disk ?? config('filemanager.disk', 'public');
        return Storage::disk($disk)->exists($this->path);
    }

    /**
     * Get file content
     */
    public function getContent(): string
    {
        $disk = $this->disk ?? config('filemanager.disk', 'public');
        return Storage::disk($disk)->get($this->path);
    }

    /**
     * Delete file from storage when model is deleted
     */
    protected static function booted()
    {
        static::deleting(function ($media) {
            $disk = $media->disk ?? config('filemanager.disk', 'public');
            
            // Delete main file
            if (Storage::disk($disk)->exists($media->path)) {
                Storage::disk($disk)->delete($media->path);
            }
            
            // Delete thumbnail
            if ($media->thumbnail_path && Storage::disk($disk)->exists($media->thumbnail_path)) {
                Storage::disk($disk)->delete($media->thumbnail_path);
            }
        });
    }
}
