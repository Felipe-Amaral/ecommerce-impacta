<?php

namespace App\Services;

use App\Models\ProductVariant;

class CartService
{
    private const SESSION_KEY = 'storefront_cart_v1';

    /**
     * @return array<string, mixed>
     */
    public function summary(): array
    {
        return $this->summaryWithShipping();
    }

    /**
     * @return array<string, mixed>
     */
    public function summaryWithShipping(?float $shippingTotalOverride = null): array
    {
        $items = array_values($this->items());
        $subtotal = array_reduce(
            $items,
            fn (float $carry, array $item): float => $carry + (float) $item['line_total'],
            0.0,
        );

        $discountTotal = 0.0;
        $shippingTotal = $shippingTotalOverride !== null
            ? max(0, (float) $shippingTotalOverride)
            : ($subtotal > 0 && $subtotal < 300 ? 24.90 : 0.0);
        $total = max(0, $subtotal - $discountTotal + $shippingTotal);

        return [
            'items' => $items,
            'count' => array_sum(array_map(fn (array $item): int => (int) $item['quantity'], $items)),
            'subtotal' => round($subtotal, 2),
            'discount_total' => round($discountTotal, 2),
            'shipping_total' => round($shippingTotal, 2),
            'total' => round($total, 2),
            'currency' => 'BRL',
        ];
    }

    /**
     * @param  array<string, string>  $configuration
     */
    public function addVariant(
        ProductVariant $variant,
        int $quantity,
        array $configuration = [],
        ?string $artworkNotes = null,
        ?array $artworkUpload = null,
    ): string {
        $variant->loadMissing('product.images');

        $product = $variant->product;
        $unitPrice = (float) ($variant->promotional_price ?: $variant->price);
        $configuration = array_filter($configuration, fn ($value) => $value !== null && $value !== '');
        $artworkNotes = trim((string) $artworkNotes);

        $fingerprint = md5(implode('|', [
            (string) $variant->id,
            json_encode($configuration, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}',
            $artworkNotes,
            (string) ($artworkUpload['path'] ?? ''),
        ]));

        $lineId = substr($fingerprint, 0, 16);
        $items = $this->items();

        if (isset($items[$lineId])) {
            $items[$lineId]['quantity'] += $quantity;
        } else {
            $items[$lineId] = [
                'id' => $lineId,
                'product_id' => $product->id,
                'variant_id' => $variant->id,
                'slug' => $product->slug,
                'product_name' => $product->name,
                'variant_name' => $variant->name,
                'sku' => $variant->sku,
                'thumbnail' => $product->images->firstWhere('is_primary', true)?->path
                    ?? $product->images->first()?->path,
                'quantity' => $quantity,
                'unit_price' => round($unitPrice, 2),
                'line_total' => round($unitPrice * $quantity, 2),
                'configuration' => $configuration,
                'artwork_notes' => $artworkNotes !== '' ? $artworkNotes : null,
                'artwork_upload' => $artworkUpload,
            ];
        }

        $items[$lineId]['line_total'] = round((float) $items[$lineId]['unit_price'] * (int) $items[$lineId]['quantity'], 2);

        $this->put($items);

        return $lineId;
    }

    public function updateQuantity(string $lineId, int $quantity): void
    {
        $items = $this->items();

        if (! isset($items[$lineId])) {
            return;
        }

        $items[$lineId]['quantity'] = $quantity;
        $items[$lineId]['line_total'] = round((float) $items[$lineId]['unit_price'] * $quantity, 2);

        $this->put($items);
    }

    public function remove(string $lineId): void
    {
        $items = $this->items();
        unset($items[$lineId]);
        $this->put($items);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function items(): array
    {
        $items = session()->get(self::SESSION_KEY, []);

        return is_array($items) ? $items : [];
    }

    /**
     * @param  array<string, array<string, mixed>>  $items
     */
    private function put(array $items): void
    {
        session()->put(self::SESSION_KEY, $items);
    }
}
