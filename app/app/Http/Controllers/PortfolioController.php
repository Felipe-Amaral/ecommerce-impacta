<?php

namespace App\Http\Controllers;

use App\Models\PortfolioCategory;
use App\Models\PortfolioProject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function index(Request $request): View
    {
        return $this->renderIndex($request);
    }

    public function category(Request $request, PortfolioCategory $portfolioCategory): View
    {
        abort_unless($portfolioCategory->is_active, 404);

        return $this->renderIndex($request, $portfolioCategory);
    }

    public function show(PortfolioProject $portfolioProject): View
    {
        $isAdminPreview = (bool) auth()->user()?->is_admin;

        if (! $portfolioProject->isVisibleToPublic() && ! $isAdminPreview) {
            abort(404);
        }

        $portfolioProject->loadMissing(['category', 'author']);

        if ($portfolioProject->isVisibleToPublic()) {
            $portfolioProject->increment('views_count');
            $portfolioProject->refresh();
            $portfolioProject->loadMissing(['category', 'author']);
        }

        $relatedProjects = PortfolioProject::query()
            ->published()
            ->with('category')
            ->whereKeyNot($portfolioProject->id)
            ->when($portfolioProject->category_id, fn (Builder $query) => $query->where('category_id', $portfolioProject->category_id))
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        $canonical = trim((string) ($portfolioProject->seo_canonical_url ?: route('portfolio.show', $portfolioProject->slug)));
        $metaRobots = $portfolioProject->seo_noindex
            ? 'noindex, nofollow, noarchive'
            : 'index, follow, max-image-preview:large, max-snippet:-1';

        $metaTitle = $portfolioProject->seoTitle();
        $metaDescription = $portfolioProject->seoDescription();
        $ogImage = $portfolioProject->ogImage() ?: asset('favicon.svg?v=uriah2');

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
                    'name' => 'Portfólio',
                    'item' => route('pages.portfolio'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => $portfolioProject->title,
                    'item' => $canonical,
                ],
            ],
        ];

        $caseStudySchema = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'CaseStudy',
            'name' => $portfolioProject->title,
            'description' => strip_tags($metaDescription),
            'url' => $canonical,
            'datePublished' => optional($portfolioProject->published_at)->toIso8601String(),
            'dateModified' => optional($portfolioProject->updated_at)->toIso8601String(),
            'inLanguage' => 'pt-BR',
            'about' => $portfolioProject->category?->name,
            'creator' => [
                '@type' => 'Organization',
                'name' => 'Uriah Criativa',
            ],
            'image' => [$ogImage],
            'keywords' => implode(', ', $portfolioProject->serviceItems()),
        ], fn ($value) => ! in_array($value, ['', null, []], true));

        $challengeHtml = Str::markdown((string) $portfolioProject->challenge, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        $solutionHtml = Str::markdown((string) $portfolioProject->solution, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        $resultsHtml = Str::markdown((string) $portfolioProject->results, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        $contentHtml = Str::markdown((string) $portfolioProject->content, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        return view('store.portfolio.show', [
            'portfolioProject' => $portfolioProject,
            'relatedProjects' => $relatedProjects,
            'isAdminPreview' => $isAdminPreview && ! $portfolioProject->isVisibleToPublic(),
            'challengeHtml' => $challengeHtml,
            'solutionHtml' => $solutionHtml,
            'resultsHtml' => $resultsHtml,
            'contentHtml' => $contentHtml,
            'metaTitle' => $metaTitle,
            'metaDescription' => $metaDescription,
            'canonical' => $canonical,
            'metaRobots' => $metaRobots,
            'ogImage' => $ogImage,
            'breadcrumbSchema' => $breadcrumbSchema,
            'caseStudySchema' => $caseStudySchema,
        ]);
    }

    private function renderIndex(Request $request, ?PortfolioCategory $forcedCategory = null): View
    {
        $search = trim((string) $request->string('q'));

        $activeCategory = $forcedCategory;
        if (! $activeCategory) {
            $categorySlug = trim((string) $request->string('categoria'));
            if ($categorySlug !== '') {
                $activeCategory = PortfolioCategory::query()->active()->where('slug', $categorySlug)->first();
            }
        }

        $projectsQuery = PortfolioProject::query()
            ->published()
            ->with(['category', 'author'])
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $inner) use ($search): void {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('summary', 'like', "%{$search}%")
                        ->orWhere('challenge', 'like', "%{$search}%")
                        ->orWhere('solution', 'like', "%{$search}%")
                        ->orWhere('results', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->when($activeCategory, fn (Builder $query) => $query->where('category_id', $activeCategory->id))
            ->orderByDesc('published_at');

        $currentPage = max(1, (int) $request->query('page', 1));
        $featuredProject = null;

        if ($currentPage === 1) {
            if ($search === '' && ! $activeCategory) {
                $featuredProject = PortfolioProject::query()
                    ->published()
                    ->with(['category', 'author'])
                    ->featured()
                    ->orderByDesc('published_at')
                    ->first();
            }

            $featuredProject ??= (clone $projectsQuery)->first();

            if ($featuredProject) {
                $projectsQuery->whereKeyNot($featuredProject->id);
            }
        }

        $projects = $projectsQuery->paginate(9)->withQueryString();

        $categories = PortfolioCategory::query()
            ->active()
            ->sorted()
            ->withCount([
                'projects as published_projects_count' => fn (Builder $query) => $query->published(),
            ])
            ->get();

        $spotlightProjects = PortfolioProject::query()
            ->published()
            ->with('category')
            ->when($featuredProject, fn (Builder $query) => $query->whereKeyNot($featuredProject->id))
            ->orderByDesc('views_count')
            ->orderByDesc('published_at')
            ->take(4)
            ->get();

        $metaTitle = match (true) {
            $activeCategory !== null => ($activeCategory->seo_title ?: $activeCategory->name).' | Portfólio Uriah Criativa',
            $search !== '' => 'Busca no Portfólio: '.$search.' | Uriah Criativa',
            default => 'Portfólio Uriah Criativa | Cases de Impressão, Branding e Materiais Gráficos',
        };

        $metaDescription = match (true) {
            $activeCategory !== null && ! empty($activeCategory->seo_description) => (string) $activeCategory->seo_description,
            $activeCategory !== null => 'Cases de '.$activeCategory->name.' com estratégia, produção gráfica e resultados mensuráveis.',
            $search !== '' => 'Resultados da busca no portfólio para "'.$search.'".',
            default => 'Veja cases reais da Uriah Criativa com soluções de impressão, acabamento e campanhas gráficas de alto impacto.',
        };

        $canonical = match (true) {
            $forcedCategory !== null => route('portfolio.category', $forcedCategory->slug),
            $activeCategory !== null && $search === '' => route('portfolio.category', $activeCategory->slug),
            default => route('pages.portfolio'),
        };

        $metaRobots = $search !== '' ? 'noindex, follow' : 'index, follow, max-image-preview:large';
        $ogImage = $featuredProject?->ogImage() ?: asset('favicon.svg?v=uriah2');

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
                'name' => 'Portfólio',
                'item' => route('pages.portfolio'),
            ],
        ];

        if ($activeCategory) {
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $activeCategory->name,
                'item' => route('portfolio.category', $activeCategory->slug),
            ];
        }

        $breadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbItems,
        ];

        $itemListSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => $activeCategory ? 'Portfólio - '.$activeCategory->name : 'Portfólio Uriah Criativa',
            'numberOfItems' => $projects->count() + ($featuredProject ? 1 : 0),
            'itemListElement' => collect($featuredProject ? [$featuredProject] : [])
                ->merge($projects->items())
                ->values()
                ->map(function (PortfolioProject $project, int $index): array {
                    return [
                        '@type' => 'ListItem',
                        'position' => $index + 1,
                        'url' => route('portfolio.show', $project->slug),
                        'name' => $project->title,
                    ];
                })
                ->all(),
        ];

        return view('store.portfolio.index', [
            'projects' => $projects,
            'featuredProject' => $featuredProject,
            'spotlightProjects' => $spotlightProjects,
            'categories' => $categories,
            'activeCategory' => $activeCategory,
            'search' => $search,
            'metaTitle' => $metaTitle,
            'metaDescription' => $metaDescription,
            'canonical' => $canonical,
            'metaRobots' => $metaRobots,
            'ogImage' => $ogImage,
            'breadcrumbSchema' => $breadcrumbSchema,
            'itemListSchema' => $itemListSchema,
            'stats' => [
                'total_projects' => PortfolioProject::query()->published()->count(),
                'total_categories' => PortfolioCategory::query()->active()->count(),
            ],
        ]);
    }
}
