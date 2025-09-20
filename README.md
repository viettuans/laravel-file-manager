# Laravel File Manager

[![Latest Version on Packagist](https://img.shields.io/packagist/v/viettuans/laravel-file-manager.svg?style=flat-square)](https://packagist.org/packages/viettuans/laravel-file-manager)
[![Total Downloads](https://img.shields.io/packagist/dt/viettuans/laravel-file-manager.svg?style=flat-square)](https://packagist.org/packages/viettuans/laravel-file-manager)
[![License](https://img.shields.io/github/license/viettuanit/laravel-file-manager.svg?style=flat-square)](LICENSE.md)

A modern, extensible Laravel file manager package with image processing, validation, and flexible storage options.

## Features

- 🚀 **Modern Architecture**: Built with interfaces and dependency injection for maximum extensibility
- 📁 **Multi-Storage Support**: Works with any Laravel filesystem disk (local, S3, etc.)
- 🖼️ **Advanced Image Processing**: Automatic resizing, optimization, and thumbnail generation
- ✅ **Comprehensive Validation**: File size, type, and security validation
- 🎯 **RESTful API**: Clean, well-documented API endpoints
- 🔍 **Advanced Filtering**: Search, filter by type, size, date, etc.
- 📊 **Database Optimization**: Proper indexing and query optimization
- 🌐 **Multilingual**: Built-in translation support
- 🛡️ **Security First**: File scanning, quarantine, and secure filename generation
- 📱 **Responsive**: Works great with any frontend framework

## Requirements

- PHP 8.1 or higher
- Laravel 9.0 or higher
- Intervention Image 2.7+ or 3.0+

## Installation

Install the package via Composer:

```bash
composer require viettuans/laravel-file-manager
```

Publish and run the migrations:

```bash
php artisan vendor:publish --tag="filemanager-migrations"
php artisan migrate
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag="filemanager-config"
```

Optionally, publish other assets:

```bash
# Publish all assets
php artisan vendor:publish --tag="filemanager"

# Or publish individually
php artisan vendor:publish --tag="filemanager-views"
php artisan vendor:publish --tag="filemanager-lang"
php artisan vendor:publish --tag="filemanager-assets"
```

## Configuration

The configuration file `config/filemanager.php` provides extensive customization options:

```php
return [
    // Storage settings
    'disk' => env('FILEMANAGER_DISK', 'public'),
    'upload_path' => env('FILEMANAGER_UPLOAD_PATH', 'uploads'),
    
    // Image processing
    'image' => [
        'max_width' => env('FILEMANAGER_MAX_WIDTH', 1920),
        'max_height' => env('FILEMANAGER_MAX_HEIGHT', 1080),
        'quality' => env('FILEMANAGER_QUALITY', 85),
        'create_thumbnails' => env('FILEMANAGER_CREATE_THUMBNAILS', true),
    ],
    
    // Validation rules
    'validation' => [
        'max_file_size' => env('FILEMANAGER_MAX_FILE_SIZE', 10240), // KB
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'],
        'allowed_mime_types' => [/* ... */],
    ],
    
    // Security
    'security' => [
        'hash_filenames' => env('FILEMANAGER_HASH_FILENAMES', true),
        'scan_uploads' => env('FILEMANAGER_SCAN_UPLOADS', false),
    ],
    
    // API routes
    'routes' => [
        'prefix' => env('FILEMANAGER_ROUTE_PREFIX', 'api/filemanager'),
        'middleware' => ['api'],
    ],
];
```

## Usage

### Basic Usage with Facade

```php
use Viettuans\FileManager\Facades\FileManager;

// Upload a file
$media = FileManager::upload($request->file('file'));

// Get all files
$files = FileManager::getAll();

// Get files with filters
$images = FileManager::getAll(['type' => 'image']);

// Delete a file
FileManager::delete($mediaId);

// Find a file
$media = FileManager::find($mediaId);
```

### Using Dependency Injection

```php
use Viettuans\FileManager\Contracts\FileManagerInterface;

class YourController extends Controller
{
    public function __construct(
        private FileManagerInterface $fileManager
    ) {}

    public function store(Request $request)
    {
        $media = $this->fileManager->upload($request->file('file'), [
            'width' => 800,
            'height' => 600,
            'quality' => 90,
        ]);

        return response()->json($media);
    }
}
```

### API Endpoints

The package provides RESTful API endpoints:

```http
GET    /api/filemanager          # List files with pagination and filters
POST   /api/filemanager          # Upload a new file
GET    /api/filemanager/{id}     # Get specific file details
DELETE /api/filemanager          # Delete a file (by ID or filename)
POST   /api/filemanager/{id}/thumbnail  # Generate thumbnail
```

### Upload Files

```javascript
// JavaScript example
const formData = new FormData();
formData.append('file', fileInput.files[0]);
formData.append('width', 1200);
formData.append('quality', 85);
formData.append('alt_text', 'Image description');

fetch('/api/filemanager', {
    method: 'POST',
    body: formData,
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
})
.then(response => response.json())
.then(data => console.log(data));
```

### Advanced Filtering

```php
// Get files with advanced filters
$files = FileManager::getAll([
    'type' => 'image',           // Filter by mime type
    'search' => 'vacation',      // Search in filename
    'min_size' => 1024,          // Minimum file size in bytes
    'max_size' => 1048576,       // Maximum file size in bytes
]);

// Paginated results
$files = FileManager::paginate(20, [
    'type' => 'document',
    'search' => 'report',
]);
```

### Working with Models

```php
use Viettuans\FileManager\Models\Media;

// Using Eloquent scopes
$images = Media::images()->latest()->take(10)->get();
$documents = Media::documents()->search('contract')->get();
$largeFiles = Media::sizeRange(1048576)->get(); // Files > 1MB

// Model attributes
$media = Media::find(1);
echo $media->url;                    // Full URL to file
echo $media->thumbnail_url;          // Thumbnail URL (if available)
echo $media->human_readable_size;    // "2.5 MB"
echo $media->is_image;               // true/false
```

### Custom Image Processing

```php
use Viettuans\FileManager\Contracts\ImageProcessorInterface;

class YourController extends Controller
{
    public function __construct(
        private ImageProcessorInterface $imageProcessor
    ) {}

    public function processImage(Request $request)
    {
        $file = $request->file('image');
        
        // Custom processing
        $processedPath = $this->imageProcessor->process($file, [
            'width' => 800,
            'height' => 600,
            'quality' => 90,
            'maintain_aspect_ratio' => true,
        ]);

        // Create thumbnail
        $thumbnailPath = $this->imageProcessor->createThumbnail(
            $processedPath, 
            300, 
            300
        );
    }
}
```

### Extending the Package

Create your own implementations:

```php
// Custom file manager
class CustomFileManager implements FileManagerInterface
{
    public function upload(UploadedFile $file, array $options = []): Media
    {
        // Your custom logic
    }
    
    // Implement other interface methods...
}

// Custom image processor
class CustomImageProcessor implements ImageProcessorInterface
{
    public function process(UploadedFile $file, array $options = []): string
    {
        // Your custom processing logic
    }
    
    // Implement other interface methods...
}

// Bind in service provider
$this->app->bind(FileManagerInterface::class, CustomFileManager::class);
$this->app->bind(ImageProcessorInterface::class, CustomImageProcessor::class);
```

## Environment Variables

Add these to your `.env` file for easy configuration:

```env
# Storage
FILEMANAGER_DISK=public
FILEMANAGER_UPLOAD_PATH=uploads
FILEMANAGER_THUMBNAIL_PATH=uploads/thumbnails

# Image processing
FILEMANAGER_MAX_WIDTH=1920
FILEMANAGER_MAX_HEIGHT=1080
FILEMANAGER_QUALITY=85
FILEMANAGER_CREATE_THUMBNAILS=true

# Validation
FILEMANAGER_MAX_FILE_SIZE=10240
FILEMANAGER_HASH_FILENAMES=true

# URLs
FILEMANAGER_URL_PREFIX=/storage
FILEMANAGER_ABSOLUTE_URLS=true

# API
FILEMANAGER_ROUTE_PREFIX=api/filemanager
```

## Security

This package includes several security features:

- **File validation**: Strict mime type and extension checking
- **Size limits**: Configurable file size restrictions
- **Filename hashing**: Optional secure filename generation
- **File scanning**: Optional virus/malware scanning integration
- **Quarantine**: Suspicious files can be quarantined

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Viet Tuan](https://github.com/viettuanit)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.