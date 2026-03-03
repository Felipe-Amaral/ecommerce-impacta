<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\HomeBanner;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $heroBanners = HomeBanner::query()
            ->activeForDisplay()
            ->sorted()
            ->limit(8)
            ->get();

        $categories = Category::query()
            ->active()
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        $featuredProducts = Product::query()
            ->active()
            ->featured()
            ->with(['category', 'variants', 'images'])
            ->orderByDesc('created_at')
            ->take(8)
            ->get();

        return view('store.home', compact('categories', 'featuredProducts', 'heroBanners'));
    }
}
