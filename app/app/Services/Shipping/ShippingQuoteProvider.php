<?php

namespace App\Services\Shipping;

interface ShippingQuoteProvider
{
    /**
     * @param  array<string, mixed>  $shipment
     * @return array<int, array<string, mixed>>
     */
    public function quote(array $shipment): array;
}
