<?php

namespace App\Services\Payments;

use App\Models\Order;

class PaymentCheckoutService
{
    public function __construct(
        private readonly MercadoPagoPaymentGateway $mercadoPagoGateway,
    ) {
    }

    /**
     * @return array{redirect_url:?string, mode:string}
     */
    public function initialize(Order $order): array
    {
        $driver = (string) config('storefront.payments.driver', 'manual');

        if ($driver === 'mercadopago') {
            return $this->mercadoPagoGateway->initializeCheckout($order);
        }

        return [
            'redirect_url' => null,
            'mode' => 'manual',
        ];
    }
}
