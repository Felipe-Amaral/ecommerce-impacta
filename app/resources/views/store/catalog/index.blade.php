@extends('layouts.store')

@php
    $selectedCategory = $categorySlug !== '' ? $categories->firstWhere('slug', $categorySlug) : null;
    $isInternalSearch = $search !== '';
    $totalCatalogProducts = (int) $categories->sum('active_products_count');

    $catalogTitle = match (true) {
        $selectedCategory && $isInternalSearch => 'Busca em '.$selectedCategory->name.' | Catálogo | Uriah Criativa',
        $selectedCategory !== null => (($selectedCategory->meta_title ?: $selectedCategory->name).' | Catálogo | Uriah Criativa'),
        $isInternalSearch => 'Resultados para "'.$search.'" | Catálogo | Uriah Criativa',
        default => 'Catálogo de Impressos | Uriah Criativa',
    };

    $catalogDescription = match (true) {
        $selectedCategory && !empty($selectedCategory->meta_description) => (string) $selectedCategory->meta_description,
        $selectedCategory !== null => 'Explore '.$selectedCategory->name.' na Uriah Criativa. Configure tiragem, acabamento e finalize seu pedido online com rapidez.',
        $isInternalSearch => 'Resultados da busca interna por impressos na Uriah Criativa. Refine filtros para encontrar o produto ideal.',
        default => 'Catálogo online de impressos da Uriah Criativa com categorias, filtros por tipo de peça e navegação rápida para orçamento e compra.',
    };

    $catalogCanonical = match (true) {
        $selectedCategory !== null && ! $isInternalSearch => route('catalog.index', ['categoria' => $selectedCategory->slug]),
        default => route('catalog.index'),
    };

    $catalogRobots = $isInternalSearch ? 'noindex, follow' : 'index, follow, max-image-preview:large';

    $catalogBreadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => array_values(array_filter([
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Início',
                'item' => route('home'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => 'Catálogo',
                'item' => route('catalog.index'),
            ],
            $selectedCategory ? [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $selectedCategory->name,
                'item' => route('catalog.index', ['categoria' => $selectedCategory->slug]),
            ] : null,
        ])),
    ];

    $catalogItemListSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'CollectionPage',
        'name' => $catalogTitle,
        'description' => $catalogDescription,
        'url' => $catalogCanonical,
        'inLanguage' => 'pt-BR',
        'mainEntity' => [
            '@type' => 'ItemList',
            'numberOfItems' => $products->count(),
            'itemListElement' => $products->values()->take(24)->map(function ($product, $index) {
                return [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'url' => route('catalog.show', $product->slug),
                    'name' => $product->name,
                ];
            })->all(),
        ],
    ];
@endphp

