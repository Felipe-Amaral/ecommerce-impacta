@extends('layouts.store')

@php
    $resolvePortfolioImage = static function (?string $rawPath): ?string {
        $path = trim((string) $rawPath);

        if ($path === '') {
            return null;
        }

        return match (true) {
            \Illuminate\Support\Str::startsWith($path, ['http://', 'https://']) => $path,
            \Illuminate\Support\Str::startsWith($path, '/storage/') => url($path),
            \Illuminate\Support\Str::startsWith($path, 'storage/') => asset($path),
            \Illuminate\Support\Str::startsWith($path, '/') => url($path),
            default => asset('storage/'.ltrim($path, '/')),
        };
    };

    $searchAction = $activeCategory
        ? route('portfolio.category', $activeCategory->slug)
        : route('pages.portfolio');

    $headline = match (true) {
        $activeCategory !== null => $activeCategory->name,
        $search !== '' => 'Resultados para "'.$search.'"',
        default => 'Cases reais para inspirar o próximo projeto da sua marca',
    };

    $subheadline = match (true) {
        $activeCategory !== null => $activeCategory->description ?: 'Projetos da categoria selecionada, com foco em resultado real.',
        $search !== '' => 'Filtramos os cases com base na sua busca.',
        default => 'Vitrine de projetos com desafio, solução, execução gráfica e impacto de negócio.',
    };

    $featuredImage = $resolvePortfolioImage($featuredProject?->cover_image_url);
@endphp

