<?php

namespace App\Enums;

use App\Support\UiStatus;

enum PaymentMethod: string
{
    case Pix = 'pix';
    case CreditCard = 'credit_card';
    case Boleto = 'boleto';
    case BankTransfer = 'bank_transfer';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

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
