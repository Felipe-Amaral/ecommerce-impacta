<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'variant_id' => [
                'required',
                'integer',
                Rule::exists('product_variants', 'id')->where(fn ($query) => $query->where('is_active', true)),
            ],
            'quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'artwork_notes' => ['nullable', 'string', 'max:2000'],
            'artwork_file' => ['nullable', 'file', 'max:20480', 'mimes:pdf,ai,eps,psd,cdr,zip,jpg,jpeg,png'],
            'configuration' => ['nullable', 'array'],
            'configuration.*' => ['nullable', 'string', 'max:255'],
        ];
    }
}
