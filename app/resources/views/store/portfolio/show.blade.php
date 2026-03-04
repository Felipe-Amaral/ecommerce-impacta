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

    $coverImage = $resolvePortfolioImage($portfolioProject->cover_image_url);
    $galleryItems = collect($portfolioProject->galleryItems())
        ->map(function (array $item) use ($resolvePortfolioImage): array {
            $item['resolved_url'] = $resolvePortfolioImage($item['url']);

            return $item;
        })
        ->filter(fn (array $item): bool => ! empty($item['resolved_url']))
        ->values();

    $serviceItems = $portfolioProject->serviceItems();
    $toolItems = $portfolioProject->toolItems();
    $metricItems = $portfolioProject->metricItems();
@endphp

@section('title', $metaTitle)
@section('meta_description', $metaDescription)
@section('canonical_url', $canonical)
@section('meta_robots', $metaRobots)
@section('og_type', 'article')
@section('og_image', $ogImage)
@section('seo_json_ld')
    <script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
    <script type="application/ld+json">{!! json_encode($caseStudySchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
@endsection

@push('head')
    <style>
        .portfolio-show-shell {
            margin: 10px 0 30px;
            display: grid;
            gap: 16px;
        }

        .portfolio-show-hero {
            position: relative;
            overflow: hidden;
            border-radius: 26px;
            border: 1px solid rgba(198,161,74,.2);
            background:
                radial-gradient(circle at 8% 0%, rgba(198,161,74,.18), transparent 45%),
                radial-gradient(circle at 96% 22%, rgba(31,94,255,.10), transparent 46%),
                linear-gradient(160deg, rgba(255,255,255,.96), rgba(247,242,234,.94));
            box-shadow:
                0 26px 48px rgba(14,11,9,.10),
                inset 0 1px 0 rgba(255,255,255,.74);
            padding: clamp(16px, 2.5vw, 28px);
            display: grid;
            gap: 16px;
        }

        .portfolio-show-hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(0, .9fr);
            gap: 16px;
            align-items: stretch;
        }

        .portfolio-show-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .portfolio-show-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 7px 11px;
            border: 1px solid rgba(198,161,74,.2);
            background: rgba(255,255,255,.76);
            color: #634c1d;
            font-size: .73rem;
            font-weight: 800;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .portfolio-show-chip::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 999px;
            background: linear-gradient(135deg, #b98c2f, #d3ad56);
            box-shadow: 0 0 0 3px rgba(198,161,74,.14);
        }

        .portfolio-show-title {
            margin: 0;
            font-size: clamp(1.7rem, 3vw, 2.75rem);
            line-height: 1.02;
            letter-spacing: -.01em;
        }

        .portfolio-show-summary {
            margin: 0;
            color: #5f564c;
            line-height: 1.58;
            font-size: .98rem;
            max-width: 60ch;
        }

        .portfolio-show-cover {
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid rgba(22,20,19,.08);
            background:
                linear-gradient(140deg, rgba(198,161,74,.24), rgba(31,94,255,.16)),
                linear-gradient(180deg, #efe6d7, #e8ddca);
            min-height: 280px;
        }

        .portfolio-show-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .portfolio-show-layout {
            display: grid;
            grid-template-columns: minmax(0, .72fr) minmax(0, 1.28fr);
            gap: 16px;
            align-items: start;
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

        .portfolio-data-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            gap: 8px;
        }

        .portfolio-data-list li {
            display: grid;
            gap: 2px;
            border-bottom: 1px dashed rgba(22,20,19,.08);
            padding-bottom: 7px;
        }

        .portfolio-data-list li:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .portfolio-pill-list {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
        }

        .portfolio-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid rgba(198,161,74,.18);
            background: rgba(255,255,255,.82);
            color: #5d4a22;
            font-size: .74rem;
            font-weight: 800;
            letter-spacing: .03em;
            text-transform: uppercase;
        }

        .portfolio-pill::before {
            content: "";
            width: 5px;
            height: 5px;
            border-radius: 999px;
            background: linear-gradient(135deg, #b98c2f, #d3ad56);
        }

        .portfolio-content {
            display: grid;
            gap: 12px;
        }

        .portfolio-block {
            border-radius: 18px;
            border: 1px solid rgba(22,20,19,.08);
            background:
                radial-gradient(circle at 96% 0%, rgba(198,161,74,.1), transparent 44%),
                linear-gradient(180deg, rgba(255,255,255,.95), rgba(255,255,255,.88));
            box-shadow:
                0 12px 22px rgba(12,10,8,.06),
                inset 0 1px 0 rgba(255,255,255,.74);
            padding: 16px;
            display: grid;
            gap: 10px;
        }

        .portfolio-block h2 {
            margin: 0;
            font-size: 1.18rem;
        }

        .portfolio-rich {
            color: #4e463d;
            line-height: 1.63;
            font-size: .95rem;
        }

        .portfolio-rich > :first-child {
            margin-top: 0;
        }

        .portfolio-rich > :last-child {
            margin-bottom: 0;
        }

        .portfolio-metric-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .portfolio-metric-card {
            border-radius: 14px;
            border: 1px solid rgba(22,20,19,.08);
            background: rgba(255,255,255,.84);
            padding: 12px;
            display: grid;
            gap: 4px;
        }

        .portfolio-metric-card.highlight {
            border-color: rgba(198,161,74,.28);
            background:
                linear-gradient(180deg, rgba(198,161,74,.12), rgba(255,255,255,.92));
            box-shadow: 0 12px 20px rgba(198,161,74,.14);
        }

        .portfolio-metric-card strong {
            font-size: 1.1rem;
            line-height: 1.1;
        }

        .portfolio-metric-card span {
            font-size: .74rem;
            color: #655d55;
            text-transform: uppercase;
            letter-spacing: .05em;
            font-weight: 700;
        }

        .portfolio-gallery-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .portfolio-gallery-item {
            border-radius: 14px;
            border: 1px solid rgba(22,20,19,.08);
            overflow: hidden;
            background: rgba(255,255,255,.82);
            box-shadow: 0 10px 18px rgba(12,10,8,.06);
        }

        .portfolio-gallery-item img {
            width: 100%;
            aspect-ratio: 4/3;
            object-fit: cover;
            display: block;
        }

        .portfolio-gallery-caption {
            padding: 8px 10px;
            font-size: .8rem;
            color: #5f564c;
            line-height: 1.45;
        }

        .portfolio-related-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .portfolio-related-card {
            border-radius: 14px;
            border: 1px solid rgba(22,20,19,.08);
            background: rgba(255,255,255,.86);
            padding: 10px;
            display: grid;
            gap: 6px;
        }

        .portfolio-related-card strong {
            font-size: .88rem;
            line-height: 1.3;
        }

        .portfolio-related-card span {
            font-size: .75rem;
            color: #6a6258;
        }

        @media (max-width: 1020px) {
            .portfolio-show-hero-grid,
            .portfolio-show-layout {
                grid-template-columns: 1fr;
            }

            .portfolio-side {
                position: static;
                top: auto;
            }

            .portfolio-metric-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 720px) {
            .portfolio-gallery-grid,
            .portfolio-related-grid,
            .portfolio-metric-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <div class="portfolio-show-shell">
        @if($isAdminPreview)
            <section class="card card-pad" style="border-color: rgba(31,94,255,.32); background: rgba(31,94,255,.08);">
                <strong>Pré-visualização de admin:</strong>
                <span class="small">este case não está publicado para o público no momento.</span>
            </section>
        @endif

        <section class="portfolio-show-hero reveal-up">
            <div class="portfolio-show-hero-grid">
                <div class="stack-lg">
                    <div class="portfolio-show-meta">
                        <span class="portfolio-show-chip">{{ $portfolioProject->category?->name ?: 'Case de portfólio' }}</span>
                        @if($portfolioProject->project_year)
                            <span class="portfolio-show-chip">{{ $portfolioProject->project_year }}</span>
                        @endif
                        @if($portfolioProject->client_name)
                            <span class="portfolio-show-chip">{{ $portfolioProject->client_name }}</span>
                        @endif
                    </div>
                    <h1 class="portfolio-show-title">{{ $portfolioProject->title }}</h1>
                    <p class="portfolio-show-summary">{{ $portfolioProject->summary ?: 'Case com estratégia criativa, execução de impressão e resultados aplicados ao negócio do cliente.' }}</p>
                    <div style="display:flex; flex-wrap:wrap; gap:8px;">
                        <a href="{{ route('pages.quote') }}" class="btn btn-primary">Solicitar projeto parecido</a>
                        <a href="{{ route('pages.contact') }}" class="btn btn-secondary">Falar com especialista</a>
                    </div>
                </div>
                <div class="portfolio-show-cover">
                    @if($coverImage)
                        <img src="{{ $coverImage }}" alt="{{ $portfolioProject->title }}">
                    @endif
                </div>
            </div>
        </section>

        <section class="portfolio-show-layout">
            <aside class="portfolio-side">
                <section class="portfolio-panel">
                    <h3>Ficha do projeto</h3>
                    <ul class="portfolio-data-list small">
                        <li>
                            <span class="tiny muted">Cliente</span>
                            <strong>{{ $portfolioProject->client_name ?: 'Não informado' }}</strong>
                        </li>
                        <li>
                            <span class="tiny muted">Segmento</span>
                            <strong>{{ $portfolioProject->industry ?: 'Não informado' }}</strong>
                        </li>
                        <li>
                            <span class="tiny muted">Localidade</span>
                            <strong>{{ $portfolioProject->location ?: 'Não informado' }}</strong>
                        </li>
                        <li>
                            <span class="tiny muted">Publicado em</span>
                            <strong>{{ optional($portfolioProject->published_at)->format('d/m/Y') ?: 'Não publicado' }}</strong>
                        </li>
                        @if($portfolioProject->project_url)
                            <li>
                                <span class="tiny muted">Link do projeto</span>
                                <strong><a href="{{ $portfolioProject->project_url }}" target="_blank" rel="noreferrer">Acessar URL externa</a></strong>
                            </li>
                        @endif
                    </ul>
                </section>

                @if(!empty($serviceItems))
                    <section class="portfolio-panel">
                        <h3>Serviços aplicados</h3>
                        <div class="portfolio-pill-list">
                            @foreach($serviceItems as $service)
                                <span class="portfolio-pill">{{ $service }}</span>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if(!empty($toolItems))
                    <section class="portfolio-panel">
                        <h3>Processos e acabamentos</h3>
                        <div class="portfolio-pill-list">
                            @foreach($toolItems as $tool)
                                <span class="portfolio-pill">{{ $tool }}</span>
                            @endforeach
                        </div>
                    </section>
                @endif
            </aside>

            <div class="portfolio-content">
                @if(!empty($metricItems))
                    <article class="portfolio-block">
                        <h2>Resultados em destaque</h2>
                        <div class="portfolio-metric-grid">
                            @foreach($metricItems as $metric)
                                <div class="portfolio-metric-card {{ $metric['highlight'] ? 'highlight' : '' }}">
                                    <strong>{{ $metric['value'] }}</strong>
                                    <span>{{ $metric['label'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </article>
                @endif

                @if(trim(strip_tags($challengeHtml)) !== '')
                    <article class="portfolio-block">
                        <h2>Desafio</h2>
                        <div class="portfolio-rich">{!! $challengeHtml !!}</div>
                    </article>
                @endif

                @if(trim(strip_tags($solutionHtml)) !== '')
                    <article class="portfolio-block">
                        <h2>Solução aplicada</h2>
                        <div class="portfolio-rich">{!! $solutionHtml !!}</div>
                    </article>
                @endif

                @if(trim(strip_tags($resultsHtml)) !== '')
                    <article class="portfolio-block">
                        <h2>Resultados</h2>
                        <div class="portfolio-rich">{!! $resultsHtml !!}</div>
                    </article>
                @endif

                @if(trim(strip_tags($contentHtml)) !== '')
                    <article class="portfolio-block">
                        <h2>Detalhamento técnico</h2>
                        <div class="portfolio-rich">{!! $contentHtml !!}</div>
                    </article>
                @endif

                @if($galleryItems->isNotEmpty())
                    <article class="portfolio-block">
                        <h2>Galeria do projeto</h2>
                        <div class="portfolio-gallery-grid">
                            @foreach($galleryItems as $galleryItem)
                                <figure class="portfolio-gallery-item">
                                    <img src="{{ $galleryItem['resolved_url'] }}" alt="{{ $galleryItem['alt'] }}">
                                    @if($galleryItem['caption'] !== '')
                                        <figcaption class="portfolio-gallery-caption">{{ $galleryItem['caption'] }}</figcaption>
                                    @endif
                                </figure>
                            @endforeach
                        </div>
                    </article>
                @endif

                <article class="portfolio-block">
                    <h2>Próximo passo</h2>
                    <p class="portfolio-rich" style="margin:0;">Quer aplicar uma solução parecida no seu negócio? Envie seu contexto e construímos uma proposta alinhada ao seu objetivo de marca e resultado comercial.</p>
                    <div style="display:flex; flex-wrap:wrap; gap:8px;">
                        <a href="{{ route('pages.quote') }}" class="btn btn-primary">Solicitar orçamento</a>
                        <a href="{{ route('pages.contact') }}" class="btn btn-secondary">Falar com atendimento</a>
                    </div>
                </article>

                @if($relatedProjects->isNotEmpty())
                    <article class="portfolio-block">
                        <h2>Cases relacionados</h2>
                        <div class="portfolio-related-grid">
                            @foreach($relatedProjects as $relatedProject)
                                <a href="{{ route('portfolio.show', $relatedProject->slug) }}" class="portfolio-related-card">
                                    <strong>{{ $relatedProject->title }}</strong>
                                    <span>{{ $relatedProject->category?->name ?: 'Case' }}</span>
                                </a>
                            @endforeach
                        </div>
                    </article>
                @endif
            </div>
        </section>
    </div>
@endsection
