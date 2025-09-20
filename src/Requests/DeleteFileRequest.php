<?php

namespace Viettuans\FileManager\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteFileRequest extends FormRequest
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
        return [
            'id' => 'required_without:filename|integer|exists:' . config('filemanager.table_name', 'media') . ',id',
            'filename' => 'required_without:id|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'id.required_without' => __('filemanager::validation.id_or_filename_required'),
            'id.exists' => __('filemanager::validation.file_not_found'),
            'filename.required_without' => __('filemanager::validation.id_or_filename_required'),
        ];
    }
}
