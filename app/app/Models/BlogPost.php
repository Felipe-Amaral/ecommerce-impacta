<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogPost extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'author_id',
        'title',
        'slug',
        'status',
        'excerpt',
        'content',
        'cover_image_url',
        'is_featured',
        'published_at',
        'reading_time_minutes',
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
            'published_at' => 'datetime',
            'reading_time_minutes' => 'integer',
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
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(BlogTag::class, 'blog_post_tag')->withTimestamps();
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
        return trim((string) ($this->seo_title ?: ($this->title.' | Blog Uriah Criativa')));
    }

    public function seoDescription(): string
    {
        $fallback = $this->excerpt ?: 'Artigo do blog da Uriah Criativa sobre impressão, acabamento e materiais gráficos.';

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
}
