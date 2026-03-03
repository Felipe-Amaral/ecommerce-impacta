@extends('layouts.store')

@php
    $resolveBlogImage = static function (?string $rawPath): ?string {
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

    $searchAction = match (true) {
        $activeCategory && ! $activeTag => route('blog.category', $activeCategory->slug),
        $activeTag && ! $activeCategory => route('blog.tag', $activeTag->slug),
        default => route('blog.index'),
    };

    $headline = match (true) {
        $activeCategory !== null => $activeCategory->name,
        $activeTag !== null => '#'.$activeTag->name,
        $search !== '' => 'Resultados para "'.$search.'"',
        default => 'Insights para elevar o impacto da sua marca impressa',
    };

    $subheadline = match (true) {
        $activeCategory !== null => $activeCategory->description ?: 'Conteúdo especializado da categoria selecionada.',
        $activeTag !== null => 'Conteúdos relacionados à tag '.$activeTag->name.'.',
        $search !== '' => 'Filtramos o conteúdo do blog com base na sua busca.',
        default => 'Tendências, dicas práticas e estratégias para materiais gráficos com performance comercial.',
    };

    $featuredImage = $resolveBlogImage($featuredPost?->cover_image_url);
@endphp

@section('title', $metaTitle)
@section('meta_description', $metaDescription)
@section('canonical_url', $canonical)
@section('meta_robots', $metaRobots)
@section('og_type', 'website')
@section('og_image', $ogImage)
@section('seo_json_ld')
    <script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
    <script type="application/ld+json">{!! json_encode($collectionSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
@endsection

@push('head')
    <style>
        .blog-shell {
            margin: 10px 0 30px;
            display: grid;
            gap: 18px;
        }

        .blog-hero-shell {
            position: relative;
            overflow: hidden;
            border-radius: 26px;
            border: 1px solid rgba(198,161,74,.18);
            background:
                radial-gradient(circle at 8% 0%, rgba(198,161,74,.16), transparent 46%),
                radial-gradient(circle at 96% 20%, rgba(31,94,255,.11), transparent 48%),
                linear-gradient(160deg, rgba(255,255,255,.96), rgba(247,242,234,.94));
            box-shadow:
                0 26px 48px rgba(14,11,9,.10),
                inset 0 1px 0 rgba(255,255,255,.74);
            padding: clamp(16px, 2.8vw, 28px);
            display: grid;
            gap: 16px;
        }

        .blog-hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(0, .8fr);
            gap: 16px;
            align-items: stretch;
        }

        .blog-kicker-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .blog-kicker-chip {
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

        .blog-kicker-chip::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 999px;
            background: linear-gradient(135deg, #b98c2f, #d3ad56);
            box-shadow: 0 0 0 3px rgba(198,161,74,.14);
        }

        .blog-headline {
            margin: 0;
            font-size: clamp(1.6rem, 3vw, 2.65rem);
            line-height: 1.03;
            letter-spacing: -.01em;
            max-width: 20ch;
        }

        .blog-subheadline {
            margin: 0;
            color: #60584f;
            font-size: .98rem;
            line-height: 1.55;
            max-width: 58ch;
        }

        .blog-search-form {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 8px;
            margin-top: 8px;
        }

        .blog-search-form .input {
            border-radius: 14px;
            min-height: 45px;
            border: 1px solid rgba(198,161,74,.18);
            background: rgba(255,255,255,.88);
        }

        .blog-search-form .btn {
            border-radius: 14px;
            min-height: 45px;
            padding-inline: 16px;
        }

        .blog-featured-card {
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

        .blog-featured-cover {
            position: absolute;
            inset: 0;
            z-index: 0;
            opacity: .7;
            background-size: cover;
            background-position: center;
        }

        .blog-featured-overlay {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(190deg, rgba(17,14,12,.16), rgba(17,14,12,.9) 68%),
                linear-gradient(130deg, rgba(198,161,74,.18), transparent 40%);
            z-index: 1;
        }

        .blog-featured-content {
            position: relative;
            z-index: 2;
            display: grid;
            align-content: end;
            gap: 10px;
            padding: 18px;
            min-height: 280px;
        }

        .blog-featured-title {
            margin: 0;
            font-size: clamp(1.1rem, 2vw, 1.55rem);
            line-height: 1.14;
        }

        .blog-featured-title a {
            color: inherit;
        }

        .blog-featured-meta {
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

        .blog-featured-meta .dot {
            width: 4px;
            height: 4px;
            border-radius: 999px;
            background: rgba(255,255,255,.66);
        }

        .blog-featured-excerpt {
            margin: 0;
            color: rgba(255,255,255,.9);
            font-size: .9rem;
            line-height: 1.5;
        }

        .blog-main-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 300px;
            gap: 16px;
            align-items: start;
        }

        .blog-feed {
            display: grid;
            gap: 14px;
        }

        .blog-feed-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .blog-card {
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

        .blog-card:hover {
            transform: translateY(-2px);
            border-color: rgba(198,161,74,.22);
            box-shadow:
                0 22px 34px rgba(12,10,8,.10),
                inset 0 1px 0 rgba(255,255,255,.78);
        }

        .blog-card-cover {
            aspect-ratio: 16 / 8;
            background:
                radial-gradient(circle at 12% 10%, rgba(198,161,74,.24), transparent 40%),
                radial-gradient(circle at 92% 4%, rgba(31,94,255,.18), transparent 45%),
                linear-gradient(135deg, #f8f4eb, #efe7d6);
            border-bottom: 1px solid rgba(22,20,19,.06);
            background-size: cover;
            background-position: center;
        }

        .blog-card-body {
            padding: 13px;
            display: grid;
            gap: 8px;
        }

        .blog-card-title {
            margin: 0;
            font-size: 1.02rem;
            line-height: 1.24;
        }

        .blog-card-title a {
            color: #1d1814;
        }

        .blog-card-excerpt {
            margin: 0;
            font-size: .88rem;
            line-height: 1.5;
            color: #5f574f;
        }

        .blog-meta-line {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            color: #70675d;
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .blog-meta-line .dot {
            width: 4px;
            height: 4px;
            border-radius: 999px;
            background: rgba(112,103,93,.56);
        }

        .blog-card-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .blog-mini-tag {
            display: inline-flex;
            align-items: center;
            padding: 5px 9px;
            border-radius: 999px;
            border: 1px solid rgba(198,161,74,.18);
            background: rgba(198,161,74,.10);
            color: #674f1c;
            font-size: .72rem;
            font-weight: 700;
            line-height: 1;
        }

        .blog-side {
            display: grid;
            gap: 12px;
        }

        .blog-side-box {
            border-radius: 18px;
            border: 1px solid rgba(22,20,19,.08);
            background:
                linear-gradient(180deg, rgba(255,255,255,.92), rgba(255,255,255,.83));
            box-shadow: 0 12px 24px rgba(12,10,8,.05);
            padding: 13px;
            display: grid;
            gap: 10px;
        }

        .blog-side-title {
            margin: 0;
            font-size: .94rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #5f4e24;
        }

        .blog-side-list {
            display: grid;
            gap: 7px;
        }

        .blog-side-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 8px 10px;
            border-radius: 12px;
            border: 1px solid rgba(22,20,19,.06);
            background: rgba(255,255,255,.86);
            font-size: .82rem;
            color: #3d342b;
            transition: transform .18s ease, border-color .18s ease;
        }

        .blog-side-link strong {
            font-weight: 700;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .blog-side-link span {
            flex-shrink: 0;
            border-radius: 999px;
            border: 1px solid rgba(198,161,74,.2);
            padding: 4px 7px;
            font-size: .7rem;
            font-weight: 700;
            color: #6a5320;
            background: rgba(198,161,74,.10);
        }

        .blog-side-link:hover {
            transform: translateY(-1px);
            border-color: rgba(198,161,74,.2);
        }

        .blog-side-link.is-active {
            border-color: rgba(198,161,74,.3);
            background:
                linear-gradient(180deg, rgba(255,255,255,.96), rgba(248,241,227,.92));
        }

        .blog-spotlight-item {
            display: grid;
            gap: 2px;
            padding: 8px 0;
            border-bottom: 1px dashed rgba(22,20,19,.1);
        }

        .blog-spotlight-item:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .blog-spotlight-item a {
            font-size: .84rem;
            font-weight: 700;
            color: #2a231d;
            line-height: 1.3;
        }

        .blog-spotlight-item .meta {
            font-size: .72rem;
            color: #74685a;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .blog-empty {
            border-radius: 16px;
            border: 1px dashed rgba(22,20,19,.2);
            padding: 20px;
            text-align: center;
            color: #655b52;
            background: rgba(255,255,255,.72);
        }

        @media (max-width: 1120px) {
            .blog-main-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .blog-side {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 860px) {
            .blog-hero-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .blog-feed-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .blog-side {
                grid-template-columns: minmax(0, 1fr);
            }
        }
    </style>
@endpush

@section('content')
    <section class="blog-shell">
        <section class="blog-hero-shell reveal-up">
            <div class="blog-kicker-row">
                <span class="blog-kicker-chip">Blog Uriah Criativa</span>
                @if($activeCategory)
                    <span class="pill">Categoria: {{ $activeCategory->name }}</span>
                @endif
                @if($activeTag)
                    <span class="pill">Tag: #{{ $activeTag->name }}</span>
                @endif
                @if($search !== '')
                    <span class="pill">Busca: {{ $search }}</span>
                @endif
            </div>

            <div class="blog-hero-grid">
                <div class="stack-lg">
                    <h1 class="blog-headline">{{ $headline }}</h1>
                    <p class="blog-subheadline">{{ $subheadline }}</p>

                    <form method="GET" action="{{ $searchAction }}" class="blog-search-form" role="search" aria-label="Buscar no blog">
                        @if($activeCategory && $searchAction === route('blog.index'))
                            <input type="hidden" name="categoria" value="{{ $activeCategory->slug }}">
                        @endif
                        @if($activeTag && $searchAction === route('blog.index'))
                            <input type="hidden" name="tag" value="{{ $activeTag->slug }}">
                        @endif
                        <input class="input" type="search" name="q" value="{{ $search }}" placeholder="Buscar por tema, acabamento, arquivo, campanha...">
                        <button class="btn btn-primary" type="submit">Buscar</button>
                    </form>
                </div>

                @if($featuredPost)
                    <article class="blog-featured-card">
                        @if($featuredImage)
                            <div class="blog-featured-cover" style="background-image:url('{{ $featuredImage }}');"></div>
                        @endif
                        <div class="blog-featured-overlay"></div>
                        <div class="blog-featured-content">
                            <div class="blog-featured-meta">
                                <span>{{ optional($featuredPost->published_at)->format('d/m/Y') ?: 'Sem data' }}</span>
                                <span class="dot"></span>
                                <span>{{ max(1, (int) $featuredPost->reading_time_minutes) }} min</span>
                                @if($featuredPost->category)
                                    <span class="dot"></span>
                                    <span>{{ $featuredPost->category->name }}</span>
                                @endif
                            </div>
                            <h2 class="blog-featured-title">
                                <a href="{{ route('blog.show', $featuredPost->slug) }}">{{ $featuredPost->title }}</a>
                            </h2>
                            <p class="blog-featured-excerpt">{{ $featuredPost->excerpt ?: 'Leitura recomendada da semana com foco comercial e acabamento gráfico.' }}</p>
                        </div>
                    </article>
                @else
                    <article class="blog-featured-card">
                        <div class="blog-featured-overlay"></div>
                        <div class="blog-featured-content">
                            <h2 class="blog-featured-title">Novos artigos em breve</h2>
                            <p class="blog-featured-excerpt">Estamos preparando conteúdos estratégicos sobre impressão, materiais e performance de campanhas.</p>
                        </div>
                    </article>
                @endif
            </div>
        </section>

        <section class="blog-main-grid">
            <div class="blog-feed">
                @if($posts->isEmpty())
                    <div class="blog-empty">
                        <strong>Nenhum artigo encontrado.</strong>
                        <div class="small" style="margin-top:6px;">Tente remover filtros ou buscar por outro termo.</div>
                    </div>
                @else
                    <div class="blog-feed-grid">
                        @foreach($posts as $post)
                            @php
                                $postImage = $resolveBlogImage($post->cover_image_url);
                            @endphp
                            <article class="blog-card reveal-up">
                                <a href="{{ route('blog.show', $post->slug) }}" class="blog-card-cover" @if($postImage) style="background-image:url('{{ $postImage }}');" @endif aria-label="Abrir artigo {{ $post->title }}"></a>
                                <div class="blog-card-body">
                                    <div class="blog-meta-line">
                                        <span>{{ optional($post->published_at)->format('d/m/Y') ?: 'Sem data' }}</span>
                                        <span class="dot"></span>
                                        <span>{{ max(1, (int) $post->reading_time_minutes) }} min</span>
                                        @if($post->category)
                                            <span class="dot"></span>
                                            <span>{{ $post->category->name }}</span>
                                        @endif
                                    </div>

                                    <h3 class="blog-card-title">
                                        <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                                    </h3>

                                    <p class="blog-card-excerpt">{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags((string) $post->content), 150) }}</p>

                                    @if($post->tags->isNotEmpty())
                                        <div class="blog-card-tags">
                                            @foreach($post->tags->take(3) as $tag)
                                                <a href="{{ route('blog.tag', $tag->slug) }}" class="blog-mini-tag">#{{ $tag->name }}</a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>

                    {{ $posts->links() }}
                @endif
            </div>

            <aside class="blog-side">
                <section class="blog-side-box">
                    <h2 class="blog-side-title">Categorias</h2>
                    <div class="blog-side-list">
                        <a href="{{ route('blog.index') }}" class="blog-side-link {{ !$activeCategory && !$activeTag && $search === '' ? 'is-active' : '' }}">
                            <strong>Todas</strong>
                            <span>{{ $categories->sum('published_posts_count') }}</span>
                        </a>
                        @foreach($categories as $category)
                            <a href="{{ route('blog.category', $category->slug) }}" class="blog-side-link {{ $activeCategory?->id === $category->id ? 'is-active' : '' }}">
                                <strong>{{ $category->name }}</strong>
                                <span>{{ $category->published_posts_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </section>

                <section class="blog-side-box">
                    <h2 class="blog-side-title">Tags populares</h2>
                    <div class="blog-side-list">
                        @forelse($popularTags as $tag)
                            <a href="{{ route('blog.tag', $tag->slug) }}" class="blog-side-link {{ $activeTag?->id === $tag->id ? 'is-active' : '' }}">
                                <strong>#{{ $tag->name }}</strong>
                                <span>{{ $tag->published_posts_count }}</span>
                            </a>
                        @empty
                            <span class="small muted">Nenhuma tag publicada ainda.</span>
                        @endforelse
                    </div>
                </section>

                <section class="blog-side-box">
                    <h2 class="blog-side-title">Mais lidos</h2>
                    @forelse($spotlightPosts as $spotlight)
                        <article class="blog-spotlight-item">
                            <a href="{{ route('blog.show', $spotlight->slug) }}">{{ $spotlight->title }}</a>
                            <span class="meta">{{ optional($spotlight->published_at)->format('d/m/Y') ?: 'Sem data' }} • {{ $spotlight->views_count }} visualizações</span>
                        </article>
                    @empty
                        <span class="small muted">Sem histórico de visualização ainda.</span>
                    @endforelse
                </section>
            </aside>
        </section>
    </section>
@endsection
