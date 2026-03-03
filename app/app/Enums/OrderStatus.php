<?php

namespace App\Enums;

use App\Support\UiStatus;

enum OrderStatus: string
{
    case Draft = 'draft';
    case PendingPayment = 'pending_payment';
    case Paid = 'paid';
    case InProduction = 'in_production';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Canceled = 'canceled';
    case Refunded = 'refunded';

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
