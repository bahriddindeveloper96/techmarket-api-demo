<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'images.*' => [
                'required',
                'file',
                'mimes:' . implode(',', config('files.allowed_extensions')),
                'max:' . config('files.max_size')
            ],
            'is_main' => 'boolean'
        ];
    }
}