@section('title', $metaTitle)
@section('meta_description', $metaDescription)
@section('canonical_url', $canonical)
@section('meta_robots', $metaRobots)
@section('og_type', 'website')
@section('og_image', $ogImage)
@section('seo_json_ld')
    <script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
    <script type="application/ld+json">{!! json_encode($itemListSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
@endsection

@push('head')
    <style>
        .portfolio-shell {
            margin: 10px 0 30px;
            display: grid;
            gap: 18px;
        }

        .portfolio-hero {
            position: relative;
            overflow: hidden;
            border-radius: 26px;
            border: 1px solid rgba(198,161,74,.18);
            background:
                radial-gradient(circle at 10% 0%, rgba(198,161,74,.18), transparent 44%),
                radial-gradient(circle at 96% 20%, rgba(31,94,255,.10), transparent 46%),
                linear-gradient(160deg, rgba(255,255,255,.96), rgba(247,242,234,.94));
            box-shadow:
                0 26px 48px rgba(14,11,9,.10),
                inset 0 1px 0 rgba(255,255,255,.74);
            padding: clamp(16px, 2.6vw, 28px);
            display: grid;
            gap: 16px;
        }

        .portfolio-hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(0, .85fr);
            gap: 16px;
        }

        .portfolio-kicker-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .portfolio-kicker-chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 11px;
            border-radius: 999px;
            border: 1px solid rgba(198,161,74,.2);
            background: rgba(255,255,255,.72);
            color: #5e4b22;
            font-size: .74rem;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .portfolio-kicker-chip::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 999px;
            background: linear-gradient(135deg, #b98c2f, #d3ad56);
            box-shadow: 0 0 0 3px rgba(198,161,74,.14);
        }

        .portfolio-headline {
            margin: 0;
            font-size: clamp(1.6rem, 3vw, 2.65rem);
            line-height: 1.03;
            letter-spacing: -.01em;
            max-width: 20ch;
        }

        .portfolio-subheadline {
            margin: 0;
            color: #60584f;
            font-size: .98rem;
            line-height: 1.55;
            max-width: 58ch;
        }

        .portfolio-search-form {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 8px;
            margin-top: 8px;
        }

        .portfolio-search-form .input {
            border-radius: 14px;
            min-height: 45px;
            border: 1px solid rgba(198,161,74,.18);
            background: rgba(255,255,255,.88);
        }

        .portfolio-search-form .btn {
            border-radius: 14px;
            min-height: 45px;
            padding-inline: 16px;
        }

        .portfolio-featured-card {
            position: relative;
            border-radius: 20px;
            border: 1px solid rgba(22,20,19,.07);
            min-height: 100%;
            overflow: hidden;
            display: grid;
            background:
                radial-gradient(circle at 90% 10%, rgba(255,255,255,.12), transparent 44%),
                linear-gradient(170deg, #1f1a16, #12100e 62%);
            color: #fff;
        }

        .portfolio-featured-cover {
            position: absolute;
            inset: 0;
            z-index: 0;
            opacity: .68;
            background-size: cover;
            background-position: center;
        }

        .portfolio-featured-overlay {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(190deg, rgba(17,14,12,.18), rgba(17,14,12,.9) 68%),
                linear-gradient(130deg, rgba(198,161,74,.18), transparent 40%);
            z-index: 1;
        }

        .portfolio-featured-content {
            position: relative;
            z-index: 2;
            display: grid;
            align-content: end;
            gap: 10px;
            padding: 18px;
            min-height: 280px;
        }

        .portfolio-featured-title {
            margin: 0;
            font-size: clamp(1.1rem, 2vw, 1.55rem);
            line-height: 1.14;
        }

        .portfolio-featured-title a {
            color: inherit;
        }

        .portfolio-featured-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            font-size: .76rem;
            color: rgba(255,255,255,.88);
            letter-spacing: .04em;
            text-transform: uppercase;
            font-weight: 700;
        }

        .portfolio-featured-meta .dot {
            width: 4px;
            height: 4px;
            border-radius: 999px;
            background: rgba(255,255,255,.66);
        }

        .portfolio-featured-summary {
            margin: 0;
            color: rgba(255,255,255,.9);
            font-size: .9rem;
            line-height: 1.5;
        }

        .portfolio-main-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 300px;
            gap: 16px;
            align-items: start;
        }

        .portfolio-feed {
            display: grid;
            gap: 14px;
        }

        .portfolio-feed-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .portfolio-card {
            position: relative;
            border-radius: 18px;
            border: 1px solid rgba(22,20,19,.08);
            background:
                radial-gradient(circle at 92% -10%, rgba(198,161,74,.14), transparent 45%),
                linear-gradient(180deg, rgba(255,255,255,.94), rgba(255,255,255,.84));
            box-shadow:
                0 14px 24px rgba(12,10,8,.06),
                inset 0 1px 0 rgba(255,255,255,.72);
            overflow: hidden;
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        }

        .portfolio-card:hover {
            transform: translateY(-2px);
            border-color: rgba(198,161,74,.26);
            box-shadow:
                0 18px 30px rgba(12,10,8,.08),
                inset 0 1px 0 rgba(255,255,255,.78);
        }

        .portfolio-card-cover {
            position: relative;
            aspect-ratio: 16/9;
            overflow: hidden;
            border-bottom: 1px solid rgba(22,20,19,.08);
            background:
                linear-gradient(135deg, rgba(198,161,74,.25), rgba(31,94,255,.16)),
                linear-gradient(180deg, #f0e8d8, #e7dcc9);
        }

        .portfolio-card-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .portfolio-card-cover::after {
            content: "";
            position: absolute;
            inset: auto 0 0;
            height: 40%;
            background: linear-gradient(180deg, transparent, rgba(11,9,8,.38));
            pointer-events: none;
        }

        .portfolio-card-body {
            display: grid;
            gap: 10px;
            padding: 14px;
        }

        .portfolio-card-title {
            margin: 0;
            font-size: 1.02rem;
            line-height: 1.2;
        }

        .portfolio-card-title a {
            color: inherit;
        }

        .portfolio-card-summary {
            margin: 0;
            color: #625a50;
            font-size: .88rem;
            line-height: 1.5;
        }

        .portfolio-card-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            align-items: center;
            font-size: .75rem;
            color: #615a52;
            text-transform: uppercase;
            letter-spacing: .04em;
            font-weight: 700;
        }

        .portfolio-card-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 8px;
            border-radius: 999px;
            border: 1px solid rgba(198,161,74,.18);
            background: rgba(255,255,255,.78);
            color: #665022;
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .03em;
            text-transform: uppercase;
        }

        .portfolio-card-chip::before {
            content: "";
            width: 5px;
            height: 5px;
            border-radius: 999px;
            background: linear-gradient(135deg, #b98c2f, #d3ad56);
        }

        .portfolio-metric-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .portfolio-metric-box {
            border-radius: 12px;
            border: 1px solid rgba(22,20,19,.08);
            padding: 8px;
            background: rgba(255,255,255,.8);
            display: grid;
            gap: 2px;
        }

        .portfolio-metric-box strong {
            font-size: .92rem;
            line-height: 1.1;
        }

        .portfolio-metric-box span {
            font-size: .72rem;
            color: #6a635a;
            text-transform: uppercase;
            letter-spacing: .05em;
            font-weight: 700;
        }

        .portfolio-side {
            display: grid;
            gap: 12px;
            position: sticky;
            top: 88px;
        }

        .portfolio-panel {
            border-radius: 18px;
            border: 1px solid rgba(22,20,19,.08);
            background:
                radial-gradient(circle at 94% 0%, rgba(198,161,74,.12), transparent 40%),
                linear-gradient(180deg, rgba(255,255,255,.94), rgba(255,255,255,.86));
            box-shadow:
                0 12px 22px rgba(12,10,8,.06),
                inset 0 1px 0 rgba(255,255,255,.74);
            padding: 14px;
            display: grid;
            gap: 10px;
        }

        .portfolio-panel h3 {
            margin: 0;
            font-size: 1rem;
        }

        .portfolio-panel p {
            margin: 0;
            color: #645b52;
            font-size: .88rem;
            line-height: 1.5;
        }

        .portfolio-category-list,
        .portfolio-spotlight-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            gap: 8px;
        }

        .portfolio-category-list a,
        .portfolio-spotlight-list a {
            display: grid;
            gap: 2px;
            border-radius: 12px;
            border: 1px solid rgba(22,20,19,.08);
            background: rgba(255,255,255,.82);
            padding: 10px;
            transition: .16s ease;
        }

        .portfolio-category-list a:hover,
        .portfolio-spotlight-list a:hover {
            transform: translateY(-1px);
            border-color: rgba(198,161,74,.24);
            box-shadow: 0 10px 20px rgba(12,10,8,.06);
        }

        .portfolio-category-list a.active {
            border-color: rgba(198,161,74,.28);
            background: rgba(255,255,255,.96);
            box-shadow: 0 10px 20px rgba(12,10,8,.07);
        }

        .portfolio-category-list strong,
        .portfolio-spotlight-list strong {
            font-size: .84rem;
            line-height: 1.3;
        }

        .portfolio-category-list span,
        .portfolio-spotlight-list span {
            font-size: .74rem;
            color: #6c655c;
        }

        @media (max-width: 980px) {
            .portfolio-hero-grid,
            .portfolio-main-grid {
                grid-template-columns: 1fr;
            }

            .portfolio-feed-grid {
                grid-template-columns: 1fr;
            }

            .portfolio-side {
                position: static;
                top: auto;
            }
        }
    </style>
