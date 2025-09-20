<?php

namespace Viettuans\FileManager\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $config = config('filemanager.validation');
        
        return [
            'file' => [
                'required',
                'file',
                'max:' . $config['max_file_size'],
                'mimes:' . implode(',', $this->getAllowedExtensions()),
            ],
            'disk' => 'sometimes|string',
            'upload_path' => 'sometimes|string',
            'width' => 'sometimes|integer|min:1|max:5000',
            'height' => 'sometimes|integer|min:1|max:5000',
            'quality' => 'sometimes|integer|min:1|max:100',
            'alt_text' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'file.required' => __('filemanager::validation.file_required'),
            'file.file' => __('filemanager::validation.file_invalid'),
            'file.max' => __('filemanager::validation.file_too_large'),
            'file.mimes' => __('filemanager::validation.file_type_invalid'),
            'width.integer' => __('filemanager::validation.width_invalid'),
            'height.integer' => __('filemanager::validation.height_invalid'),
            'quality.integer' => __('filemanager::validation.quality_invalid'),
            'quality.min' => __('filemanager::validation.quality_min'),
            'quality.max' => __('filemanager::validation.quality_max'),
        ];
    }

    /**
     * Get allowed file extensions
     */
    protected function getAllowedExtensions(): array
    {
        return config('filemanager.validation.allowed_extensions', []);
    }
}
