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

    protected function prepareForValidation(): void
    {
        $quantity = $this->input('quantity');

        $this->merge([
            'quantity' => is_numeric($quantity) ? (int) $quantity : $quantity,
        ]);
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

    public function messages(): array
    {
        return [
            'variant_id.required' => 'Selecione uma variação do produto.',
            'variant_id.integer' => 'Selecione uma variação válida.',
            'variant_id.exists' => 'Esta variação não está disponível no momento.',
            'quantity.required' => 'Informe a quantidade.',
            'quantity.integer' => 'A quantidade deve ser um número inteiro.',
            'quantity.min' => 'A quantidade mínima permitida é :min.',
            'quantity.max' => 'A quantidade máxima permitida é :max.',
            'artwork_notes.max' => 'As observações da arte podem ter no máximo :max caracteres.',
            'artwork_file.file' => 'Envie um arquivo válido.',
            'artwork_file.max' => 'O arquivo deve ter no máximo 20MB.',
            'artwork_file.mimes' => 'Formato inválido. Use PDF, AI, EPS, PSD, CDR, ZIP, JPG ou PNG.',
            'configuration.array' => 'A configuração enviada é inválida.',
            'configuration.*.max' => 'Cada campo de configuração pode ter no máximo :max caracteres.',
        ];
    }

    public function attributes(): array
    {
        return [
            'variant_id' => 'variação',
            'quantity' => 'quantidade',
            'artwork_notes' => 'observações',
            'artwork_file' => 'arquivo de arte',
        ];
    }
}
