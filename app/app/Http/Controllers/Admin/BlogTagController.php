<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogTag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BlogTagController extends Controller
{
    public function index(): View
    {
        $this->assertAdmin();

        return view('admin.blog.tags.index', [
            'tags' => BlogTag::query()->withCount('posts')->orderBy('name')->get(),
            'tag' => new BlogTag([
                'is_featured' => false,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->assertAdmin();

        $data = $this->validateTag($request);
        BlogTag::query()->create($this->payloadFromValidated($data));

        return redirect()->route('admin.blog.tags.index')->with('success', 'Tag criada.');
    }

    public function update(Request $request, BlogTag $blogTag): RedirectResponse
    {
        $this->assertAdmin();

        $data = $this->validateTag($request, $blogTag);
        $blogTag->update($this->payloadFromValidated($data, $blogTag));

        return redirect()->route('admin.blog.tags.index')->with('success', 'Tag atualizada.');
    }

    public function destroy(BlogTag $blogTag): RedirectResponse
    {
        $this->assertAdmin();

        $blogTag->delete();

        return redirect()->route('admin.blog.tags.index')->with('success', 'Tag removida.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateTag(Request $request, ?BlogTag $blogTag = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:160', Rule::unique('blog_tags', 'slug')->ignore($blogTag?->id)],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_featured' => ['nullable', 'boolean'],
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payloadFromValidated(array $data, ?BlogTag $existing = null): array
    {
        return [
            'name' => trim((string) $data['name']),
            'slug' => $this->resolveUniqueSlug($data['slug'] ?? null, (string) $data['name'], $existing?->id),
            'description' => $this->nullableTrim($data['description'] ?? null),
            'is_featured' => (bool) ($data['is_featured'] ?? false),
        ];
    }

    private function resolveUniqueSlug(?string $slug, string $fallback, ?int $ignoreId = null): string
    {
        $base = Str::slug(trim((string) ($slug ?: $fallback)));
        if ($base === '') {
            $base = 'tag-blog';
        }

        $candidate = $base;
        $attempt = 2;

        while (
            BlogTag::query()
                ->where('slug', $candidate)
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $candidate = $base.'-'.$attempt;
            $attempt++;
        }

        return $candidate;
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
