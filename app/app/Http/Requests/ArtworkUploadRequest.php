<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArtworkUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'checklist' => [
                'cmyk' => filter_var($this->input('checklist.cmyk', false), FILTER_VALIDATE_BOOLEAN),
                'bleed' => filter_var($this->input('checklist.bleed', false), FILTER_VALIDATE_BOOLEAN),
                'outlined_fonts' => filter_var($this->input('checklist.outlined_fonts', false), FILTER_VALIDATE_BOOLEAN),
                'high_resolution_images' => filter_var($this->input('checklist.high_resolution_images', false), FILTER_VALIDATE_BOOLEAN),
            ],
        ]);
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'max:25600', 'mimes:pdf,jpg,jpeg,png,tif,tiff,zip'],
            'customer_notes' => ['nullable', 'string', 'max:2000'],
            'checklist' => ['required', 'array'],
            'checklist.cmyk' => ['boolean'],
            'checklist.bleed' => ['boolean'],
            'checklist.outlined_fonts' => ['boolean'],
            'checklist.high_resolution_images' => ['boolean'],
        ];
    }
}
