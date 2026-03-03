<?php

namespace App\Enums;

use App\Support\UiStatus;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Authorized = 'authorized';
    case Paid = 'paid';
    case Failed = 'failed';
    case Refunded = 'refunded';
    case PartiallyRefunded = 'partially_refunded';

    public function label(): string
    {
        return UiStatus::labelValue($this->value);
    }

    public function icon(): string
    {
        return UiStatus::iconValue($this->value);
    }

    public function tone(): string
    {
        return UiStatus::toneValue($this->value);
    }
}
