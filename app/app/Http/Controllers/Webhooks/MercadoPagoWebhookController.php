<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Services\Payments\MercadoPagoPaymentGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class MercadoPagoWebhookController extends Controller
{
    public function __invoke(Request $request, MercadoPagoPaymentGateway $gateway): JsonResponse
    {
        try {
            $order = $gateway->handleWebhook(
                payload: (array) $request->all(),
                query: (array) $request->query(),
            );

            return response()->json([
                'ok' => true,
                'order' => $order?->order_number,
            ]);
        } catch (RuntimeException $e) {
            $status = str_contains($e->getMessage(), 'invalid_webhook_token') ? 403 : 422;

            return response()->json([
                'ok' => false,
                'message' => $e->getMessage(),
            ], $status);
        }
    }
}
