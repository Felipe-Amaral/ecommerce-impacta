<?php

namespace App\Services\Shipping;

class LocalShippingQuoteProvider implements ShippingQuoteProvider
{
    /**
     * @param  array<string, mixed>  $shipment
     * @return array<int, array<string, mixed>>
     */
    public function quote(array $shipment): array
    {
        $destinationCep = (string) ($shipment['destination_zipcode'] ?? '');
        $destinationDigits = preg_replace('/\D+/', '', $destinationCep) ?: '';
        $originDigits = preg_replace('/\D+/', '', (string) ($shipment['origin_zipcode'] ?? '')) ?: '';
        $itemsCount = (int) ($shipment['items_count'] ?? 0);
        $weightGrams = max(100, (int) ($shipment['weight_grams'] ?? 500));
        $subtotal = (float) ($shipment['subtotal'] ?? 0);

        $distanceFactor = $this->distanceFactor($originDigits, $destinationDigits);
        $weightFactor = max(0, ($weightGrams - 500) / 1000);
        $volumeFactor = max(0, $itemsCount - 1) * 1.4;
        $insuranceFactor = $subtotal > 0 ? min(24.0, $subtotal * 0.0085) : 0.0;

        $pac = 17.90 + ($distanceFactor * 7.2) + ($weightFactor * 8.8) + $volumeFactor + $insuranceFactor;
        $sedex = 25.90 + ($distanceFactor * 12.6) + ($weightFactor * 11.4) + ($volumeFactor * 1.2) + ($insuranceFactor * 1.05);
        $carrier = 21.50 + ($distanceFactor * 8.7) + ($weightFactor * 9.6) + ($volumeFactor * 1.6) + $insuranceFactor;

        return [
            $this->makeQuote(
                code: 'correios_pac',
                label: 'Correios PAC',
                provider: 'correios',
                cost: $pac,
                deliveryDays: $this->deadlineDays('pac', $distanceFactor),
                metadata: ['mode' => 'local_rate_table'],
            ),
            $this->makeQuote(
                code: 'correios_sedex',
                label: 'Correios SEDEX',
                provider: 'correios',
                cost: $sedex,
                deliveryDays: $this->deadlineDays('sedex', $distanceFactor),
                metadata: ['mode' => 'local_rate_table'],
            ),
            $this->makeQuote(
                code: 'transportadora_economica',
                label: 'Transportadora econômica',
                provider: 'transportadora',
                cost: $carrier,
                deliveryDays: $this->deadlineDays('carrier', $distanceFactor),
                metadata: ['mode' => 'local_rate_table'],
            ),
        ];
    }

    private function distanceFactor(string $originCep, string $destinationCep): float
    {
        if ($originCep === '' || $destinationCep === '') {
            return 1.0;
        }

        $originPrefix = (int) substr($originCep, 0, 1);
        $destinationPrefix = (int) substr($destinationCep, 0, 1);
        $delta = abs($originPrefix - $destinationPrefix);

        return 0.8 + ($delta * 0.35);
    }

    private function deadlineDays(string $service, float $distanceFactor): int
    {
        $base = match ($service) {
            'sedex' => 2,
            'carrier' => 3,
            default => 4,
        };

        return (int) ceil($base + max(0, $distanceFactor - 1.0) * 1.6);
    }

    /**
     * @return array<string, mixed>
     */
    private function makeQuote(
        string $code,
        string $label,
        string $provider,
        float $cost,
        int $deliveryDays,
        array $metadata = [],
    ): array {
        return [
            'code' => $code,
            'label' => $label,
            'provider' => $provider,
            'cost' => round(max(0, $cost), 2),
            'delivery_days' => max(1, $deliveryDays),
            'is_pickup' => false,
            'payload' => $metadata,
        ];
    }
}
