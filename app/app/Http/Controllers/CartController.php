<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

        $artworkUpload = $this->storeArtworkUpload($request->file('artwork_file'));

        $this->cartService->addVariant(
            $variant,
            $request->integer('quantity'),
            (array) $request->input('configuration', []),
            $request->string('artwork_notes')->toString(),
            $artworkUpload,
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

    public function clear(): RedirectResponse
    {
        $items = (array) ($this->cartService->summary()['items'] ?? []);

        foreach ($items as $item) {
            $upload = (array) ($item['artwork_upload'] ?? []);
            $path = (string) ($upload['path'] ?? '');
            if ($path === '') {
                continue;
            }

            $disk = (string) ($upload['disk'] ?? 'public');
            try {
                Storage::disk($disk)->delete($path);
            } catch (\Throwable) {
                // Keep flow resilient; session cleanup is the priority.
            }
        }

        $this->cartService->clear();

        return redirect()
            ->route('cart.index')
            ->with('success', 'Carrinho limpo com sucesso.');
    }

    /**
     * @return array<string, mixed>|null
     */
    private function storeArtworkUpload(?UploadedFile $file): ?array
    {
        if (! $file) {
            return null;
        }

        $path = Storage::disk('public')->putFile('cart-artworks', $file);

        return [
            'disk' => 'public',
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size_bytes' => $file->getSize(),
        ];
    }
}