@section('title', $catalogTitle)
@section('meta_description', $catalogDescription)
@section('canonical_url', $catalogCanonical)
@section('meta_robots', $catalogRobots)
@section('og_type', 'website')
@section('seo_json_ld')
    <script type="application/ld+json">{!! json_encode($catalogBreadcrumbSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
    <script type="application/ld+json">{!! json_encode($catalogItemListSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
@endsection

@push('head')
    <style>
        .catalog-shell {
            display: grid;
            grid-template-columns: 290px minmax(0, 1fr);
            gap: 16px;
            align-items: start;
            margin: 4px 0 28px;
        }

        .catalog-sidebar {
            min-width: 0;
        }

        .catalog-sidebar-card {
            position: relative;
            overflow: hidden;
            display: grid;
            gap: 14px;
            border-radius: 20px;
            border: 1px solid rgba(22,20,19,.08);
            background:
                radial-gradient(circle at 92% 6%, rgba(198,161,74,.08), transparent 42%),
                linear-gradient(180deg, rgba(255,255,255,.92), rgba(255,255,255,.82));
            box-shadow:
                0 12px 26px rgba(17,15,12,.06),
                inset 0 1px 0 rgba(255,255,255,.72);
        }

        .catalog-sidebar-card::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                linear-gradient(130deg, rgba(198,161,74,.05), transparent 28%, transparent 72%, rgba(198,161,74,.04));
        }

        .catalog-sidebar-card > * {
            position: relative;
            z-index: 1;
        }

        .catalog-sidebar-head {
            display: grid;
            gap: 6px;
        }

        .catalog-sidebar-head h2 {
            margin: 0;
            font-size: 1.16rem;
            line-height: 1.02;
        }

        .catalog-search-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: fit-content;
            max-width: 100%;
            padding: 8px 10px;
            border-radius: 999px;
            border: 1px solid rgba(198,161,74,.16);
            background: rgba(198,161,74,.08);
            color: #6f5620;
            font-size: .78rem;
            font-weight: 700;
        }

        .catalog-search-chip strong {
            color: #2c261e;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 140px;
        }

        .catalog-side-links {
            display: grid;
            gap: 8px;
            max-height: min(66vh, 620px);
            overflow: auto;
            padding-right: 2px;
            scrollbar-width: thin;
            scrollbar-color: rgba(198,161,74,.25) transparent;
        }

        .catalog-side-link {
            position: relative;
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            align-items: center;
            gap: 10px;
            padding: 10px 11px;
            border-radius: 14px;
            border: 1px solid rgba(22,20,19,.06);
            background: rgba(255,255,255,.84);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.85);
            transition:
                transform .18s ease,
                border-color .18s ease,
                box-shadow .18s ease,
                background-color .18s ease;
        }

        .catalog-side-link:hover {
            transform: translateY(-1px);
            border-color: rgba(198,161,74,.18);
            background: rgba(255,255,255,.96);
            box-shadow:
                0 8px 16px rgba(17,15,12,.04),
                inset 0 1px 0 rgba(255,255,255,.9);
        }

        .catalog-side-link.active {
            border-color: rgba(198,161,74,.20);
            background:
                linear-gradient(180deg, rgba(255,255,255,.98), rgba(249,244,234,.96));
            box-shadow:
                0 10px 18px rgba(17,15,12,.04),
                inset 0 1px 0 rgba(255,255,255,.92);
        }

        .catalog-side-link.active::before {
            content: "";
            position: absolute;
            inset: 8px auto 8px 6px;
            width: 3px;
            border-radius: 999px;
            background: linear-gradient(180deg, #b98c2f, #e2c47a);
            box-shadow: 0 4px 10px rgba(198,161,74,.18);
        }

        .catalog-side-link-icon {
            width: 30px;
            height: 30px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            font-weight: 800;
            font-size: .82rem;
            color: #4f3d15;
            border: 1px solid rgba(198,161,74,.18);
            background:
                linear-gradient(180deg, rgba(255,255,255,.95), rgba(247,241,230,.96));
            box-shadow: inset 0 1px 0 rgba(255,255,255,.9);
        }

        .catalog-side-link.active .catalog-side-link-icon {
            color: #3f2f0e;
            border-color: rgba(198,161,74,.24);
            background:
                linear-gradient(180deg, rgba(242,224,174,.95), rgba(214,181,98,.92));
        }

        .catalog-side-link-label {
            min-width: 0;
            display: grid;
            gap: 1px;
        }

        .catalog-side-link-label strong {
            font-size: .9rem;
            line-height: 1.1;
            color: #1d1916;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .catalog-side-link-label span {
            font-size: .74rem;
            color: rgba(92,83,72,.82);
            line-height: 1.1;
        }

        .catalog-side-link-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            height: 28px;
            padding: 0 8px;
            border-radius: 999px;
            border: 1px solid rgba(22,20,19,.06);
            background: rgba(255,255,255,.9);
            color: #574f45;
            font-size: .76rem;
            font-weight: 800;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.88);
        }

        .catalog-side-link.active .catalog-side-link-count {
            border-color: rgba(198,161,74,.18);
            background: rgba(198,161,74,.10);
            color: #7f6120;
        }

        .catalog-side-note {
            border-radius: 14px;
            border: 1px dashed rgba(198,161,74,.20);
            background: rgba(255,255,255,.74);
            padding: 10px 11px;
            color: rgba(92,83,72,.9);
            font-size: .8rem;
            line-height: 1.35;
        }

        .catalog-main-stack {
            min-width: 0;
        }

        .catalog-toolbar {
            padding: 12px 14px;
            border-radius: 18px;
        }

        .catalog-toolbar > form.stack {
            gap: 10px;
        }

        .catalog-toolbar-inline {
            display: grid;
            grid-template-columns: minmax(0, 1.45fr) auto auto auto;
            gap: 10px;
            align-items: end;
        }

        .catalog-toolbar-category-field,
        .catalog-toolbar-categories {
            display: none;
        }

        .catalog-toolbar-inline .field {
            gap: 4px;
        }

        .catalog-toolbar-inline .field label {
            font-size: .68rem;
            letter-spacing: .08em;
        }

        .catalog-toolbar-inline .input,
        .catalog-toolbar-inline .select {
            min-height: 40px;
            padding: 9px 11px;
        }

        .catalog-toolbar-inline .btn {
            min-height: 40px;
            padding: 9px 12px;
            white-space: nowrap;
        }

        .catalog-toolbar-count {
            align-self: center;
            white-space: nowrap;
            justify-self: end;
            font-weight: 700;
        }

        .catalog-results-headline {
            display: inline-flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .catalog-results-category-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 30px;
            padding: 4px 10px;
            border-radius: 999px;
            border: 1px solid rgba(198,161,74,.18);
            background:
                linear-gradient(180deg, rgba(255,252,243,.98), rgba(251,241,214,.90));
            color: #7a5b1a;
            font-size: .78rem;
            font-weight: 800;
            box-shadow:
                0 6px 12px rgba(198,161,74,.12),
                inset 0 1px 0 rgba(255,255,255,.74);
        }

        .catalog-results-category-chip .dot {
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: currentColor;
            opacity: .9;
        }

        @media (max-width: 980px) {
            .catalog-shell {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .catalog-sidebar .floating-sticky {
                position: static;
                top: auto;
            }

            .catalog-sidebar-card {
                gap: 12px;
            }

            .catalog-side-links {
                max-height: none;
                overflow: visible;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .catalog-side-link-label span {
                display: none;
            }

            .catalog-toolbar {
                padding: 12px;
            }

            .catalog-toolbar-inline {
                grid-template-columns: 1fr 1fr;
                align-items: stretch;
            }

            .catalog-toolbar-category-field {
                display: grid;
            }

            .catalog-toolbar-categories {
                display: flex;
            }

            .catalog-toolbar-inline .catalog-toolbar-count {
                grid-column: 1 / -1;
                justify-self: start;
                margin-top: -2px;
            }
        }

        @media (max-width: 640px) {
            .catalog-side-links {
                grid-template-columns: 1fr;
            }

            .catalog-side-link {
                grid-template-columns: auto minmax(0, 1fr) auto;
            }

            .catalog-toolbar-inline {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .catalog-toolbar-inline .catalog-toolbar-count {
                margin-top: 0;
            }
        }
    </style>
@endpush

@section('content')
    <nav aria-label="Breadcrumb" class="small muted" style="margin: 2px 0 12px;">
        <a href="{{ route('home') }}">Início</a>
        <span aria-hidden="true"> / </span>
        @if($selectedCategory)
            <a href="{{ route('catalog.index') }}">Catálogo</a>
            <span aria-hidden="true"> / </span>
            <span aria-current="page">{{ $selectedCategory->name }}</span>
        @else
            <span aria-current="page">Catálogo</span>
        @endif
    </nav>

    <section class="catalog-shell">
        <aside class="catalog-sidebar reveal-up" aria-label="Menu lateral de categorias">
            <div class="card card-pad catalog-sidebar-card floating-sticky">
                <div class="catalog-sidebar-head">
                    <span class="section-kicker">Categorias</span>
                    <h2>Menu lateral</h2>
                    <p class="small muted">Clique em uma categoria no menu à esquerda para listar os produtos ao lado.</p>
                </div>

                @if($search !== '')
                    <div class="catalog-search-chip" title="{{ $search }}">
                        Busca ativa
                        <strong>{{ $search }}</strong>
                    </div>
                @endif

                <nav class="catalog-side-links" aria-label="Categorias do catálogo">
                    <a
                        href="{{ route('catalog.index', ['q' => $search ?: null]) }}"
                        class="catalog-side-link {{ $selectedCategory ? '' : 'active' }}"
                    >
                        <span class="catalog-side-link-icon">T</span>
                        <span class="catalog-side-link-label">
                            <strong>Todas as categorias</strong>
                            <span>Visão geral do catálogo</span>
                        </span>
                        <span class="catalog-side-link-count">{{ $totalCatalogProducts }}</span>
                    </a>

                    @foreach ($categories as $category)
                        <a
                            href="{{ route('catalog.index', ['categoria' => $category->slug, 'q' => $search ?: null]) }}"
                            class="catalog-side-link {{ $categorySlug === $category->slug ? 'active' : '' }}"
                        >
                            <span class="catalog-side-link-icon">
                                {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($category->name, 0, 1)) }}
                            </span>
                            <span class="catalog-side-link-label">
                                <strong>{{ $category->name }}</strong>
                                <span>{{ $category->description ?: 'Categoria do catálogo' }}</span>
                            </span>
                            <span class="catalog-side-link-count">{{ (int) ($category->active_products_count ?? 0) }}</span>
                        </a>
                    @endforeach
                </nav>

                <div class="catalog-side-note">
                    Dica: selecione uma categoria e use a busca para filtrar mais rápido por tipo de peça, material ou aplicação.
                </div>
            </div>
        </aside>

        <div class="catalog-main-stack stack-xl">
            <section class="hero hero-studio reveal-up" style="margin-bottom: 0;">
                <div class="hero-studio-grid">
                    <div class="hero-pane">
                        <div class="stack-xl">
                            <div class="stack">
                                <span class="hero-tagline"><span class="dot"></span> Catálogo de impressos</span>
                                <h1 class="text-gradient" style="font-size: clamp(1.8rem, 3vw, 2.7rem);">Encontre materiais por categoria, aplicação ou tipo de peça</h1>
                                <p class="lead">Filtros simples, cards objetivos e foco em leitura rápida de preço e prazo.</p>
                            </div>
                            <div class="checkout-progress">
                                <span class="step-chip"><span class="n">1</span> Buscar</span>
                                <span class="step-chip"><span class="n">2</span> Configurar</span>
                                <span class="step-chip"><span class="n">3</span> Comprar</span>
                            </div>
                        </div>
                    </div>
                    <div class="hero-pane">
                        <div class="card card-pad stack" style="box-shadow:none;">
                            <span class="section-kicker">Vitrine</span>
                            <h3 style="font-size:1.15rem;">{{ $products->count() }} produto(s) encontrado(s)</h3>
                            <p class="small muted">
                                @if ($categorySlug)
                                    Filtrado por categoria selecionada.
                                @else
                                    Exibindo todas as categorias disponíveis.
                                @endif
                            </p>
                            <div class="filter-pills">
                                @if ($search !== '')
                                    <span class="filter-pill"><span>Busca</span> <strong>{{ $search }}</strong></span>
                                @endif
                                @if ($categorySlug !== '')
                                    <span class="filter-pill"><span>Categoria</span> <strong>{{ $selectedCategory?->name }}</strong></span>
                                @endif
                                @if ($search === '' && $categorySlug === '')
                                    <span class="filter-pill"><strong>Sem filtros ativos</strong></span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="card card-pad stack-lg catalog-toolbar reveal-up" style="margin-bottom: 0;">
                <form method="GET" action="{{ route('catalog.index') }}" class="stack">
                    <div class="catalog-toolbar-inline">
                        <div class="field">
                            <label for="q">Buscar produto</label>
                            <input id="q" class="input" type="search" name="q" value="{{ $search }}" placeholder="Ex.: cartão, flyer, banner..." />
                        </div>
                        <div class="field catalog-toolbar-category-field">
                            <label for="categoria">Categoria</label>
                            <select id="categoria" class="select" name="categoria">
                                <option value="">Todas</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->slug }}" @selected($categorySlug === $category->slug)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                            </select>
                        </div>
                        <button class="btn btn-primary" type="submit">Aplicar filtros</button>
                        <a class="btn btn-secondary" href="{{ route('catalog.index') }}">Limpar</a>
                        <span class="small muted catalog-toolbar-count">{{ $products->count() }} produto(s) encontrado(s)</span>
                    </div>
                    <div class="filter-pills catalog-toolbar-categories">
                        @foreach ($categories as $category)
                            <a class="filter-pill" href="{{ route('catalog.index', ['categoria' => $category->slug, 'q' => $search ?: null]) }}" style="{{ $categorySlug === $category->slug ? 'box-shadow: inset 0 0 0 1px rgba(200,164,78,.28); color:#7f6120; background: rgba(200,164,78,.10);' : '' }}">
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </form>
            </section>

            <section class="stack-xl" style="margin-bottom: 0;">
                <div class="section-head">
                    <div class="copy">
                        <span class="section-kicker">Resultados</span>
                        <h2>
                            <span class="catalog-results-headline">
                                <span>{{ $products->count() ? 'Vitrine filtrada' : 'Nenhum produto encontrado' }}</span>
                                @if($selectedCategory)
                                    <span class="catalog-results-category-chip">
                                        <span class="dot" aria-hidden="true"></span>
                                        {{ $selectedCategory->name }}
                                    </span>
                                @endif
                            </span>
                        </h2>
                        <p class="muted">Refine os filtros ou limpe a busca para explorar todo o catálogo.</p>
                    </div>
                </div>

                <div class="grid grid-4">
                @forelse ($products as $product)
                    @include('store.partials.product-card', ['product' => $product])
                @empty
                    <div class="card card-pad">
                        <p class="muted">Nenhum produto encontrado com os filtros atuais.</p>
                    </div>
                @endforelse
                </div>
            </section>
        </div>
    </section>
@endsection
