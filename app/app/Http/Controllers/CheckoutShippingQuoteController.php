<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\Shipping\ShippingQuoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CheckoutShippingQuoteController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly ShippingQuoteService $shippingQuoteService,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'zipcode' => ['required', 'string', 'max:16'],
            'selected_code' => ['nullable', 'string', 'max:100'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Informe um CEP válido para calcular o frete.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $cart = $this->cartService->summary();
        if (empty($cart['items'])) {
            return response()->json([
                'message' => 'Seu carrinho está vazio.',
            ], 422);
        }

        $zipcode = (string) $validator->validated()['zipcode'];
        $quotes = $this->shippingQuoteService->quote($cart, $zipcode);
        $selected = $this->shippingQuoteService->resolveSelected(
            $cart,
            $zipcode,
            ['code' => (string) ($validator->validated()['selected_code'] ?? '')],
        );
        $cartWithShipping = $this->shippingQuoteService->applySelectedToCart($cart, $selected);

        return response()->json([
            'quotes' => $quotes,
            'selected' => $selected,
            'cart' => [
                'subtotal' => $cartWithShipping['subtotal'],
                'discount_total' => $cartWithShipping['discount_total'],
                'shipping_total' => $cartWithShipping['shipping_total'],
                'total' => $cartWithShipping['total'],
            ],
            'pickup' => [
                'address' => (string) config('storefront.shipping.pickup.address', ''),
            ],
        ]);
    }
}
