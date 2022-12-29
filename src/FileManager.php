<?php

namespace Viettuans\FileManager;

use Viettuans\FileManager\Models\Media;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;
use Storage;
class FileManager
{
    public function getList()
    {
        $items = Media::all(['id', 'name', 'path', 'size']);
        return $items;
    }

    public function storeMedia(UploadedFile $image, int $width = 1600, int $height = null, int $quality = null)
    {
        $fileInfo = $image->getClientOriginalName();
        $extension = pathinfo($fileInfo, PATHINFO_EXTENSION);
        $fileName = pathinfo($fileInfo, PATHINFO_FILENAME);
        $fileName = $fileName . '-' . time() . '.' . $extension;
        $interventionImage = Image::make($image);

        if (!empty($width) || !empty($height)) {
            $interventionImage = $interventionImage->resize($width, $height, function ($constraint) use ($width, $height) {
                if (empty($height) || empty($width)) {
                    $constraint->aspectRatio();
                }
            });
        }

        if ($interventionImage->filesize() > 512000 && empty($quality)) {
            $quality = 70;
        }

        $path = 'uploads/' . $fileName;
        $interventionImage->save(Storage::path($path), $quality);

        return Media::create([
            'name' => $fileName,
            'path' => 'storage/' . $path,
            'size' => $image->getSize(),
            'mime_type' => $extension,
            'branch_id' => Config::get('branch_id')
        ]);
    }

    public function deleteMedia($request)
    {
        $filename = $request->get('filename');
        $path = Media::UPLOAD_FOLDER . $filename;

        if ($filename) {
            Media::where('name', $filename)->delete();
            Storage::delete($path);

            return true;
        }
        return false;
    }
}
