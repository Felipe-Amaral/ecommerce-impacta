<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeBanner;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class HomeBannerController extends Controller
{
    public function index(): View
    {
        $this->assertAdmin();

        return view('admin.catalog.banners.index', [
            'banners' => HomeBanner::query()->sorted()->get(),
            'banner' => new HomeBanner([
                'theme' => 'gold',
                'is_active' => true,
                'sort_order' => (int) (HomeBanner::query()->max('sort_order') ?? 0) + 1,
                'metadata' => ['text_side' => 'left'],
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->assertAdmin();

        $validated = $this->validateBanner($request);

        HomeBanner::query()->create($this->payloadFromValidated($request, $validated));

        return redirect()->route('admin.catalog.banners.index')->with('success', 'Banner cadastrado.');
    }

    public function edit(HomeBanner $banner): View
    {
        $this->assertAdmin();

        return view('admin.catalog.banners.edit', compact('banner'));
    }

    public function update(Request $request, HomeBanner $banner): RedirectResponse
    {
        $this->assertAdmin();

        $validated = $this->validateBanner($request);

        $banner->update($this->payloadFromValidated($request, $validated, $banner));

        return redirect()->route('admin.catalog.banners.index')->with('success', 'Banner atualizado.');
    }

    public function toggleActive(HomeBanner $banner): RedirectResponse
    {
        $this->assertAdmin();

        $banner->forceFill([
            'is_active' => ! $banner->is_active,
        ])->save();

        return back()->with('success', $banner->is_active ? 'Banner ativado.' : 'Banner desativado.');
    }

    public function destroy(HomeBanner $banner): RedirectResponse
    {
        $this->assertAdmin();

        $this->deleteManagedBannerImage($banner->background_image_url);
        $banner->delete();

        return redirect()->route('admin.catalog.banners.index')->with('success', 'Banner removido.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateBanner(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:140'],
            'badge' => ['nullable', 'string', 'max:80'],
            'headline' => ['required', 'string', 'max:220'],
            'subheadline' => ['nullable', 'string', 'max:220'],
            'description' => ['nullable', 'string', 'max:4000'],
            'cta_label' => ['nullable', 'string', 'max:80'],
            'cta_url' => ['nullable', 'string', 'max:500'],
            'secondary_cta_label' => ['nullable', 'string', 'max:80'],
            'secondary_cta_url' => ['nullable', 'string', 'max:500'],
            'theme' => ['required', 'string', Rule::in(['gold', 'obsidian', 'ivory'])],
            'text_side' => ['nullable', 'string', Rule::in(['left', 'right'])],
            'background_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:6144'],
            'background_image_url' => ['nullable', 'string', 'max:1000'],
            'remove_background_image' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['nullable', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payloadFromValidated(Request $request, array $data, ?HomeBanner $existingBanner = null): array
    {
        $backgroundImageUrl = $this->normalizePublicStorageUrl(
            $this->nullableTrim($data['background_image_url'] ?? null)
        );

        if ((bool) ($data['remove_background_image'] ?? false)) {
            $this->deleteManagedBannerImage($existingBanner?->background_image_url);
            $backgroundImageUrl = null;
        }

        if ($request->hasFile('background_image')) {
            $this->deleteManagedBannerImage($existingBanner?->background_image_url);
            $backgroundImageUrl = $this->storeBannerImage($request->file('background_image'));
        }

        $metadata = (array) ($existingBanner?->metadata ?? []);
        $metadata['text_side'] = (string) ($data['text_side'] ?? ($metadata['text_side'] ?? 'left'));

        return [
            'name' => trim((string) $data['name']),
            'badge' => $this->nullableTrim($data['badge'] ?? null),
            'headline' => trim((string) $data['headline']),
            'subheadline' => $this->nullableTrim($data['subheadline'] ?? null),
            'description' => $this->nullableTrim($data['description'] ?? null),
            'cta_label' => $this->nullableTrim($data['cta_label'] ?? null),
            'cta_url' => $this->nullableTrim($data['cta_url'] ?? null),
            'secondary_cta_label' => $this->nullableTrim($data['secondary_cta_label'] ?? null),
            'secondary_cta_url' => $this->nullableTrim($data['secondary_cta_url'] ?? null),
            'theme' => (string) ($data['theme'] ?? 'gold'),
            'background_image_url' => $backgroundImageUrl,
            'metadata' => $metadata,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => (bool) ($data['is_active'] ?? false),
            'starts_at' => ! empty($data['starts_at']) ? $data['starts_at'] : null,
            'ends_at' => ! empty($data['ends_at']) ? $data['ends_at'] : null,
        ];
    }

    private function nullableTrim(mixed $value): ?string
    {
        $trimmed = trim((string) ($value ?? ''));

        return $trimmed === '' ? null : $trimmed;
    }

    private function storeBannerImage(UploadedFile $file): string
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeBaseName = Str::slug($originalName);
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        $filename = now()->format('Ymd_His')
            .'-'.Str::limit($safeBaseName !== '' ? $safeBaseName : 'banner-home', 44, '')
            .'-'.Str::lower(Str::random(6))
            .'.'.$extension;

        $path = $file->storeAs('home-banners', $filename, 'public');

        return '/storage/'.$path;
    }

    private function deleteManagedBannerImage(?string $imageUrl): void
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

            return str_starts_with($relative, 'home-banners/') ? $relative : null;
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
