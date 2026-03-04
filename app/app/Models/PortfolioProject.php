<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PortfolioProject extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'author_id',
        'title',
        'slug',
        'status',
        'is_featured',
        'client_name',
        'industry',
        'location',
        'project_year',
        'project_url',
        'cover_image_url',
        'gallery_images',
        'summary',
        'challenge',
        'solution',
        'results',
        'metrics',
        'services',
        'tools',
        'content',
        'published_at',
        'views_count',
        'seo_title',
        'seo_description',
        'focus_keyword',
        'seo_canonical_url',
        'seo_og_title',
        'seo_og_description',
        'seo_og_image_url',
        'seo_noindex',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'project_year' => 'integer',
            'gallery_images' => 'array',
            'metrics' => 'array',
            'services' => 'array',
            'tools' => 'array',
            'published_at' => 'datetime',
            'views_count' => 'integer',
            'seo_noindex' => 'boolean',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PortfolioCategory::class, 'category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function isVisibleToPublic(): bool
    {
        if ($this->status !== 'published' || ! $this->published_at) {
            return false;
        }

        return $this->published_at->lte(now());
    }

    public function seoTitle(): string
    {
        return trim((string) ($this->seo_title ?: ($this->title.' | Portfólio Uriah Criativa')));
    }

    public function seoDescription(): string
    {
        $fallback = $this->summary ?: 'Case real do portfólio da Uriah Criativa com estratégia, produção e resultados gráficos.';

        return trim((string) ($this->seo_description ?: $fallback));
    }

    public function ogTitle(): string
    {
        return trim((string) ($this->seo_og_title ?: $this->seoTitle()));
    }

    public function ogDescription(): string
    {
        return trim((string) ($this->seo_og_description ?: $this->seoDescription()));
    }

    public function ogImage(): ?string
    {
        $image = trim((string) ($this->seo_og_image_url ?: $this->cover_image_url));

        return $image !== '' ? $image : null;
    }

    /**
     * @return array<int, string>
     */
    public function serviceItems(): array
    {
        return collect((array) $this->services)
            ->map(fn ($item) => trim((string) $item))
            ->filter(fn ($item) => $item !== '')
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    public function toolItems(): array
    {
        return collect((array) $this->tools)
            ->map(fn ($item) => trim((string) $item))
            ->filter(fn ($item) => $item !== '')
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{label: string, value: string, highlight: bool}>
     */
    public function metricItems(): array
    {
        return collect((array) $this->metrics)
            ->map(function ($item): ?array {
                $label = trim((string) data_get($item, 'label', ''));
                $value = trim((string) data_get($item, 'value', ''));
                $highlight = (bool) data_get($item, 'highlight', false);

                if ($label === '' || $value === '') {
                    return null;
                }

                return [
                    'label' => $label,
                    'value' => $value,
                    'highlight' => $highlight,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{url: string, alt: string, caption: string}>
     */
    public function galleryItems(): array
    {
        return collect((array) $this->gallery_images)
            ->map(function ($item): ?array {
                $url = trim((string) data_get($item, 'url', ''));
                $alt = trim((string) data_get($item, 'alt', ''));
                $caption = trim((string) data_get($item, 'caption', ''));

                if ($url === '') {
                    return null;
                }

                if ($alt === '') {
                    $alt = $caption !== '' ? $caption : $this->title;
                }

                return [
                    'url' => $url,
                    'alt' => Str::limit($alt, 180, ''),
                    'caption' => $caption,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }
}
