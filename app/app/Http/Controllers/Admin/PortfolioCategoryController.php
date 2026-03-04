<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortfolioCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PortfolioCategoryController extends Controller
{
    public function index(): View
    {
        $this->assertAdmin();

        return view('admin.portfolio.categories.index', [
            'categories' => PortfolioCategory::query()
                ->withCount('projects')
                ->sorted()
                ->get(),
            'category' => new PortfolioCategory([
                'is_active' => true,
                'sort_order' => (int) (PortfolioCategory::query()->max('sort_order') ?? 0) + 1,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->assertAdmin();

        $data = $this->validateCategory($request);
        PortfolioCategory::query()->create($this->payloadFromValidated($data));

        return redirect()->route('admin.portfolio.categories.index')->with('success', 'Categoria criada.');
    }

    public function update(Request $request, PortfolioCategory $portfolioCategory): RedirectResponse
    {
        $this->assertAdmin();

        $data = $this->validateCategory($request, $portfolioCategory);
        $portfolioCategory->update($this->payloadFromValidated($data, $portfolioCategory));

        return redirect()->route('admin.portfolio.categories.index')->with('success', 'Categoria atualizada.');
    }

    public function destroy(PortfolioCategory $portfolioCategory): RedirectResponse
    {
        $this->assertAdmin();

        $portfolioCategory->delete();

        return redirect()->route('admin.portfolio.categories.index')->with('success', 'Categoria removida.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateCategory(Request $request, ?PortfolioCategory $category = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:160', Rule::unique('portfolio_categories', 'slug')->ignore($category?->id)],
            'description' => ['nullable', 'string', 'max:2200'],
            'color_hex' => ['nullable', 'string', 'regex:/^#?[0-9A-Fa-f]{6}$/', 'max:12'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payloadFromValidated(array $data, ?PortfolioCategory $existing = null): array
    {
        return [
            'name' => trim((string) $data['name']),
            'slug' => $this->resolveUniqueSlug($data['slug'] ?? null, (string) $data['name'], $existing?->id),
            'description' => $this->nullableTrim($data['description'] ?? null),
            'color_hex' => $this->normalizeColorHex($data['color_hex'] ?? null),
            'seo_title' => $this->nullableTrim($data['seo_title'] ?? null),
            'seo_description' => $this->nullableTrim($data['seo_description'] ?? null),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => (bool) ($data['is_active'] ?? false),
        ];
    }

    private function resolveUniqueSlug(?string $slug, string $fallback, ?int $ignoreId = null): string
    {
        $base = Str::slug(trim((string) ($slug ?: $fallback)));
        if ($base === '') {
            $base = 'categoria-portfolio';
        }

        $candidate = $base;
        $attempt = 2;

        while (
            PortfolioCategory::query()
                ->where('slug', $candidate)
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $candidate = $base.'-'.$attempt;
            $attempt++;
        }

        return $candidate;
    }

    private function normalizeColorHex(mixed $value): ?string
    {
        $color = $this->nullableTrim($value);

        if (! $color) {
            return null;
        }

        return '#'.ltrim((string) $color, '#');
    }

    private function nullableTrim(mixed $value): ?string
    {
        $trimmed = trim((string) ($value ?? ''));

        return $trimmed === '' ? null : $trimmed;
    }

    private function assertAdmin(): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
    }
}
