<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductVariantController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $this->assertAdmin();

        $data = $this->validateVariant($request);

        $product->variants()->create($this->payloadFromValidated($data));

        return redirect()->route('admin.catalog.products.edit', $product)->with('success', 'Variação criada.');
    }

    public function update(Request $request, Product $product, ProductVariant $variant): RedirectResponse
    {
        $this->assertAdmin();
        abort_unless($variant->product_id === $product->id, 404);

        $data = $this->validateVariant($request, $variant);

        $variant->update($this->payloadFromValidated($data));

        return redirect()->route('admin.catalog.products.edit', $product)->with('success', 'Variação atualizada.');
    }

    public function destroy(Product $product, ProductVariant $variant): RedirectResponse
    {
        $this->assertAdmin();
        abort_unless($variant->product_id === $product->id, 404);

        $variant->delete();

        return redirect()->route('admin.catalog.products.edit', $product)->with('success', 'Variação removida.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateVariant(Request $request, ?ProductVariant $variant = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:180'],
            'sku' => ['required', 'string', 'max:120'],
            'attributes_json' => ['nullable', 'string', 'max:10000'],
            'price' => ['required', 'numeric', 'min:0'],
            'promotional_price' => ['nullable', 'numeric', 'min:0'],
            'production_days' => ['nullable', 'integer', 'min:0', 'max:120'],
            'weight_grams' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'stock_qty' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['nullable', 'boolean'],
        ];

        $rules['sku'][] = Rule::unique('product_variants', 'sku')->ignore($variant?->id);

        $data = $request->validate($rules);

        if (! empty($data['attributes_json'])) {
            json_decode((string) $data['attributes_json'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'attributes_json' => 'O campo de atributos deve conter JSON válido.',
                ]);
            }
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payloadFromValidated(array $data): array
    {
        return [
            'name' => $data['name'],
            'sku' => $data['sku'],
            'attributes' => ! empty($data['attributes_json']) ? json_decode((string) $data['attributes_json'], true) : null,
            'price' => (float) $data['price'],
            'promotional_price' => $data['promotional_price'] !== null && $data['promotional_price'] !== ''
                ? (float) $data['promotional_price']
                : null,
            'production_days' => $data['production_days'] !== null && $data['production_days'] !== ''
                ? (int) $data['production_days']
                : null,
            'weight_grams' => $data['weight_grams'] !== null && $data['weight_grams'] !== ''
                ? (int) $data['weight_grams']
                : null,
            'stock_qty' => $data['stock_qty'] !== null && $data['stock_qty'] !== ''
                ? (int) $data['stock_qty']
                : null,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => (bool) ($data['is_active'] ?? false),
        ];
    }

    private function assertAdmin(): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
    }
}
