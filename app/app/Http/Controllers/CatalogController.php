<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));
        $categorySlug = trim((string) $request->string('categoria'));

        $categories = Category::query()
            ->active()
            ->withCount([
                'products as active_products_count' => fn (Builder $query) => $query->where('is_active', true),
            ])
            ->orderBy('sort_order')
            ->get();

        $products = Product::query()
            ->active()
            ->with(['category', 'variants', 'images'])
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('short_description', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($categorySlug !== '', function (Builder $query) use ($categorySlug): void {
                $query->whereHas('category', fn (Builder $categoryQuery) => $categoryQuery->where('slug', $categorySlug));
            })
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->get();

        return view('store.catalog.index', compact('categories', 'products', 'search', 'categorySlug'));
    }

    public function show(string $slug): View
    {
        $product = Product::query()
            ->active()
            ->with(['category', 'variants' => fn ($query) => $query->active(), 'images'])
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedProducts = Product::query()
            ->active()
            ->with(['variants', 'images'])
            ->whereKeyNot($product->id)
            ->where('category_id', $product->category_id)
            ->take(4)
            ->get();

        return view('store.catalog.show', compact('product', 'relatedProducts'));
    }
}
