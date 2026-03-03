<?php

namespace App\Enums;

use App\Support\UiStatus;

enum FulfillmentStatus: string
{
    case Pending = 'pending';
    case Prepress = 'prepress';
    case Approved = 'approved';
    case Printing = 'printing';
    case Finishing = 'finishing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Canceled = 'canceled';

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
