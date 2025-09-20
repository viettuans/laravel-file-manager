<?php

namespace Viettuans\FileManager\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListFilesRequest extends FormRequest
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
            'type' => 'sometimes|string|in:image,document,video,audio',
            'search' => 'sometimes|string|max:255',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'sort_by' => 'sometimes|string|in:name,size,created_at,updated_at',
            'sort_order' => 'sometimes|string|in:asc,desc',
            'min_size' => 'sometimes|integer|min:0',
            'max_size' => 'sometimes|integer|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'type.in' => __('filemanager::validation.type_invalid'),
            'search.max' => __('filemanager::validation.search_too_long'),
            'page.integer' => __('filemanager::validation.page_invalid'),
            'per_page.max' => __('filemanager::validation.per_page_max'),
            'sort_by.in' => __('filemanager::validation.sort_by_invalid'),
            'sort_order.in' => __('filemanager::validation.sort_order_invalid'),
        ];
    }
}
