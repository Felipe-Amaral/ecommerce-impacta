<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderPlacementService;
use App\Services\Payments\MercadoPagoPaymentGateway;
use App\Services\Payments\PaymentCheckoutService;
use App\Services\Shipping\ShippingQuoteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly OrderPlacementService $orderPlacementService,
        private readonly ShippingQuoteService $shippingQuoteService,
        private readonly PaymentCheckoutService $paymentCheckoutService,
    ) {
    }

    public function index(): RedirectResponse|View
    {
        $cart = $this->cartService->summary();

        if (empty($cart['items'])) {
            return redirect()
                ->route('catalog.index')
                ->with('error', 'Seu carrinho está vazio.');
        }

        return view('store.checkout.index', [
            'cart' => $cart,
            'paymentOptions' => $this->paymentOptions(),
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        $cart = $this->cartService->summary();

        if (empty($cart['items'])) {
            return redirect()
                ->route('catalog.index')
                ->with('error', 'Seu carrinho está vazio.');
        }

        $shippingSelection = $this->shippingQuoteService->resolveSelected(
            cartSummary: $cart,
            destinationZipcode: (string) data_get($request->validated(), 'shipping.zipcode', ''),
            selectedOption: (array) data_get($request->validated(), 'shipping_option', []),
        );

        $cart = $this->shippingQuoteService->applySelectedToCart($cart, $shippingSelection);

        $checkoutData = $request->validated();
        $checkoutData['shipping_option'] = $shippingSelection;

        $order = $this->orderPlacementService->place(
            checkoutData: $checkoutData,
            cartSummary: $cart,
            user: $request->user(),
            requestMeta: [
                'ip' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
                'channel' => 'web',
            ],
        );

        try {
            $paymentInit = $this->paymentCheckoutService->initialize($order);
        } catch (Throwable) {
            $paymentInit = ['redirect_url' => null, 'mode' => 'manual'];
        }

        $this->cartService->clear();

        if (($paymentInit['redirect_url'] ?? null) && config('storefront.payments.success_redirect', 'gateway') === 'gateway') {
            return redirect()->away((string) $paymentInit['redirect_url']);
        }

        return redirect()
            ->signedRoute('checkout.success', ['order' => $order])
            ->with('success', 'Pedido criado com sucesso. Agora siga para o pagamento.');
    }

    public function success(Order $order, Request $request, MercadoPagoPaymentGateway $mercadoPagoGateway): View
    {
        if ($request->user() && $order->user_id && $request->user()->id !== $order->user_id) {
            abort(403);
        }

        $providerPaymentId = $request->query('payment_id') ?? $request->query('collection_id');
        if ($providerPaymentId && (string) config('storefront.payments.driver', 'manual') === 'mercadopago') {
            try {
                $mercadoPagoGateway->syncPaymentByProviderId((string) $providerPaymentId, [
                    'source' => 'return_url',
                    'query' => $request->query(),
                ]);
                $order->refresh();
            } catch (Throwable) {
                // keep success page rendering even if sync fails; webhook can reconcile later
            }
        }

        $order->load(['items', 'payments']);

        return view('store.checkout.success', compact('order'));
    }

    /**
     * @return array<string, string>
     */
    private function paymentOptions(): array
    {
        return [
            PaymentMethod::Pix->value => 'PIX (instantâneo)',
            PaymentMethod::CreditCard->value => 'Cartão de crédito',
            PaymentMethod::Boleto->value => 'Boleto bancário',
            PaymentMethod::BankTransfer->value => 'Transferência bancária',
        ];
    }
}
