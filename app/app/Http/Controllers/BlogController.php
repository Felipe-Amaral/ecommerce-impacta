<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        return $this->renderIndex($request);
    }

    public function category(Request $request, BlogCategory $blogCategory): View
    {
        abort_unless($blogCategory->is_active, 404);

        return $this->renderIndex($request, $blogCategory, null);
    }

    public function tag(Request $request, BlogTag $blogTag): View
    {
        return $this->renderIndex($request, null, $blogTag);
    }

    public function show(BlogPost $blogPost): View
    {
        $isAdminPreview = (bool) auth()->user()?->is_admin;

        if (! $blogPost->isVisibleToPublic() && ! $isAdminPreview) {
            abort(404);
        }

        $blogPost->loadMissing(['category', 'tags', 'author']);

        if ($blogPost->isVisibleToPublic()) {
            $blogPost->increment('views_count');
            $blogPost->refresh();
            $blogPost->loadMissing(['category', 'tags', 'author']);
        }

        $tagIds = $blogPost->tags->pluck('id')->values();

        $relatedPosts = BlogPost::query()
            ->published()
            ->with(['category', 'tags'])
            ->whereKeyNot($blogPost->id)
            ->when($blogPost->category_id || $tagIds->isNotEmpty(), function (Builder $query) use ($blogPost, $tagIds): void {
                $query->where(function (Builder $inner) use ($blogPost, $tagIds): void {
                    if ($blogPost->category_id && $tagIds->isNotEmpty()) {
                        $inner->where('category_id', $blogPost->category_id)
                            ->orWhereHas('tags', fn (Builder $tagQuery) => $tagQuery->whereIn('blog_tags.id', $tagIds));
                        return;
                    }

                    if ($blogPost->category_id) {
                        $inner->where('category_id', $blogPost->category_id);
                        return;
                    }

                    if ($tagIds->isNotEmpty()) {
                        $inner->whereHas('tags', fn (Builder $tagQuery) => $tagQuery->whereIn('blog_tags.id', $tagIds));
                    }
                });
            })
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        $canonical = trim((string) ($blogPost->seo_canonical_url ?: route('blog.show', $blogPost->slug)));
        $metaRobots = $blogPost->seo_noindex
            ? 'noindex, nofollow, noarchive'
            : 'index, follow, max-image-preview:large, max-snippet:-1';

        $metaTitle = $blogPost->seoTitle();
        $metaDescription = $blogPost->seoDescription();
        $ogImage = $blogPost->ogImage() ?: asset('favicon.svg?v=uriah2');

        $breadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Início',
                    'item' => route('home'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => 'Blog',
                    'item' => route('blog.index'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => $blogPost->title,
                    'item' => $canonical,
                ],
            ],
        ];

        $blogPostingSchema = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $blogPost->title,
            'description' => strip_tags($metaDescription),
            'articleSection' => $blogPost->category?->name,
            'datePublished' => optional($blogPost->published_at)->toIso8601String(),
            'dateModified' => optional($blogPost->updated_at)->toIso8601String(),
            'author' => [
                '@type' => 'Person',
                'name' => $blogPost->author?->name ?: 'Equipe Uriah Criativa',
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Uriah Criativa',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('favicon.svg?v=uriah2'),
                ],
            ],
            'mainEntityOfPage' => $canonical,
            'image' => [$ogImage],
            'keywords' => collect($blogPost->tags)->pluck('name')->implode(', '),
            'wordCount' => str_word_count(strip_tags((string) $blogPost->content)),
            'timeRequired' => 'PT'.max(1, (int) $blogPost->reading_time_minutes).'M',
        ], fn ($value) => ! in_array($value, ['', null, []], true));

        $articleHtml = Str::markdown((string) $blogPost->content, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        return view('store.blog.show', [
            'blogPost' => $blogPost,
            'relatedPosts' => $relatedPosts,
            'isAdminPreview' => $isAdminPreview && ! $blogPost->isVisibleToPublic(),
            'articleHtml' => $articleHtml,
            'metaTitle' => $metaTitle,
            'metaDescription' => $metaDescription,
            'canonical' => $canonical,
            'metaRobots' => $metaRobots,
            'ogImage' => $ogImage,
            'breadcrumbSchema' => $breadcrumbSchema,
            'blogPostingSchema' => $blogPostingSchema,
        ]);
    }

    private function renderIndex(Request $request, ?BlogCategory $forcedCategory = null, ?BlogTag $forcedTag = null): View
    {
        $search = trim((string) $request->string('q'));

        $activeCategory = $forcedCategory;
        $activeTag = $forcedTag;

        if (! $activeCategory) {
            $categorySlug = trim((string) $request->string('categoria'));
            if ($categorySlug !== '') {
                $activeCategory = BlogCategory::query()->active()->where('slug', $categorySlug)->first();
            }
        }

        if (! $activeTag) {
            $tagSlug = trim((string) $request->string('tag'));
            if ($tagSlug !== '') {
                $activeTag = BlogTag::query()->where('slug', $tagSlug)->first();
            }
        }

        $postsQuery = BlogPost::query()
            ->published()
            ->with(['category', 'tags', 'author'])
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $inner) use ($search): void {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('excerpt', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->when($activeCategory, fn (Builder $query) => $query->where('category_id', $activeCategory->id))
            ->when($activeTag, fn (Builder $query) => $query->whereHas('tags', fn (Builder $tagQuery) => $tagQuery->where('blog_tags.id', $activeTag->id)))
            ->orderByDesc('published_at');

        $currentPage = max(1, (int) $request->query('page', 1));
        $featuredPost = null;

        if ($currentPage === 1) {
            if ($search === '' && ! $activeCategory && ! $activeTag) {
                $featuredPost = BlogPost::query()
                    ->published()
                    ->with(['category', 'tags', 'author'])
                    ->featured()
                    ->orderByDesc('published_at')
                    ->first();
            }

            $featuredPost ??= (clone $postsQuery)->first();

            if ($featuredPost) {
                $postsQuery->whereKeyNot($featuredPost->id);
            }
        }

        $posts = $postsQuery->paginate(9)->withQueryString();

        $categories = BlogCategory::query()
            ->active()
            ->sorted()
            ->withCount([
                'posts as published_posts_count' => fn (Builder $query) => $query->published(),
            ])
            ->get();

        $popularTags = BlogTag::query()
            ->whereHas('posts', fn (Builder $query) => $query->published())
            ->withCount([
                'posts as published_posts_count' => fn (Builder $query) => $query->published(),
            ])
            ->orderByDesc('published_posts_count')
            ->orderBy('name')
            ->take(14)
            ->get();

        $spotlightPosts = BlogPost::query()
            ->published()
            ->with('category')
            ->when($featuredPost, fn (Builder $query) => $query->whereKeyNot($featuredPost->id))
            ->orderByDesc('views_count')
            ->orderByDesc('published_at')
            ->take(4)
            ->get();

        $metaTitle = match (true) {
            $activeCategory !== null => ($activeCategory->seo_title ?: $activeCategory->name).' | Blog Uriah Criativa',
            $activeTag !== null => 'Tag: '.$activeTag->name.' | Blog Uriah Criativa',
            $search !== '' => 'Busca no Blog: '.$search.' | Uriah Criativa',
            default => 'Blog da Uriah Criativa | Dicas de Impressão e Marketing Gráfico',
        };

        $metaDescription = match (true) {
            $activeCategory !== null && ! empty($activeCategory->seo_description) => (string) $activeCategory->seo_description,
            $activeCategory !== null => 'Conteúdos sobre '.$activeCategory->name.' com dicas práticas para melhorar materiais gráficos e resultados comerciais.',
            $activeTag !== null => 'Conteúdos relacionados à tag '.$activeTag->name.' no blog da Uriah Criativa.',
            $search !== '' => 'Resultados da busca no blog para "'.$search.'".',
            default => 'Conteúdo estratégico sobre impressão, pré-impressão, acabamentos e materiais para fortalecer o posicionamento da sua marca.',
        };

        $canonical = match (true) {
            $forcedCategory !== null => route('blog.category', $forcedCategory->slug),
            $forcedTag !== null => route('blog.tag', $forcedTag->slug),
            $activeCategory !== null && $search === '' && $activeTag === null => route('blog.category', $activeCategory->slug),
            $activeTag !== null && $search === '' && $activeCategory === null => route('blog.tag', $activeTag->slug),
            default => route('blog.index'),
        };

        $metaRobots = $search !== '' ? 'noindex, follow' : 'index, follow, max-image-preview:large';
        $ogImage = $featuredPost?->ogImage() ?: asset('favicon.svg?v=uriah2');

        $breadcrumbItems = [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Início',
                'item' => route('home'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => 'Blog',
                'item' => route('blog.index'),
            ],
        ];

        if ($activeCategory) {
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $activeCategory->name,
                'item' => route('blog.category', $activeCategory->slug),
            ];
        }

        if ($activeTag) {
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => $activeCategory ? 4 : 3,
                'name' => '#'.$activeTag->name,
                'item' => route('blog.tag', $activeTag->slug),
            ];
        }

        $breadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbItems,
        ];

        $collectionSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $metaTitle,
            'description' => $metaDescription,
            'url' => $canonical,
            'inLanguage' => 'pt-BR',
            'mainEntity' => [
                '@type' => 'ItemList',
                'numberOfItems' => ($featuredPost ? 1 : 0) + $posts->count(),
                'itemListElement' => collect([$featuredPost])
                    ->filter()
                    ->concat($posts->getCollection())
                    ->values()
                    ->map(function (BlogPost $post, int $index): array {
                        return [
                            '@type' => 'ListItem',
                            'position' => $index + 1,
                            'name' => $post->title,
                            'url' => route('blog.show', $post->slug),
                        ];
                    })
                    ->all(),
            ],
        ];

        return view('store.blog.index', [
            'featuredPost' => $featuredPost,
            'posts' => $posts,
            'categories' => $categories,
            'popularTags' => $popularTags,
            'spotlightPosts' => $spotlightPosts,
            'search' => $search,
            'activeCategory' => $activeCategory,
            'activeTag' => $activeTag,
            'metaTitle' => $metaTitle,
            'metaDescription' => $metaDescription,
            'canonical' => $canonical,
            'metaRobots' => $metaRobots,
            'ogImage' => $ogImage,
            'breadcrumbSchema' => $breadcrumbSchema,
            'collectionSchema' => $collectionSchema,
        ]);
    }
}
