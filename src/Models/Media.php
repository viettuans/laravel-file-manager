<?php

namespace Viettuans\FileManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;
    const UPLOAD_FOLDER = "uploads";
    const THUMBNAIL_FOLDER = "uploads/thumbs";

    protected $fillable = [
        'name',
        'path',
        'size',
        'mime_type',
    ];

    protected $appends = [
        'full_path'
    ];

    public function getFullPathAttribute()
    {
        return !empty($this->path) ? asset($this->path) : '';
    }
}
