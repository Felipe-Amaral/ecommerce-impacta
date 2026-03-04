<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortfolioCategory;
use App\Models\PortfolioProject;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PortfolioProjectController extends Controller
{
    public function index(Request $request): View
    {
        $this->assertAdmin();

        $query = PortfolioProject::query()
            ->with(['category', 'author'])
            ->latest('updated_at');

        if ($request->filled('q')) {
            $term = trim((string) $request->query('q'));
            $query->where(function ($q) use ($term): void {
                $q->where('title', 'like', "%{$term}%")
                    ->orWhere('slug', 'like', "%{$term}%")
                    ->orWhere('client_name', 'like', "%{$term}%")
                    ->orWhere('industry', 'like', "%{$term}%")
                    ->orWhere('summary', 'like', "%{$term}%");
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

        return view('admin.portfolio.index', [
            'projects' => $query->paginate(18)->withQueryString(),
            'categories' => PortfolioCategory::query()->sorted()->get(),
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'status' => (string) $request->query('status', ''),
                'category_id' => (string) $request->query('category_id', ''),
                'featured' => (bool) $request->boolean('featured'),
            ],
            'stats' => [
                'total' => PortfolioProject::query()->count(),
                'published' => PortfolioProject::query()->where('status', 'published')->count(),
                'draft' => PortfolioProject::query()->where('status', 'draft')->count(),
                'scheduled' => PortfolioProject::query()->where('status', 'scheduled')->count(),
                'featured' => PortfolioProject::query()->where('is_featured', true)->count(),
            ],
        ]);
    }

    public function create(): View
    {
        $this->assertAdmin();

        return view('admin.portfolio.create', [
            'portfolioProject' => new PortfolioProject([
                'status' => 'draft',
                'is_featured' => false,
                'seo_noindex' => false,
                'project_year' => (int) now()->year,
            ]),
            'categories' => PortfolioCategory::query()->sorted()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->assertAdmin();

        $validated = $this->validateProject($request);
        $payload = $this->payloadFromValidated($request, $validated, null);

        $portfolioProject = PortfolioProject::query()->create($payload);

        return redirect()
            ->route('admin.portfolio.edit', $portfolioProject)
            ->with('success', 'Case de portfólio criado com sucesso.');
    }

    public function edit(PortfolioProject $portfolioProject): View
    {
        $this->assertAdmin();

        $portfolioProject->loadMissing(['category', 'author']);

        return view('admin.portfolio.edit', [
            'portfolioProject' => $portfolioProject,
            'categories' => PortfolioCategory::query()->sorted()->get(),
        ]);
    }

    public function update(Request $request, PortfolioProject $portfolioProject): RedirectResponse
    {
        $this->assertAdmin();

        $validated = $this->validateProject($request, $portfolioProject);
        $payload = $this->payloadFromValidated($request, $validated, $portfolioProject);

        $portfolioProject->update($payload);

        return redirect()->route('admin.portfolio.edit', $portfolioProject)->with('success', 'Case atualizado.');
    }

    public function destroy(PortfolioProject $portfolioProject): RedirectResponse
    {
        $this->assertAdmin();

        $this->deleteManagedCoverImage($portfolioProject->cover_image_url);
        $portfolioProject->delete();

        return redirect()->route('admin.portfolio.index')->with('success', 'Case removido.');
    }

    public function publish(PortfolioProject $portfolioProject): RedirectResponse
    {
        $this->assertAdmin();

        $portfolioProject->forceFill([
            'status' => 'published',
            'published_at' => $portfolioProject->published_at ?: now(),
        ])->save();

        return back()->with('success', 'Case publicado.');
    }

    public function draft(PortfolioProject $portfolioProject): RedirectResponse
    {
        $this->assertAdmin();

        $portfolioProject->forceFill([
            'status' => 'draft',
            'published_at' => null,
        ])->save();

        return back()->with('success', 'Case movido para rascunho.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateProject(Request $request, ?PortfolioProject $portfolioProject = null): array
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'integer', Rule::exists('portfolio_categories', 'id')],
            'title' => ['required', 'string', 'max:220'],
            'slug' => ['nullable', 'string', 'max:240'],
            'status' => ['required', 'string', Rule::in(['draft', 'published', 'scheduled'])],
            'client_name' => ['nullable', 'string', 'max:140'],
            'industry' => ['nullable', 'string', 'max:120'],
            'location' => ['nullable', 'string', 'max:120'],
            'project_year' => ['nullable', 'integer', 'min:1980', 'max:2100'],
            'project_url' => ['nullable', 'url', 'max:1200'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:8192'],
            'cover_image_url' => ['nullable', 'string', 'max:1200'],
            'remove_cover_image' => ['nullable', 'boolean'],
            'summary' => ['nullable', 'string', 'max:2500'],
            'challenge' => ['nullable', 'string', 'max:120000'],
            'solution' => ['nullable', 'string', 'max:120000'],
            'results' => ['nullable', 'string', 'max:120000'],
            'content' => ['nullable', 'string', 'max:120000'],
            'services_text' => ['nullable', 'string', 'max:12000'],
            'tools_text' => ['nullable', 'string', 'max:12000'],
            'metrics_text' => ['nullable', 'string', 'max:18000'],
            'gallery_text' => ['nullable', 'string', 'max:22000'],
            'is_featured' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:255'],
            'focus_keyword' => ['nullable', 'string', 'max:120'],
            'seo_canonical_url' => ['nullable', 'url', 'max:1200'],
            'seo_og_title' => ['nullable', 'string', 'max:255'],
            'seo_og_description' => ['nullable', 'string', 'max:255'],
            'seo_og_image_url' => ['nullable', 'string', 'max:1200'],
            'seo_noindex' => ['nullable', 'boolean'],
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
    private function payloadFromValidated(Request $request, array $data, ?PortfolioProject $existingProject): array
    {
        $coverImageUrl = $this->nullableTrim($data['cover_image_url'] ?? ($existingProject?->cover_image_url));

        if ((bool) ($data['remove_cover_image'] ?? false)) {
            $this->deleteManagedCoverImage($existingProject?->cover_image_url);
            $coverImageUrl = null;
        }

        if ($request->hasFile('cover_image')) {
            $this->deleteManagedCoverImage($existingProject?->cover_image_url);
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

        return [
            'category_id' => $data['category_id'] ?? null,
            'author_id' => $existingProject?->author_id ?: auth()->id(),
            'title' => trim((string) $data['title']),
            'slug' => $this->resolveUniqueSlug($data['slug'] ?? null, (string) $data['title'], $existingProject?->id),
            'status' => $status,
            'is_featured' => (bool) ($data['is_featured'] ?? false),
            'client_name' => $this->nullableTrim($data['client_name'] ?? null),
            'industry' => $this->nullableTrim($data['industry'] ?? null),
            'location' => $this->nullableTrim($data['location'] ?? null),
            'project_year' => isset($data['project_year']) ? (int) $data['project_year'] : null,
            'project_url' => $this->nullableTrim($data['project_url'] ?? null),
            'cover_image_url' => $coverImageUrl,
            'gallery_images' => $this->parseGalleryText((string) ($data['gallery_text'] ?? '')),
            'summary' => $this->nullableTrim($data['summary'] ?? null),
            'challenge' => $this->normalizeLongText($data['challenge'] ?? null),
            'solution' => $this->normalizeLongText($data['solution'] ?? null),
            'results' => $this->normalizeLongText($data['results'] ?? null),
            'metrics' => $this->parseMetricsText((string) ($data['metrics_text'] ?? '')),
            'services' => $this->parseSimpleList((string) ($data['services_text'] ?? '')),
            'tools' => $this->parseSimpleList((string) ($data['tools_text'] ?? '')),
            'content' => $this->normalizeLongText($data['content'] ?? null),
            'published_at' => $publishedAt,
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

    private function storeCoverImage(UploadedFile $file): string
    {
        return Storage::disk('public')->putFile('portfolio-covers', $file);
    }

    private function deleteManagedCoverImage(?string $value): void
    {
        $relativePath = $this->resolveManagedStoragePath($value);
        if (! $relativePath) {
            return;
        }

        Storage::disk('public')->delete($relativePath);
    }

    private function resolveManagedStoragePath(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        $trimmed = trim($value);
        if ($trimmed === '') {
            return null;
        }

        if (str_starts_with($trimmed, 'portfolio-covers/')) {
            return $trimmed;
        }

        $prefixes = [
            asset('storage').'/',
            url('/storage').'/',
            '/storage/',
            'storage/',
        ];

        foreach ($prefixes as $prefix) {
            if (str_starts_with($trimmed, $prefix)) {
                $relative = ltrim(substr($trimmed, strlen($prefix)), '/');

                return str_starts_with($relative, 'portfolio-covers/') ? $relative : null;
            }
        }

        return null;
    }

    /**
     * @return array<int, string>
     */
    private function parseSimpleList(string $value): array
    {
        return collect(preg_split('/\R/u', $value) ?: [])
            ->map(fn ($line) => trim((string) $line))
            ->filter(fn ($line) => $line !== '')
            ->unique()
            ->values()
            ->take(30)
            ->all();
    }

    /**
     * @return array<int, array{label: string, value: string, highlight: bool}>
     */
    private function parseMetricsText(string $value): array
    {
        return collect(preg_split('/\R/u', $value) ?: [])
            ->map(function ($line): ?array {
                $parts = array_map('trim', explode('|', (string) $line));
                $label = $parts[0] ?? '';
                $metricValue = $parts[1] ?? '';
                $highlightRaw = Str::lower((string) ($parts[2] ?? ''));

                if ($label === '' || $metricValue === '') {
                    return null;
                }

                return [
                    'label' => Str::limit($label, 90, ''),
                    'value' => Str::limit($metricValue, 90, ''),
                    'highlight' => in_array($highlightRaw, ['1', 'sim', 'yes', 'true', 'destaque', 'highlight'], true),
                ];
            })
            ->filter()
            ->values()
            ->take(24)
            ->all();
    }

    /**
     * @return array<int, array{url: string, alt: string, caption: string}>
     */
    private function parseGalleryText(string $value): array
    {
        return collect(preg_split('/\R/u', $value) ?: [])
            ->map(function ($line): ?array {
                $parts = array_map('trim', explode('|', (string) $line));
                $url = $parts[0] ?? '';
                $alt = $parts[1] ?? '';
                $caption = $parts[2] ?? '';

                if ($url === '') {
                    return null;
                }

                return [
                    'url' => Str::limit($url, 1200, ''),
                    'alt' => Str::limit($alt, 180, ''),
                    'caption' => Str::limit($caption, 220, ''),
                ];
            })
            ->filter()
            ->values()
            ->take(40)
            ->all();
    }

    private function resolveUniqueSlug(?string $slug, string $fallback, ?int $ignoreId = null): string
    {
        $base = Str::slug(trim((string) ($slug ?: $fallback)));
        if ($base === '') {
            $base = 'case-portfolio';
        }

        $candidate = $base;
        $attempt = 2;

        while (
            PortfolioProject::query()
                ->where('slug', $candidate)
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $candidate = $base.'-'.$attempt;
            $attempt++;
        }

        return $candidate;
    }

    private function normalizeLongText(mixed $value): ?string
    {
        $text = str_replace("\r\n", "\n", trim((string) ($value ?? '')));

        return $text === '' ? null : $text;
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
