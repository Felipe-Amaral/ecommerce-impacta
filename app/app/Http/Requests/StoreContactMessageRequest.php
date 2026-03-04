<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'lgpd_consent' => filter_var($this->input('lgpd_consent', false), FILTER_VALIDATE_BOOLEAN),
            'form_started_at' => (int) $this->input('form_started_at', 0),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'subject' => ['required', 'string', 'min:4', 'max:140'],
            'service_interest' => ['nullable', 'string', 'max:120'],
            'preferred_contact' => ['nullable', Rule::in(['email', 'whatsapp', 'phone'])],
            'order_reference' => ['nullable', 'string', 'max:80'],
            'message' => ['required', 'string', 'min:12', 'max:5000'],
            'lgpd_consent' => ['accepted'],
            'company_website' => [
                'nullable',
                'max:120',
                static function (string $attribute, mixed $value, \Closure $fail): void {
                    if (filled($value)) {
                        $fail('Não foi possível validar sua mensagem. Atualize a página e tente novamente.');
                    }
                },
            ],
            'form_started_at' => [
                'required',
                'integer',
                static function (string $attribute, mixed $value, \Closure $fail): void {
                    $startedAt = (int) $value;
                    $now = now()->timestamp;

                    if ($startedAt <= 0 || $startedAt > ($now + 15)) {
                        $fail('Não foi possível validar o envio. Atualize a página e tente novamente.');

                        return;
                    }

                    if (($now - $startedAt) < 1) {
                        $fail('Aguarde alguns segundos e envie novamente.');
                    }
                },
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'email' => 'e-mail',
            'phone' => 'telefone',
            'subject' => 'assunto',
            'service_interest' => 'interesse',
            'preferred_contact' => 'canal preferido',
            'order_reference' => 'referência',
            'message' => 'mensagem',
            'lgpd_consent' => 'aceite da política',
        ];
    }

    public function messages(): array
    {
        return [
            'lgpd_consent.accepted' => 'Para enviar, confirme o aceite da política de privacidade.',
        ];
    }
}
