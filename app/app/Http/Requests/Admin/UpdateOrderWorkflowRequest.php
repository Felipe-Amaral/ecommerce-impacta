<?php

namespace App\Http\Requests\Admin;

use App\Enums\FulfillmentStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderWorkflowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) auth()->user()?->is_admin;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(array_column(OrderStatus::cases(), 'value'))],
            'payment_status' => ['required', Rule::in(array_column(PaymentStatus::cases(), 'value'))],
            'fulfillment_status' => ['required', Rule::in(array_column(FulfillmentStatus::cases(), 'value'))],
            'message' => ['nullable', 'string', 'max:300'],
        ];
    }
}
