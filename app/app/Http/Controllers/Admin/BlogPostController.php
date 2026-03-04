<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BlogPostController extends Controller
{
    public function index(Request $request): View
    {
        $this->assertAdmin();

        $query = BlogPost::query()
            ->with(['category', 'tags', 'author'])
            ->latest('updated_at');

        if ($request->filled('q')) {
            $term = trim((string) $request->query('q'));

            $query->where(function ($q) use ($term): void {
                $q->where('title', 'like', "%{$term}%")
                    ->orWhere('slug', 'like', "%{$term}%")
                    ->orWhere('excerpt', 'like', "%{$term}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', (string) $request->query('status'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', (int) $request->query('category_id'));
        }

        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        return view('admin.blog.index', [
            'posts' => $query->paginate(18)->withQueryString(),
            'categories' => BlogCategory::query()->sorted()->get(),
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'status' => (string) $request->query('status', ''),
                'category_id' => (string) $request->query('category_id', ''),
                'featured' => (bool) $request->boolean('featured'),
            ],
            'stats' => [
                'total' => BlogPost::query()->count(),
                'published' => BlogPost::query()->where('status', 'published')->count(),
                'draft' => BlogPost::query()->where('status', 'draft')->count(),
                'scheduled' => BlogPost::query()->where('status', 'scheduled')->count(),
            ],
        ]);
    }

    public function create(): View
    {
        $this->assertAdmin();

        return view('admin.blog.create', [
            'blogPost' => new BlogPost([
                'status' => 'draft',
                'is_featured' => false,
                'seo_noindex' => false,
            ]),
            'categories' => BlogCategory::query()->sorted()->get(),
            'tags' => BlogTag::query()->orderBy('name')->get(),
            'selectedTagIds' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->assertAdmin();

        $validated = $this->validatePost($request);
        $payload = $this->payloadFromValidated($request, $validated, null);

        $blogPost = BlogPost::query()->create($payload);
        $this->syncTags($blogPost, $validated);

        return redirect()
            ->route('admin.blog.edit', $blogPost)
            ->with('success', 'Post criado com sucesso.');
    }

    public function edit(BlogPost $blogPost): View
    {
        $this->assertAdmin();

        $blogPost->loadMissing(['category', 'tags', 'author']);

        return view('admin.blog.edit', [
            'blogPost' => $blogPost,
            'categories' => BlogCategory::query()->sorted()->get(),
            'tags' => BlogTag::query()->orderBy('name')->get(),
            'selectedTagIds' => $blogPost->tags->pluck('id')->map(fn ($id) => (int) $id)->all(),
        ]);
    }

    public function update(Request $request, BlogPost $blogPost): RedirectResponse
    {
        $this->assertAdmin();

        $validated = $this->validatePost($request, $blogPost);
        $payload = $this->payloadFromValidated($request, $validated, $blogPost);

        $blogPost->update($payload);
        $this->syncTags($blogPost, $validated);

        return redirect()->route('admin.blog.edit', $blogPost)->with('success', 'Post atualizado.');
    }

    public function destroy(BlogPost $blogPost): RedirectResponse
    {
        $this->assertAdmin();

        $this->deleteManagedCoverImage($blogPost->cover_image_url);
        $blogPost->tags()->detach();
        $blogPost->delete();

        return redirect()->route('admin.blog.index')->with('success', 'Post removido.');
    }

    public function publish(BlogPost $blogPost): RedirectResponse
    {
        $this->assertAdmin();

        $blogPost->forceFill([
            'status' => 'published',
            'published_at' => $blogPost->published_at ?: now(),
        ])->save();

        return back()->with('success', 'Post publicado.');
    }

    public function draft(BlogPost $blogPost): RedirectResponse
    {
        $this->assertAdmin();

        $blogPost->forceFill([
            'status' => 'draft',
            'published_at' => null,
        ])->save();

        return back()->with('success', 'Post movido para rascunho.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePost(Request $request, ?BlogPost $blogPost = null): array
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'integer', Rule::exists('blog_categories', 'id')],
            'title' => ['required', 'string', 'max:220'],
            'slug' => ['nullable', 'string', 'max:240'],
            'status' => ['required', 'string', Rule::in(['draft', 'published', 'scheduled'])],
            'excerpt' => ['nullable', 'string', 'max:1200'],
            'content' => ['required', 'string', 'max:120000'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:8192'],
            'cover_image_url' => ['nullable', 'string', 'max:1200'],
            'remove_cover_image' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'reading_time_minutes' => ['nullable', 'integer', 'min:1', 'max:120'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:255'],
            'focus_keyword' => ['nullable', 'string', 'max:120'],
            'seo_canonical_url' => ['nullable', 'url', 'max:1200'],
            'seo_og_title' => ['nullable', 'string', 'max:255'],
            'seo_og_description' => ['nullable', 'string', 'max:255'],
            'seo_og_image_url' => ['nullable', 'string', 'max:1200'],
            'seo_noindex' => ['nullable', 'boolean'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', Rule::exists('blog_tags', 'id')],
            'new_tags' => ['nullable', 'string', 'max:1200'],
        ]);

        if ((string) $data['status'] === 'scheduled') {
            if (empty($data['published_at'])) {
                throw ValidationException::withMessages([
                    'published_at' => 'Informe data/hora de publicação para agendamento.',
                ]);
            }

            if (Carbon::parse((string) $data['published_at'])->lte(now())) {
                throw ValidationException::withMessages([
                    'published_at' => 'A data agendada deve ser futura.',
                ]);
            }
        }

        if ((string) $data['status'] === 'published' && ! empty($data['published_at'])) {
            if (Carbon::parse((string) $data['published_at'])->gt(now())) {
                throw ValidationException::withMessages([
                    'published_at' => 'Para data futura, use o status "Agendado".',
                ]);
            }
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payloadFromValidated(Request $request, array $data, ?BlogPost $existingPost): array
    {
        $coverImageUrl = $this->normalizePublicStorageUrl(
            $this->nullableTrim($data['cover_image_url'] ?? ($existingPost?->cover_image_url))
        );

        if ((bool) ($data['remove_cover_image'] ?? false)) {
            $this->deleteManagedCoverImage($existingPost?->cover_image_url);
            $coverImageUrl = null;
        }

        if ($request->hasFile('cover_image')) {
            $this->deleteManagedCoverImage($existingPost?->cover_image_url);
            $coverImageUrl = $this->storeCoverImage($request->file('cover_image'));
        }

        $status = (string) ($data['status'] ?? 'draft');
        $publishedAt = match ($status) {
            'published' => ! empty($data['published_at'])
                ? Carbon::parse((string) $data['published_at'])
                : now(),
            'scheduled' => Carbon::parse((string) $data['published_at']),
            default => null,
        };

        $content = $this->normalizeContent((string) $data['content']);
        $readingTime = (int) ($data['reading_time_minutes'] ?? 0);
        if ($readingTime <= 0) {
            $readingTime = $this->estimateReadingTime($content);
        }

        return [
            'category_id' => $data['category_id'] ?? null,
            'author_id' => $existingPost?->author_id ?: auth()->id(),
            'title' => trim((string) $data['title']),
            'slug' => $this->resolveUniqueSlug($data['slug'] ?? null, (string) $data['title'], $existingPost?->id),
            'status' => $status,
            'excerpt' => $this->nullableTrim($data['excerpt'] ?? null),
            'content' => $content,
            'cover_image_url' => $coverImageUrl,
            'is_featured' => (bool) ($data['is_featured'] ?? false),
            'published_at' => $publishedAt,
            'reading_time_minutes' => $readingTime,
            'seo_title' => $this->nullableTrim($data['seo_title'] ?? null),
            'seo_description' => $this->nullableTrim($data['seo_description'] ?? null),
            'focus_keyword' => $this->nullableTrim($data['focus_keyword'] ?? null),
            'seo_canonical_url' => $this->nullableTrim($data['seo_canonical_url'] ?? null),
            'seo_og_title' => $this->nullableTrim($data['seo_og_title'] ?? null),
            'seo_og_description' => $this->nullableTrim($data['seo_og_description'] ?? null),
            'seo_og_image_url' => $this->nullableTrim($data['seo_og_image_url'] ?? null),
            'seo_noindex' => (bool) ($data['seo_noindex'] ?? false),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function syncTags(BlogPost $blogPost, array $data): void
    {
        $tagIds = collect($data['tag_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->values();

        $newTagNames = collect(explode(',', (string) ($data['new_tags'] ?? '')))
            ->map(fn (string $name) => trim($name))
            ->filter();

        foreach ($newTagNames as $name) {
            $slug = Str::slug((string) $name);
            if ($slug === '') {
                continue;
            }

            $tag = BlogTag::query()->firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => Str::title((string) $name),
                    'description' => null,
                    'is_featured' => false,
                ],
            );

            $tagIds->push($tag->id);
        }

        $blogPost->tags()->sync($tagIds->unique()->values()->all());
    }

    private function nullableTrim(mixed $value): ?string
    {
        $trimmed = trim((string) ($value ?? ''));

        return $trimmed === '' ? null : $trimmed;
    }

    private function normalizeContent(string $content): string
    {
        return trim(str_replace(["\r\n", "\r"], "\n", $content));
    }

    private function estimateReadingTime(string $content): int
    {
        $normalized = preg_replace('/[#*_`>\-\[\]()]/', ' ', $content) ?? $content;
        $words = str_word_count(strip_tags($normalized));

        return max(1, (int) ceil($words / 210));
    }

    private function resolveUniqueSlug(?string $slug, string $fallback, ?int $ignoreId = null): string
    {
        $base = Str::slug(trim((string) ($slug ?: $fallback)));
        if ($base === '') {
            $base = 'post-blog';
        }

        $candidate = $base;
        $attempt = 2;

        while (
            BlogPost::query()
                ->where('slug', $candidate)
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $candidate = $base.'-'.$attempt;
            $attempt++;
        }

        return $candidate;
    }

    private function storeCoverImage(UploadedFile $file): string
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeBaseName = Str::slug($originalName);
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        $filename = now()->format('Ymd_His')
            .'-'.Str::limit($safeBaseName !== '' ? $safeBaseName : 'blog-post', 60, '')
            .'-'.Str::lower(Str::random(6))
            .'.'.$extension;

        $path = $file->storeAs('blog-posts', $filename, 'public');

        return '/storage/'.$path;
    }

    private function deleteManagedCoverImage(?string $imageUrl): void
    {
        $relativePath = $this->managedPublicPathFromImageUrl($imageUrl);

        if (! $relativePath) {
            return;
        }

        Storage::disk('public')->delete($relativePath);
    }

    private function managedPublicPathFromImageUrl(?string $imageUrl): ?string
    {
        $value = trim((string) $imageUrl);

        if ($value === '') {
            return null;
        }

        $path = parse_url($value, PHP_URL_PATH);
        $candidate = is_string($path) && $path !== '' ? $path : $value;

        if (str_starts_with($candidate, '/storage/')) {
            $relative = ltrim(Str::after($candidate, '/storage/'), '/');

            return str_starts_with($relative, 'blog-posts/') ? $relative : null;
        }

        return null;
    }

    private function normalizePublicStorageUrl(?string $imageUrl): ?string
    {
        $value = trim((string) $imageUrl);

        if ($value === '') {
            return null;
        }

        $path = parse_url($value, PHP_URL_PATH);
        $candidate = is_string($path) && $path !== '' ? $path : $value;

        if (str_starts_with($candidate, '/storage/')) {
            return $candidate;
        }

        return $value;
    }

    private function assertAdmin(): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
    }
}
