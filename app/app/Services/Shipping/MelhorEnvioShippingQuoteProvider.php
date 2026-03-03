<?php

namespace App\Services\Shipping;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class MelhorEnvioShippingQuoteProvider implements ShippingQuoteProvider
{
    /**
     * @param  array<string, mixed>  $shipment
     * @return array<int, array<string, mixed>>
     */
    public function quote(array $shipment): array
    {
        $token = (string) config('services.melhor_envio.token');
        if ($token === '') {
            throw new RuntimeException('missing_melhor_envio_token');
        }

        $baseUrl = rtrim((string) config('services.melhor_envio.base_url', ''), '/');
        $sandbox = (bool) config('services.melhor_envio.sandbox', false);

        if ($baseUrl === '') {
            throw new RuntimeException('missing_melhor_envio_base_url');
        }

        $payload = [
            'from' => [
                'postal_code' => preg_replace('/\D+/', '', (string) ($shipment['origin_zipcode'] ?? '')),
            ],
            'to' => [
                'postal_code' => preg_replace('/\D+/', '', (string) ($shipment['destination_zipcode'] ?? '')),
            ],
            'products' => [[
                'id' => 'cart-package',
                'width' => (int) ($shipment['package']['width_cm'] ?? 20),
                'height' => (int) ($shipment['package']['height_cm'] ?? 6),
                'length' => (int) ($shipment['package']['length_cm'] ?? 28),
                'weight' => round(max(0.1, ((int) ($shipment['weight_grams'] ?? 500)) / 1000), 3),
                'insurance_value' => round((float) ($shipment['subtotal'] ?? 0), 2),
                'quantity' => 1,
            ]],
            'options' => [
                'receipt' => false,
                'own_hand' => false,
                'reverse' => false,
                'non_commercial' => true,
                'insurance_value' => true,
            ],
        ];

        $response = Http::acceptJson()
            ->withToken($token)
            ->withHeaders(array_filter([
                'User-Agent' => 'VerticeGrafica/1.0',
                'Sandbox' => $sandbox ? 'true' : null,
            ]))
            ->timeout(12)
            ->post($baseUrl.'/shipment/calculate', $payload);

        if ($response->failed()) {
            throw new RuntimeException('melhor_envio_quote_failed');
        }

        $data = $response->json();
        if (! is_array($data)) {
            throw new RuntimeException('melhor_envio_invalid_response');
        }

        $quotes = [];

        foreach ($data as $row) {
            if (! is_array($row)) {
                continue;
            }

            if (! empty($row['error'])) {
                continue;
            }

            $price = $row['custom_price'] ?? $row['price'] ?? null;
            if ($price === null) {
                continue;
            }

            $deliveryTime = $row['custom_delivery_time'] ?? $row['delivery_time'] ?? null;
            $deliveryDays = is_numeric($deliveryTime) ? (int) $deliveryTime : null;

            $companyName = trim((string) data_get($row, 'company.name', ''));
            $serviceName = trim((string) ($row['name'] ?? 'Frete'));
            $provider = str_contains(strtolower($companyName), 'correios') ? 'correios' : 'transportadora';

            $codeSeed = (string) ($row['id'] ?? $serviceName);
            $quotes[] = [
                'code' => 'me_'.strtolower(preg_replace('/[^a-z0-9]+/i', '_', $codeSeed) ?: uniqid()),
                'label' => trim($companyName !== '' ? ($companyName.' - '.$serviceName) : $serviceName),
                'provider' => $provider,
                'cost' => round((float) $price, 2),
                'delivery_days' => $deliveryDays !== null && $deliveryDays > 0 ? $deliveryDays : null,
                'is_pickup' => false,
                'payload' => [
                    'provider' => 'melhor_envio',
                    'raw' => $row,
                ],
            ];
        }

        if ($quotes === []) {
            throw new RuntimeException('melhor_envio_no_quotes');
        }

        return $quotes;
    }
}
