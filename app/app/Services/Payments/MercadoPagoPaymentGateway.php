<?php

namespace App\Services\Payments;

use App\Enums\FulfillmentStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use RuntimeException;

class MercadoPagoPaymentGateway
{
    public function enabled(): bool
    {
        return (string) config('services.mercadopago.access_token', '') !== '';
    }

    /**
     * @return array{redirect_url:?string, mode:string}
     */
    public function initializeCheckout(Order $order): array
    {
        if (! $this->enabled()) {
            return [
                'redirect_url' => null,
                'mode' => 'manual',
            ];
        }

        $payment = $order->payments()->latest()->firstOrFail();
        $baseUrl = rtrim((string) config('services.mercadopago.base_url', 'https://api.mercadopago.com'), '/');
        $accessToken = (string) config('services.mercadopago.access_token');
        $statementDescriptor = substr((string) config('services.mercadopago.statement_descriptor', 'VERTICE'), 0, 13);

        $successUrl = URL::signedRoute('checkout.success', ['order' => $order]);
        $notificationUrl = route('webhooks.mercadopago', array_filter([
            'token' => (string) config('services.mercadopago.webhook_token', ''),
        ]));

        $preferencePayload = [
            'external_reference' => $order->order_number,
            'statement_descriptor' => $statementDescriptor,
            'items' => $order->items->map(fn ($item) => [
                'id' => (string) $item->id,
                'title' => trim($item->product_name.' '.($item->variant_name ?: '')),
                'quantity' => (int) $item->quantity,
                'unit_price' => round((float) $item->unit_price, 2),
                'currency_id' => 'BRL',
            ])->values()->all(),
            'payer' => array_filter([
                'name' => $order->customer_name,
                'email' => $order->customer_email,
                'phone' => $order->customer_phone ? [
                    'number' => preg_replace('/\D+/', '', (string) $order->customer_phone),
                ] : null,
            ]),
            'back_urls' => [
                'success' => $successUrl,
                'pending' => $successUrl,
                'failure' => $successUrl,
            ],
            'auto_return' => 'approved',
            'notification_url' => $notificationUrl,
            'metadata' => [
                'order_number' => $order->order_number,
                'payment_id' => $payment->id,
                'shipping_method_code' => $order->shipping_method_code,
            ],
        ];

        $response = Http::acceptJson()
            ->withToken($accessToken)
            ->timeout(15)
            ->post($baseUrl.'/checkout/preferences', $preferencePayload);

        if ($response->failed()) {
            throw new RuntimeException('mercadopago_preference_creation_failed');
        }

        $data = $response->json();
        if (! is_array($data)) {
            throw new RuntimeException('mercadopago_invalid_preference_response');
        }

        $checkoutUrl = (string) ($data['init_point'] ?? $data['sandbox_init_point'] ?? '');

        $payment->forceFill([
            'provider' => 'mercadopago',
            'transaction_id' => (string) ($data['id'] ?? $payment->transaction_id),
            'gateway_payload' => array_merge((array) $payment->gateway_payload, [
                'mercadopago' => [
                    'preference_id' => $data['id'] ?? null,
                    'checkout_url' => $checkoutUrl !== '' ? $checkoutUrl : null,
                    'sandbox_checkout_url' => $data['sandbox_init_point'] ?? null,
                    'preference' => $data,
                ],
            ]),
        ])->save();

        return [
            'redirect_url' => $checkoutUrl !== '' ? $checkoutUrl : null,
            'mode' => 'mercadopago',
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $query
     */
    public function handleWebhook(array $payload, array $query = []): ?Order
    {
        $configuredToken = (string) config('services.mercadopago.webhook_token', '');
        $requestToken = (string) ($query['token'] ?? '');

        if ($configuredToken !== '' && ! hash_equals($configuredToken, $requestToken)) {
            throw new RuntimeException('mercadopago_invalid_webhook_token');
        }

        $paymentId = $this->extractPaymentId($payload, $query);
        if ($paymentId === null) {
            return null;
        }

        return $this->syncPaymentByProviderId($paymentId, [
            'webhook_payload' => $payload,
            'webhook_query' => $query,
        ]);
    }

    public function syncPaymentByProviderId(string|int $providerPaymentId, array $context = []): ?Order
    {
        if (! $this->enabled()) {
            return null;
        }

        $baseUrl = rtrim((string) config('services.mercadopago.base_url', 'https://api.mercadopago.com'), '/');
        $accessToken = (string) config('services.mercadopago.access_token');

        $response = Http::acceptJson()
            ->withToken($accessToken)
            ->timeout(15)
            ->get($baseUrl.'/v1/payments/'.urlencode((string) $providerPaymentId));

        if ($response->failed()) {
            throw new RuntimeException('mercadopago_payment_sync_failed');
        }

        $data = $response->json();
        if (! is_array($data)) {
            throw new RuntimeException('mercadopago_invalid_payment_response');
        }

        $orderNumber = (string) ($data['external_reference'] ?? Arr::get($data, 'metadata.order_number', ''));
        if ($orderNumber === '') {
            return null;
        }

        $order = Order::query()
            ->with(['payments', 'items'])
            ->where('order_number', $orderNumber)
            ->first();

        if (! $order) {
            return null;
        }

        $payment = $order->payments()->latest()->first();
        if (! $payment) {
            return $order;
        }

        [$internalPaymentStatus, $internalOrderStatus] = $this->mapMercadoPagoStatus((string) ($data['status'] ?? 'pending'));

        $gatewayPayload = array_merge((array) $payment->gateway_payload, [
            'mercadopago' => array_filter([
                'payment_id' => (string) ($data['id'] ?? $providerPaymentId),
                'status' => $data['status'] ?? null,
                'status_detail' => $data['status_detail'] ?? null,
                'payment_method_id' => $data['payment_method_id'] ?? null,
                'payment_type_id' => $data['payment_type_id'] ?? null,
                'ticket_url' => Arr::get($data, 'transaction_details.external_resource_url'),
                'pix_qr_code' => Arr::get($data, 'point_of_interaction.transaction_data.qr_code'),
                'pix_ticket_url' => Arr::get($data, 'point_of_interaction.transaction_data.ticket_url'),
                'pix_qr_code_base64' => Arr::get($data, 'point_of_interaction.transaction_data.qr_code_base64'),
                'last_synced_at' => now()->toIso8601String(),
                'raw_payment' => $data,
                'last_webhook' => $context,
            ]),
        ]);

        $paymentUpdate = [
            'provider' => 'mercadopago',
            'transaction_id' => (string) ($data['id'] ?? $payment->transaction_id),
            'status' => $internalPaymentStatus,
            'amount' => (float) ($data['transaction_amount'] ?? $payment->amount),
            'gateway_payload' => $gatewayPayload,
        ];

        if ($internalPaymentStatus === PaymentStatus::Paid && $payment->paid_at === null) {
            $paymentUpdate['paid_at'] = now();
        }

        if ($internalPaymentStatus === PaymentStatus::Failed && $payment->failed_at === null) {
            $paymentUpdate['failed_at'] = now();
        }

        $payment->forceFill($paymentUpdate)->save();

        $oldStatus = $order->status->value;
        $oldPaymentStatus = $order->payment_status->value;

        $order->payment_status = $internalPaymentStatus;
        if ($internalPaymentStatus === PaymentStatus::Paid) {
            $order->status = OrderStatus::Paid;
            if ($order->fulfillment_status === FulfillmentStatus::Pending) {
                $order->fulfillment_status = FulfillmentStatus::Prepress;
            }
            $order->paid_at ??= now();
        } elseif ($internalOrderStatus !== null) {
            $order->status = $internalOrderStatus;
        }
        $order->save();

        $statusChanged = $oldStatus !== $order->status->value;
        $paymentStatusChanged = $oldPaymentStatus !== $order->payment_status->value;

        if ($statusChanged || $paymentStatusChanged) {
            $order->statusHistory()->create([
                'from_status' => $oldStatus,
                'to_status' => $order->status->value,
                'actor_type' => 'system',
                'actor_id' => null,
                'message' => 'Status atualizado automaticamente pela cobrança.',
                'metadata' => [
                    'payment_status_from' => $oldPaymentStatus,
                    'payment_status_to' => $order->payment_status->value,
                    'provider' => 'mercadopago',
                    'provider_payment_id' => (string) ($data['id'] ?? $providerPaymentId),
                    'provider_status' => (string) ($data['status'] ?? ''),
                ],
                'created_at' => now(),
            ]);
        }

        return $order->fresh(['payments', 'items']);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $query
     */
    private function extractPaymentId(array $payload, array $query): ?string
    {
        $type = (string) ($payload['type'] ?? $query['type'] ?? $query['topic'] ?? '');
        $action = (string) ($payload['action'] ?? '');

        $candidate = $payload['data']['id'] ?? $payload['id'] ?? $query['id'] ?? null;
        if ($candidate === null) {
            return null;
        }

        if ($type !== '' && $type !== 'payment' && ! str_contains($action, 'payment')) {
            return null;
        }

        return (string) $candidate;
    }

    /**
     * @return array{0: PaymentStatus, 1: ?OrderStatus}
     */
    private function mapMercadoPagoStatus(string $providerStatus): array
    {
        return match ($providerStatus) {
            'approved' => [PaymentStatus::Paid, OrderStatus::Paid],
            'authorized' => [PaymentStatus::Authorized, OrderStatus::PendingPayment],
            'cancelled', 'rejected', 'charged_back' => [PaymentStatus::Failed, OrderStatus::PendingPayment],
            'refunded' => [PaymentStatus::Refunded, OrderStatus::Refunded],
            default => [PaymentStatus::Pending, OrderStatus::PendingPayment],
        };
    }
}
