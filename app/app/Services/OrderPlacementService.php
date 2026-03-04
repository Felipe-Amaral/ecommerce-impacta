<?php

namespace App\Services;

use App\Enums\FulfillmentStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class OrderPlacementService
{
    public function __construct(
        private readonly OrderNumberGenerator $orderNumberGenerator,
    ) {
    }

    /**
     * @param  array<string, mixed>  $checkoutData
     * @param  array<string, mixed>  $cartSummary
     * @param  array<string, mixed>  $requestMeta
     */
    public function place(array $checkoutData, array $cartSummary, ?User $user = null, array $requestMeta = []): Order
    {
        return DB::transaction(function () use ($checkoutData, $cartSummary, $user, $requestMeta): Order {
            $customer = Arr::get($checkoutData, 'customer', []);
            $billing = Arr::get($checkoutData, 'billing', []);
            $shipping = Arr::get($checkoutData, 'shipping', []);
            $shippingOption = (array) Arr::get($checkoutData, 'shipping_option', []);
            $payment = Arr::get($checkoutData, 'payment', []);
            $notes = Arr::get($checkoutData, 'notes');

            $order = Order::query()->create([
                'order_number' => $this->orderNumberGenerator->generate(),
                'user_id' => $user?->id,
                'status' => OrderStatus::PendingPayment,
                'payment_status' => PaymentStatus::Pending,
                'fulfillment_status' => FulfillmentStatus::Pending,
                'customer_name' => (string) Arr::get($customer, 'name'),
                'customer_email' => (string) Arr::get($customer, 'email'),
                'customer_phone' => Arr::get($customer, 'phone'),
                'currency' => (string) ($cartSummary['currency'] ?? 'BRL'),
                'subtotal' => (float) ($cartSummary['subtotal'] ?? 0),
                'discount_total' => (float) ($cartSummary['discount_total'] ?? 0),
                'shipping_total' => (float) ($cartSummary['shipping_total'] ?? 0),
                'shipping_method_code' => Arr::get($shippingOption, 'code'),
                'shipping_method_label' => Arr::get($shippingOption, 'label'),
                'shipping_provider' => Arr::get($shippingOption, 'provider'),
                'shipping_delivery_days' => Arr::get($shippingOption, 'delivery_days'),
                'tax_total' => 0,
                'total' => (float) ($cartSummary['total'] ?? 0),
                'notes' => $notes,
                'billing_address' => $this->normalizeAddress($billing),
                'shipping_address' => $this->normalizeAddress($shipping),
                'shipping_quote_payload' => Arr::get($shippingOption, 'payload'),
                'metadata' => array_filter([
                    'customer_document' => Arr::get($customer, 'document'),
                    'installments' => Arr::get($payment, 'installments'),
                    'shipping' => array_filter([
                        'is_pickup' => Arr::get($shippingOption, 'is_pickup'),
                    ], fn ($value) => $value !== null),
                    'request' => $requestMeta,
                ]),
                'placed_at' => now(),
            ]);

            foreach ((array) ($cartSummary['items'] ?? []) as $item) {
                $orderItem = $order->items()->create([
                    'product_id' => Arr::get($item, 'product_id'),
                    'product_variant_id' => Arr::get($item, 'variant_id'),
                    'product_name' => (string) Arr::get($item, 'product_name'),
                    'variant_name' => Arr::get($item, 'variant_name'),
                    'sku' => Arr::get($item, 'sku'),
                    'quantity' => (int) Arr::get($item, 'quantity', 1),
                    'unit_price' => (float) Arr::get($item, 'unit_price', 0),
                    'discount_total' => 0,
                    'total' => (float) Arr::get($item, 'line_total', 0),
                    'configuration' => Arr::get($item, 'configuration', []),
                    'artwork_notes' => Arr::get($item, 'artwork_notes'),
                    'production_status' => 'pending_file',
                ]);

                $artworkPath = trim((string) Arr::get($item, 'artwork_upload.path', ''));
                if ($artworkPath !== '') {
                    $orderItem->artworkFiles()->create([
                        'storage_disk' => (string) Arr::get($item, 'artwork_upload.disk', 'public'),
                        'path' => $artworkPath,
                        'original_name' => (string) Arr::get($item, 'artwork_upload.original_name', 'arquivo-arte'),
                        'mime_type' => Arr::get($item, 'artwork_upload.mime_type'),
                        'size_bytes' => Arr::get($item, 'artwork_upload.size_bytes'),
                        'status' => 'uploaded',
                        'review_notes' => null,
                        'checklist' => null,
                        'metadata' => [
                            'source' => 'cart_upload',
                        ],
                        'uploaded_by_user_id' => $user?->id,
                    ]);
                }
            }

            $order->payments()->create([
                'provider' => (string) config('storefront.payments.driver', 'manual'),
                'method' => (string) Arr::get($payment, 'method', 'pix'),
                'status' => PaymentStatus::Pending,
                'amount' => (float) ($cartSummary['total'] ?? 0),
                'currency' => (string) ($cartSummary['currency'] ?? 'BRL'),
                'gateway_payload' => [
                    'mode' => (string) config('storefront.payments.driver', 'manual'),
                ],
            ]);

            $order->statusHistory()->create([
                'from_status' => null,
                'to_status' => OrderStatus::PendingPayment->value,
                'actor_type' => $user ? 'user' : 'guest',
                'actor_id' => $user?->id,
                'message' => 'Pedido criado via checkout web.',
                'metadata' => [
                    'payment_method' => Arr::get($payment, 'method'),
                ],
                'created_at' => now(),
            ]);

            // Open a customer service thread on every new order for alignment and file/payment details.
            $order->messages()->create([
                'user_id' => null,
                'sender_role' => 'system',
                'body' => 'Pedido criado com sucesso. Este chat fica disponível para alinhar arte, prazo, cobrança e entrega.',
                'metadata' => [
                    'source' => 'order_placement',
                ],
                'read_by_client_at' => now(),
                'read_by_admin_at' => null,
            ]);

            return $order->fresh(['items', 'payments']);
        });
    }

    /**
     * @param  array<string, mixed>  $address
     * @return array<string, mixed>
     */
    private function normalizeAddress(array $address): array
    {
        return [
            'recipient_name' => (string) Arr::get($address, 'recipient_name'),
            'phone' => (string) Arr::get($address, 'phone'),
            'zipcode' => (string) Arr::get($address, 'zipcode'),
            'street' => (string) Arr::get($address, 'street'),
            'number' => (string) Arr::get($address, 'number'),
            'complement' => Arr::get($address, 'complement'),
            'district' => (string) Arr::get($address, 'district'),
            'city' => (string) Arr::get($address, 'city'),
            'state' => strtoupper((string) Arr::get($address, 'state')),
            'country' => strtoupper((string) Arr::get($address, 'country', 'BR')),
        ];
    }
}
