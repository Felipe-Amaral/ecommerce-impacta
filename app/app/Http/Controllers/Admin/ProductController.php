<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $this->assertAdmin();

        $query = Product::query()
            ->with(['category', 'variants'])
            ->withCount('variants')
            ->latest();

        if ($request->filled('q')) {
            $term = trim((string) $request->query('q'));
            $query->where(function ($q) use ($term): void {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('slug', 'like', "%{$term}%")
                    ->orWhere('sku', 'like', "%{$term}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', (int) $request->query('category_id'));
        }

        return view('admin.catalog.products.index', [
            'products' => $query->paginate(20)->withQueryString(),
            'categories' => Category::query()->orderBy('sort_order')->orderBy('name')->get(),
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'category_id' => (string) $request->query('category_id', ''),
            ],
        ]);
    }

    public function create(): View
    {
        $this->assertAdmin();

        return view('admin.catalog.products.create', [
            'product' => new Product([
                'product_type' => 'print',
                'is_customizable' => true,
                'is_active' => true,
                'is_featured' => false,
                'lead_time_days' => 3,
                'min_quantity' => 1,
                'base_price' => 0,
            ]),
            'categories' => Category::query()->orderBy('sort_order')->orderBy('name')->get(),
            'specificationsText' => '',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->assertAdmin();

        $data = $this->validateProduct($request);

        $product = Product::query()->create($this->payloadFromValidated($data));

        return redirect()
            ->route('admin.catalog.products.edit', $product)
            ->with('success', 'Produto criado. Agora você pode cadastrar variações.');
    }

    public function edit(Product $product): View
    {
        $this->assertAdmin();

        $product->load(['variants', 'category']);

        return view('admin.catalog.products.edit', [
            'product' => $product,
            'categories' => Category::query()->orderBy('sort_order')->orderBy('name')->get(),
            'specificationsText' => $this->specificationsText($product->specifications),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $this->assertAdmin();

        $data = $this->validateProduct($request, $product);

        $product->update($this->payloadFromValidated($data));

        return redirect()->route('admin.catalog.products.edit', $product)->with('success', 'Produto atualizado.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateProduct(Request $request, ?Product $product = null): array
    {
        $rules = [
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')],
            'name' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:220'],
            'sku' => ['required', 'string', 'max:100'],
            'short_description' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string', 'max:20000'],
            'product_type' => ['nullable', 'string', 'max:60'],
            'is_customizable' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'lead_time_days' => ['nullable', 'integer', 'min:0', 'max:120'],
            'min_quantity' => ['nullable', 'integer', 'min:1', 'max:1000000'],
            'base_price' => ['nullable', 'numeric', 'min:0'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:255'],
            'specifications_json' => ['nullable', 'string', 'max:20000'],
        ];

        $rules['slug'][] = Rule::unique('products', 'slug')->ignore($product?->id);
        $rules['sku'][] = Rule::unique('products', 'sku')->ignore($product?->id);

        $data = $request->validate($rules);

        if (! empty($data['specifications_json'])) {
            json_decode((string) $data['specifications_json'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'specifications_json' => 'O campo de especificações deve conter JSON válido.',
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
            'category_id' => $data['category_id'] ?? null,
            'name' => $data['name'],
            'slug' => $this->resolveSlug($data['slug'] ?? null, $data['name']),
            'sku' => $data['sku'],
            'short_description' => $data['short_description'] ?? null,
            'description' => $data['description'] ?? null,
            'product_type' => $data['product_type'] ?? 'print',
            'is_customizable' => (bool) ($data['is_customizable'] ?? false),
            'is_active' => (bool) ($data['is_active'] ?? false),
            'is_featured' => (bool) ($data['is_featured'] ?? false),
            'lead_time_days' => (int) ($data['lead_time_days'] ?? 0),
            'min_quantity' => (int) ($data['min_quantity'] ?? 1),
            'base_price' => (float) ($data['base_price'] ?? 0),
            'seo_title' => $data['seo_title'] ?? null,
            'seo_description' => $data['seo_description'] ?? null,
            'specifications' => ! empty($data['specifications_json'])
                ? json_decode((string) $data['specifications_json'], true)
                : null,
        ];
    }

    private function resolveSlug(?string $slug, string $fallback): string
    {
        return Str::slug(trim((string) ($slug ?: $fallback))) ?: Str::slug($fallback);
    }

    private function specificationsText(mixed $specifications): string
    {
        if (! is_array($specifications) || $specifications === []) {
            return '';
        }

        return json_encode($specifications, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
    }

    private function assertAdmin(): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
    }
}
