<?php

namespace App\Http\Requests;

use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $sameAsBilling = filter_var($this->input('same_as_billing', false), FILTER_VALIDATE_BOOLEAN);

        if ($sameAsBilling) {
            $this->merge([
                'shipping' => $this->input('billing', []),
            ]);
        }

        $this->merge([
            'same_as_billing' => $sameAsBilling,
        ]);
    }

    public function rules(): array
    {
        $addressRules = [
            'recipient_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30'],
            'zipcode' => ['required', 'string', 'max:16'],
            'street' => ['required', 'string', 'max:180'],
            'number' => ['required', 'string', 'max:20'],
            'complement' => ['nullable', 'string', 'max:120'],
            'district' => ['required', 'string', 'max:120'],
            'city' => ['required', 'string', 'max:120'],
            'state' => ['required', 'string', 'max:4'],
            'country' => ['nullable', 'string', 'size:2'],
        ];

        return [
            'customer.name' => ['required', 'string', 'max:120'],
            'customer.email' => ['required', 'email', 'max:190'],
            'customer.phone' => ['required', 'string', 'max:30'],
            'customer.document' => ['nullable', 'string', 'max:32'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'same_as_billing' => ['nullable', 'boolean'],
            'shipping_option' => ['required', 'array'],
            'shipping_option.code' => ['required', 'string', 'max:100'],
            'shipping_option.label' => ['required', 'string', 'max:190'],
            'shipping_option.provider' => ['required', 'string', 'max:80'],
            'shipping_option.cost' => ['required', 'numeric', 'min:0'],
            'shipping_option.delivery_days' => ['nullable', 'integer', 'min:0', 'max:120'],
            'shipping_option.is_pickup' => ['nullable', 'boolean'],
            'payment.method' => ['required', Rule::in(PaymentMethod::values())],
            'payment.installments' => ['nullable', 'integer', 'min:1', 'max:12'],
            'billing' => ['required', 'array'],
            'shipping' => ['required', 'array'],
        ] + $this->prefixedAddressRules('billing', $addressRules)
            + $this->prefixedAddressRules('shipping', $addressRules);
    }

    /**
     * @param  array<string, array<int, string>>  $addressRules
     * @return array<string, array<int, string>>
     */
    private function prefixedAddressRules(string $prefix, array $addressRules): array
    {
        $rules = [];

        foreach ($addressRules as $field => $fieldRules) {
            $rules["{$prefix}.{$field}"] = $fieldRules;
        }

        return $rules;
    }
}
