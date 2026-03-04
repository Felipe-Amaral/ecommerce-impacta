<?php

namespace App\Services\Shipping;

use App\Models\ProductVariant;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Throwable;

class ShippingQuoteService
{
    public function __construct(
        private readonly LocalShippingQuoteProvider $localProvider,
        private readonly MelhorEnvioShippingQuoteProvider $melhorEnvioProvider,
    ) {
    }

    /**
     * @param  array<string, mixed>  $cartSummary
     * @return array<int, array<string, mixed>>
     */
    public function quote(array $cartSummary, string $destinationZipcode): array
    {
        $shipment = $this->buildShipmentContext($cartSummary, $destinationZipcode);

        $quotes = [];
        $driver = (string) config('storefront.shipping.driver', 'local');

        if ($driver === 'melhor_envio') {
            try {
                $quotes = $this->melhorEnvioProvider->quote($shipment);
            } catch (Throwable) {
                $quotes = [];
            }
        }

        if ($quotes === []) {
            $quotes = $this->localProvider->quote($shipment);
        }

        if ((bool) config('storefront.shipping.pickup.enabled', true)) {
            array_unshift($quotes, $this->pickupQuote());
        }

        return $this->normalizeAndSort($quotes)->all();
    }

    /**
     * @param  array<string, mixed>  $cartSummary
     * @param  array<string, mixed>|null  $selectedOption
     * @return array<string, mixed>
     */
    public function resolveSelected(array $cartSummary, string $destinationZipcode, ?array $selectedOption = null): array
    {
        $quotes = $this->quote($cartSummary, $destinationZipcode);
        $selectedCode = trim((string) Arr::get($selectedOption, 'code', ''));

        if ($selectedCode !== '') {
            foreach ($quotes as $quote) {
                if (($quote['code'] ?? null) === $selectedCode) {
                    return $quote;
                }
            }
        }

        return $quotes[0] ?? $this->pickupQuote();
    }

    /**
     * @param  array<string, mixed>  $cartSummary
     * @return array<string, mixed>
     */
    public function applySelectedToCart(array $cartSummary, array $selectedQuote): array
    {
        $shippingTotal = (float) ($selectedQuote['cost'] ?? 0);

        $subtotal = (float) ($cartSummary['subtotal'] ?? 0);
        $discountTotal = (float) ($cartSummary['discount_total'] ?? 0);
        $total = max(0, $subtotal - $discountTotal + $shippingTotal);

        $cartSummary['shipping_total'] = round($shippingTotal, 2);
        $cartSummary['total'] = round($total, 2);
        $cartSummary['shipping_selection'] = $selectedQuote;

        return $cartSummary;
    }

    /**
     * @param  array<string, mixed>  $cartSummary
     * @return array<string, mixed>
     */
    private function buildShipmentContext(array $cartSummary, string $destinationZipcode): array
    {
        $packageDefaults = (array) config('storefront.shipping.package_defaults', []);
        $origin = (array) config('storefront.shipping.origin', []);

        $cartItems = collect((array) ($cartSummary['items'] ?? []));
        $variantIds = $cartItems->pluck('variant_id')->filter()->map(fn ($id) => (int) $id)->all();

        $variants = ProductVariant::query()
            ->with('product.category')
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        $weightGrams = 0;

        foreach ($cartItems as $item) {
            $variant = $variants->get((int) ($item['variant_id'] ?? 0));
            $qty = max(1, (int) ($item['quantity'] ?? 1));

            $itemWeight = (int) ($variant?->weight_grams ?: $this->estimateVariantWeightGrams($variant?->product?->category?->slug));
            $weightGrams += max(50, $itemWeight) * $qty;
        }

        if ($weightGrams <= 0) {
            $weightGrams = (int) ($packageDefaults['weight_grams'] ?? 500);
        }

        return [
            'origin_zipcode' => (string) ($origin['zipcode'] ?? ''),
            'destination_zipcode' => $destinationZipcode,
            'items_count' => (int) ($cartSummary['count'] ?? 0),
            'subtotal' => (float) ($cartSummary['subtotal'] ?? 0),
            'weight_grams' => $weightGrams,
            'package' => [
                'width_cm' => (int) ($packageDefaults['width_cm'] ?? 20),
                'height_cm' => (int) ($packageDefaults['height_cm'] ?? 6),
                'length_cm' => (int) ($packageDefaults['length_cm'] ?? 28),
            ],
        ];
    }

    private function estimateVariantWeightGrams(?string $categorySlug): int
    {
        return match ($categorySlug) {
            'comunicacao-visual' => 900,
            'promocionais' => 350,
            'rotulos-e-etiquetas' => 250,
            'brindes-personalizados' => 500,
            'produtos-corporativos' => 420,
            'outros-produtos' => 850,
            default => 180,
        };
    }

    /**
     * @param  array<int, array<string, mixed>>  $quotes
     * @return Collection<int, array<string, mixed>>
     */
    private function normalizeAndSort(array $quotes): Collection
    {
        return collect($quotes)
            ->map(function (array $quote): array {
                return [
                    'code' => (string) ($quote['code'] ?? ''),
                    'label' => (string) ($quote['label'] ?? 'Frete'),
                    'provider' => (string) ($quote['provider'] ?? 'shipping'),
                    'cost' => round((float) ($quote['cost'] ?? 0), 2),
                    'delivery_days' => isset($quote['delivery_days']) ? (int) $quote['delivery_days'] : null,
                    'is_pickup' => (bool) ($quote['is_pickup'] ?? false),
                    'payload' => (array) ($quote['payload'] ?? []),
                ];
            })
            ->filter(fn (array $quote): bool => $quote['code'] !== '')
            ->sort(function (array $a, array $b): int {
                if ($a['is_pickup'] !== $b['is_pickup']) {
                    return $a['is_pickup'] ? -1 : 1;
                }

                return $a['cost'] <=> $b['cost'];
            })
            ->values();
    }

    /**
     * @return array<string, mixed>
     */
    private function pickupQuote(): array
    {
        $pickupConfig = (array) config('storefront.shipping.pickup', []);

        return [
            'code' => 'pickup_counter',
            'label' => (string) ($pickupConfig['label'] ?? 'Retirada no balcão'),
            'provider' => 'pickup',
            'cost' => 0.0,
            'delivery_days' => (int) ($pickupConfig['lead_time_days'] ?? 1),
            'is_pickup' => true,
            'payload' => [
                'address' => (string) ($pickupConfig['address'] ?? ''),
                'type' => 'pickup',
            ],
        ];
    }
}