@endpush

@section('content')
    <div class="portfolio-shell">
        <section class="portfolio-hero reveal-up">
            <div class="portfolio-hero-grid">
                <div>
                    <div class="portfolio-kicker-row">
                        <span class="portfolio-kicker-chip">Portfólio Uriah Criativa</span>
                        <span class="pill">{{ $stats['total_projects'] }} cases publicados</span>
                        <span class="pill">{{ $stats['total_categories'] }} categorias ativas</span>
                    </div>
                    <h1 class="portfolio-headline">{{ $headline }}</h1>
                    <p class="portfolio-subheadline">{{ $subheadline }}</p>

                    <form method="GET" action="{{ $searchAction }}" class="portfolio-search-form" role="search" aria-label="Buscar no portfólio">
                        <input class="input" type="search" name="q" value="{{ $search }}" placeholder="Buscar por desafio, solução, cliente ou segmento">
                        <button type="submit" class="btn btn-primary">Buscar</button>
                    </form>
                </div>

                @if($featuredProject)
                    <article class="portfolio-featured-card">
                        @if($featuredImage)
                            <div class="portfolio-featured-cover" style="background-image:url('{{ $featuredImage }}');"></div>
                        @endif
                        <div class="portfolio-featured-overlay"></div>
                        <div class="portfolio-featured-content">
                            <div class="portfolio-featured-meta">
                                <span>{{ $featuredProject->category?->name ?: 'Case em destaque' }}</span>
                                @if($featuredProject->project_year)
                                    <span class="dot"></span>
                                    <span>{{ $featuredProject->project_year }}</span>
                                @endif
                            </div>
                            <h2 class="portfolio-featured-title">
                                <a href="{{ route('portfolio.show', $featuredProject->slug) }}">{{ $featuredProject->title }}</a>
                            </h2>
                            <p class="portfolio-featured-summary">{{ \Illuminate\Support\Str::limit($featuredProject->summary, 140) ?: 'Case detalhado com estratégia, produção e resultado final.' }}</p>
                        </div>
                    </article>
                @endif
            </div>
        </section>

        <section class="portfolio-main-grid">
            <div class="portfolio-feed">
                @if($projects->isEmpty())
                    <article class="card card-pad stack">
                        <h2 style="font-size:1.2rem;">Nenhum case encontrado</h2>
                        <p class="muted">Tente ajustar sua busca ou navegar por outra categoria.</p>
                        <a href="{{ route('pages.portfolio') }}" class="btn btn-secondary" style="width:fit-content;">Ver todos os cases</a>
                    </article>
                @else
                    <div class="portfolio-feed-grid">
                        @foreach($projects as $project)
                            @php
                                $coverImage = $resolvePortfolioImage($project->cover_image_url);
                                $projectMetrics = collect($project->metricItems())->take(2)->all();
                            @endphp
                            <article class="portfolio-card reveal-up">
                                <div class="portfolio-card-cover">
                                    @if($coverImage)
                                        <img src="{{ $coverImage }}" alt="{{ $project->title }}" loading="lazy">
                                    @endif
                                </div>
                                <div class="portfolio-card-body">
                                    <div class="portfolio-card-meta">
                                        <span>{{ $project->category?->name ?: 'Case' }}</span>
                                        @if($project->project_year)
                                            <span>&bull;</span>
                                            <span>{{ $project->project_year }}</span>
                                        @endif
                                    </div>
                                    <h3 class="portfolio-card-title"><a href="{{ route('portfolio.show', $project->slug) }}">{{ $project->title }}</a></h3>
                                    <p class="portfolio-card-summary">{{ \Illuminate\Support\Str::limit($project->summary, 150) ?: 'Case com briefing, execução e resultados documentados.' }}</p>

                                    @if(!empty($projectMetrics))
                                        <div class="portfolio-metric-row">
                                            @foreach($projectMetrics as $metric)
                                                <div class="portfolio-metric-box">
                                                    <strong>{{ $metric['value'] }}</strong>
                                                    <span>{{ $metric['label'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div style="display:flex; flex-wrap:wrap; gap:6px;">
                                        @foreach(collect($project->serviceItems())->take(2) as $service)
                                            <span class="portfolio-card-chip">{{ $service }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    {{ $projects->links() }}
                @endif
            </div>

            <aside class="portfolio-side">
                <section class="portfolio-panel">
                    <h3>Categorias</h3>
                    <p>Filtre os projetos por tipo de demanda.</p>
                    <ul class="portfolio-category-list">
                        <li>
                            <a href="{{ route('pages.portfolio') }}" class="{{ ! $activeCategory ? 'active' : '' }}">
                                <strong>Todos os cases</strong>
                                <span>Vitrine completa</span>
                            </a>
                        </li>
                        @foreach($categories as $category)
                            <li>
                                <a href="{{ route('portfolio.category', $category->slug) }}" class="{{ $activeCategory?->id === $category->id ? 'active' : '' }}">
                                    <strong>{{ $category->name }}</strong>
                                    <span>{{ (int) $category->published_projects_count }} projeto(s)</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </section>

                <section class="portfolio-panel">
                    <h3>Cases em evidência</h3>
                    <p>Projetos com maior interesse recente dos visitantes.</p>
                    @if($spotlightProjects->isEmpty())
                        <span class="small muted">Sem cases em evidência no momento.</span>
                    @else
                        <ul class="portfolio-spotlight-list">
                            @foreach($spotlightProjects as $spotlight)
                                <li>
                                    <a href="{{ route('portfolio.show', $spotlight->slug) }}">
                                        <strong>{{ $spotlight->title }}</strong>
                                        <span>{{ $spotlight->category?->name ?: 'Case' }} • {{ (int) $spotlight->views_count }} visualizações</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            </aside>
        </section>
    </div>
@endsection
