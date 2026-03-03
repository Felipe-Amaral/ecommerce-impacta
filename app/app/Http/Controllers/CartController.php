<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cartService)
    {
    }

    public function index(): View
    {
        return view('store.cart.index', [
            'cart' => $this->cartService->summary(),
        ]);
    }

    public function store(AddToCartRequest $request): RedirectResponse
    {
        $variant = ProductVariant::query()
            ->active()
            ->with(['product.images'])
            ->findOrFail($request->integer('variant_id'));

        if (! $variant->product || ! $variant->product->is_active) {
            return back()->withErrors(['variant_id' => 'Esta variação não está disponível no momento.']);
        }

        $this->cartService->addVariant(
            $variant,
            $request->integer('quantity'),
            (array) $request->input('configuration', []),
            $request->string('artwork_notes')->toString(),
        );

        return redirect()
            ->route('cart.index')
            ->with('success', 'Item adicionado ao carrinho.');
    }

    public function update(UpdateCartItemRequest $request, string $lineId): RedirectResponse
    {
        $this->cartService->updateQuantity($lineId, $request->integer('quantity'));

        return redirect()
            ->route('cart.index')
            ->with('success', 'Quantidade atualizada.');
    }

    public function destroy(string $lineId): RedirectResponse
    {
        $this->cartService->remove($lineId);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Item removido do carrinho.');
    }
}
