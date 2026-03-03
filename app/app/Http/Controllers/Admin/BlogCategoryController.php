<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BlogCategoryController extends Controller
{
    public function index(): View
    {
        $this->assertAdmin();

        return view('admin.blog.categories.index', [
            'categories' => BlogCategory::query()
                ->withCount('posts')
                ->sorted()
                ->get(),
            'category' => new BlogCategory([
                'is_active' => true,
                'sort_order' => (int) (BlogCategory::query()->max('sort_order') ?? 0) + 1,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->assertAdmin();

        $data = $this->validateCategory($request);
        BlogCategory::query()->create($this->payloadFromValidated($data));

        return redirect()->route('admin.blog.categories.index')->with('success', 'Categoria criada.');
    }

    public function update(Request $request, BlogCategory $blogCategory): RedirectResponse
    {
        $this->assertAdmin();

        $data = $this->validateCategory($request, $blogCategory);
        $blogCategory->update($this->payloadFromValidated($data, $blogCategory));

        return redirect()->route('admin.blog.categories.index')->with('success', 'Categoria atualizada.');
    }

    public function destroy(BlogCategory $blogCategory): RedirectResponse
    {
        $this->assertAdmin();

        $blogCategory->delete();

        return redirect()->route('admin.blog.categories.index')->with('success', 'Categoria removida.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateCategory(Request $request, ?BlogCategory $blogCategory = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:160', Rule::unique('blog_categories', 'slug')->ignore($blogCategory?->id)],
            'description' => ['nullable', 'string', 'max:2000'],
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
    private function payloadFromValidated(array $data, ?BlogCategory $existing = null): array
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
            $base = 'categoria-blog';
        }

        $candidate = $base;
        $attempt = 2;

        while (
            BlogCategory::query()
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
