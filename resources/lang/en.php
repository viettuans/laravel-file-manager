<?php

return [
    'messages' => [
        'system_error' => 'System error occurred. Please try again later.',
        'files_retrieved' => 'Files retrieved successfully.',
        'file_uploaded' => 'File uploaded successfully.',
        'file_deleted' => 'File deleted successfully.',
        'file_not_found' => 'File not found.',
        'upload_failed' => 'File upload failed.',
        'delete_failed' => 'File deletion failed.',
        'not_an_image' => 'File is not an image.',
        'thumbnail_generated' => 'Thumbnail generated successfully.',
        'thumbnail_failed' => 'Thumbnail generation failed.',
    ],
    
    'validation' => [
        'file_required' => 'Please select a file to upload.',
        'file_invalid' => 'The uploaded file is not valid.',
        'file_too_large' => 'The file size exceeds the maximum allowed size.',
        'file_type_invalid' => 'The file type is not allowed.',
        'width_invalid' => 'Width must be a valid number.',
        'height_invalid' => 'Height must be a valid number.',
        'quality_invalid' => 'Quality must be a number between 1 and 100.',
        'quality_min' => 'Quality must be at least 1.',
        'quality_max' => 'Quality cannot exceed 100.',
        'id_or_filename_required' => 'Either file ID or filename is required.',
        'type_invalid' => 'File type filter is invalid.',
        'search_too_long' => 'Search term is too long.',
        'page_invalid' => 'Page number must be a valid integer.',
        'per_page_max' => 'Items per page cannot exceed 100.',
        'sort_by_invalid' => 'Sort field is invalid.',
        'sort_order_invalid' => 'Sort order must be either asc or desc.',
    ],
];