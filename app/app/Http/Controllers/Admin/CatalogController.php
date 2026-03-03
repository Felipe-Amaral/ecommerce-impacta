<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\HomeBanner;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Contracts\View\View;

class CatalogController extends Controller
{
    public function __invoke(): View
    {
        abort_unless(auth()->user()?->is_admin, 403);

        return view('admin.catalog.index', [
            'stats' => [
                'categories' => Category::query()->count(),
                'products' => Product::query()->count(),
                'variants' => ProductVariant::query()->count(),
                'banners' => HomeBanner::query()->count(),
                'featured_products' => Product::query()->where('is_featured', true)->count(),
                'inactive_products' => Product::query()->where('is_active', false)->count(),
            ],
            'recentProducts' => Product::query()
                ->with('category')
                ->latest()
                ->limit(8)
                ->get(),
        ]);
    }
}
