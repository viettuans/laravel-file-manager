<?php

namespace Viettuans\FileManager\Controllers;

use Viettuans\FileManager\Contracts\FileManagerInterface;
use Viettuans\FileManager\Requests\UploadFileRequest;
use Viettuans\FileManager\Requests\DeleteFileRequest;
use Viettuans\FileManager\Requests\ListFilesRequest;
use Viettuans\FileManager\Exceptions\FileValidationException;
use Viettuans\FileManager\Exceptions\FileUploadException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;

class MediaController extends Controller
{
    protected FileManagerInterface $fileManager;

    public function __construct(FileManagerInterface $fileManager)
    {
        $this->fileManager = $fileManager;
    }

    /**
     * Get list of media files
     */
    public function index(ListFilesRequest $request): JsonResponse
    {
        try {
            $filters = $request->only(['type', 'search', 'min_size', 'max_size']);
            
            if ($request->has('page')) {
                $perPage = $request->get('per_page', 15);
                $media = $this->fileManager->paginate($perPage, $filters);
            } else {
                $media = $this->fileManager->getAll($filters);
            }

            return $this->successResponse($media, __('filemanager::messages.files_retrieved'));
        } catch (Exception $e) {
            Log::error('Failed to retrieve files: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);

            return $this->errorResponse(__('filemanager::messages.system_error'), 500);
        }
    }

    /**
     * Upload a new file
     */
    public function store(UploadFileRequest $request): JsonResponse
    {
        try {
            $file = $request->file('file');
            
            $options = $request->only([
                'disk', 'upload_path', 'width', 'height', 'quality'
            ]);

            $media = $this->fileManager->upload($file, $options);

            // Add optional metadata
            if ($request->has('alt_text')) {
                $media->alt_text = $request->get('alt_text');
            }
            
            if ($request->has('description')) {
                $media->description = $request->get('description');
            }
            
            $media->save();

            return $this->successResponse($media, __('filemanager::messages.file_uploaded'), 201);

        } catch (FileValidationException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        } catch (FileUploadException $e) {
            Log::error('File upload failed: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            
            return $this->errorResponse(__('filemanager::messages.upload_failed'), 500);
        } catch (Exception $e) {
            Log::error('Unexpected error during file upload: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);

            return $this->errorResponse(__('filemanager::messages.system_error'), 500);
        }
    }

    /**
     * Show a specific media file
     */
    public function show(int $id): JsonResponse
    {
        try {
            $media = $this->fileManager->find($id);
            
            if (!$media) {
                return $this->errorResponse(__('filemanager::messages.file_not_found'), 404);
            }

            return $this->successResponse($media);
        } catch (Exception $e) {
            Log::error('Failed to retrieve file: ' . $e->getMessage(), [
                'exception' => $e,
                'id' => $id
            ]);

            return $this->errorResponse(__('filemanager::messages.system_error'), 500);
        }
    }

    /**
     * Delete a media file
     */
    public function destroy(DeleteFileRequest $request): JsonResponse
    {
        try {
            $identifier = $request->get('id') ?? $request->get('filename');
            
            if ($this->fileManager->delete($identifier)) {
                return $this->successResponse(null, __('filemanager::messages.file_deleted'));
            }

            return $this->errorResponse(__('filemanager::messages.file_not_found'), 404);
        } catch (Exception $e) {
            Log::error('Failed to delete file: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);

            return $this->errorResponse(__('filemanager::messages.delete_failed'), 500);
        }
    }

    /**
     * Generate thumbnail for an image
     */
    public function generateThumbnail(Request $request, int $id): JsonResponse
    {
        try {
            $media = $this->fileManager->find($id);
            
            if (!$media) {
                return $this->errorResponse(__('filemanager::messages.file_not_found'), 404);
            }

            if (!$media->is_image) {
                return $this->errorResponse(__('filemanager::messages.not_an_image'), 422);
            }

            $options = $request->only(['thumbnail_width', 'thumbnail_height']);
            $thumbnailPath = $this->fileManager->generateThumbnail($media, $options);

            if ($thumbnailPath) {
                $media->thumbnail_path = $thumbnailPath;
                $media->save();

                return $this->successResponse($media, __('filemanager::messages.thumbnail_generated'));
            }

            return $this->errorResponse(__('filemanager::messages.thumbnail_failed'), 500);
        } catch (Exception $e) {
            Log::error('Failed to generate thumbnail: ' . $e->getMessage(), [
                'exception' => $e,
                'id' => $id
            ]);

            return $this->errorResponse(__('filemanager::messages.system_error'), 500);
        }
    }
}
