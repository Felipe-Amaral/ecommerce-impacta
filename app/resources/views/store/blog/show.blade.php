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

    $coverImage = $resolveBlogImage($blogPost->cover_image_url);
@endphp

@section('title', $metaTitle)
@section('meta_description', $metaDescription)
@section('canonical_url', $canonical)
@section('meta_robots', $metaRobots)
@section('og_type', 'article')
@section('og_image', $ogImage)
@section('seo_json_ld')
    <script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
    <script type="application/ld+json">{!! json_encode($blogPostingSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
@endsection

@push('head')
    <style>
        .blog-article-shell {
            margin: 8px 0 30px;
            display: grid;
            gap: 14px;
        }

        .blog-article-wrap {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 300px;
            gap: 16px;
            align-items: start;
        }

        .blog-article-main {
            border-radius: 22px;
            border: 1px solid rgba(22,20,19,.08);
            background:
                radial-gradient(circle at 6% -10%, rgba(198,161,74,.12), transparent 46%),
                linear-gradient(180deg, rgba(255,255,255,.95), rgba(255,255,255,.88));
            box-shadow:
                0 22px 38px rgba(12,10,8,.08),
                inset 0 1px 0 rgba(255,255,255,.72);
            overflow: hidden;
        }

        .blog-article-head {
            padding: clamp(16px, 2.8vw, 26px);
            display: grid;
            gap: 10px;
            border-bottom: 1px solid rgba(22,20,19,.07);
            background:
                radial-gradient(circle at 92% -10%, rgba(31,94,255,.12), transparent 42%),
                linear-gradient(180deg, rgba(255,255,255,.88), rgba(255,255,255,.7));
        }

        .blog-article-title {
            margin: 0;
            font-size: clamp(1.7rem, 3vw, 2.7rem);
            line-height: 1.04;
            letter-spacing: -.01em;
            max-width: 20ch;
        }

        .blog-article-excerpt {
            margin: 0;
            color: #5e554b;
            font-size: 1rem;
            line-height: 1.55;
            max-width: 70ch;
        }

        .blog-article-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            font-weight: 700;
            color: #6d6358;
        }

        .blog-article-meta .dot {
            width: 4px;
            height: 4px;
            border-radius: 999px;
            background: rgba(109,99,88,.6);
        }

        .blog-article-cover {
            aspect-ratio: 16 / 7;
            background:
                radial-gradient(circle at 14% 12%, rgba(198,161,74,.24), transparent 40%),
                radial-gradient(circle at 90% 10%, rgba(31,94,255,.18), transparent 46%),
                linear-gradient(135deg, #efe6d5, #f8f4eb);
            background-size: cover;
            background-position: center;
            border-bottom: 1px solid rgba(22,20,19,.07);
        }

        .blog-article-content {
            padding: clamp(16px, 2.8vw, 28px);
            color: #2e2822;
            line-height: 1.72;
            font-size: 1.02rem;
        }

        .blog-article-content > :first-child {
            margin-top: 0;
        }

        .blog-article-content > :last-child {
            margin-bottom: 0;
        }

        .blog-article-content h2,
        .blog-article-content h3,
        .blog-article-content h4 {
            margin-top: 1.5em;
            margin-bottom: .45em;
            line-height: 1.2;
            color: #1e1915;
        }

        .blog-article-content h2 {
            font-size: 1.52rem;
        }

        .blog-article-content h3 {
            font-size: 1.28rem;
        }

        .blog-article-content p,
        .blog-article-content ul,
        .blog-article-content ol,
        .blog-article-content blockquote,
        .blog-article-content pre {
            margin-top: .75em;
            margin-bottom: .85em;
        }

        .blog-article-content a {
            color: #1f5eff;
            text-decoration: underline;
            text-decoration-color: rgba(31,94,255,.4);
            text-underline-offset: 2px;
        }

        .blog-article-content ul,
        .blog-article-content ol {
            padding-left: 1.2em;
        }

        .blog-article-content blockquote {
            margin-inline: 0;
            padding: 12px 14px;
            border-radius: 14px;
            border-left: 3px solid rgba(198,161,74,.72);
            background: rgba(198,161,74,.09);
            color: #5a4a28;
        }

        .blog-article-content code {
            border-radius: 8px;
            padding: 2px 6px;
            font-size: .89em;
            color: #5a4a28;
            background: rgba(198,161,74,.12);
        }

        .blog-article-content pre {
            overflow: auto;
            border-radius: 14px;
            border: 1px solid rgba(22,20,19,.08);
            background: rgba(27,22,16,.95);
            color: #f8f2e7;
            padding: 12px;
        }

        .blog-article-content pre code {
            background: transparent;
            color: inherit;
            padding: 0;
        }

        .blog-article-side {
            display: grid;
            gap: 12px;
        }

        .blog-side-box {
            border-radius: 16px;
            border: 1px solid rgba(22,20,19,.08);
            background:
                linear-gradient(180deg, rgba(255,255,255,.93), rgba(255,255,255,.84));
            box-shadow: 0 14px 26px rgba(12,10,8,.06);
            padding: 12px;
            display: grid;
            gap: 10px;
        }

        .blog-side-box h2 {
            margin: 0;
            font-size: .92rem;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: #5f4d22;
        }

        .blog-tag-cloud {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .blog-tag-cloud a {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid rgba(198,161,74,.2);
            background: rgba(198,161,74,.1);
            color: #674f1c;
            font-size: .76rem;
            font-weight: 700;
        }

        .blog-related-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .blog-related-card {
            border-radius: 14px;
            border: 1px solid rgba(22,20,19,.08);
            background: rgba(255,255,255,.9);
            overflow: hidden;
            display: grid;
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .blog-related-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 24px rgba(12,10,8,.08);
        }

        .blog-related-cover {
            aspect-ratio: 16 / 8;
            background:
                radial-gradient(circle at 12% 10%, rgba(198,161,74,.26), transparent 40%),
                linear-gradient(135deg, #efe6d5, #f8f4eb);
            background-size: cover;
            background-position: center;
            border-bottom: 1px solid rgba(22,20,19,.08);
        }

        .blog-related-body {
            padding: 10px;
            display: grid;
            gap: 6px;
        }

        .blog-related-title {
            margin: 0;
            font-size: .9rem;
            line-height: 1.3;
        }

        .blog-related-meta {
            font-size: .72rem;
            color: #786d61;
            text-transform: uppercase;
            letter-spacing: .05em;
            font-weight: 700;
        }

        .blog-preview-alert {
            border-radius: 14px;
            border: 1px dashed rgba(179,38,30,.3);
            background: rgba(179,38,30,.07);
            color: #7c2b22;
            padding: 12px 14px;
            font-size: .86rem;
            font-weight: 700;
        }

        @media (max-width: 1100px) {
            .blog-article-wrap {
                grid-template-columns: minmax(0, 1fr);
            }

            .blog-article-side {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 860px) {
            .blog-related-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .blog-article-side {
                grid-template-columns: minmax(0, 1fr);
            }
        }
    </style>
@endpush

@section('content')
    <section class="blog-article-shell">
        @if($isAdminPreview)
            <div class="blog-preview-alert">
                Pré-visualização de administrador: este artigo ainda não está publicado para visitantes.
            </div>
        @endif

        <nav aria-label="Breadcrumb" class="small muted">
            <a href="{{ route('home') }}">Início</a>
            <span aria-hidden="true"> / </span>
            <a href="{{ route('blog.index') }}">Blog</a>
            <span aria-hidden="true"> / </span>
            <span aria-current="page">{{ $blogPost->title }}</span>
        </nav>

        <section class="blog-article-wrap">
            <article class="blog-article-main">
                <header class="blog-article-head">
                    <div class="blog-article-meta">
                        <span>{{ optional($blogPost->published_at)->format('d/m/Y H:i') ?: 'Sem data' }}</span>
                        <span class="dot"></span>
                        <span>{{ max(1, (int) $blogPost->reading_time_minutes) }} min de leitura</span>
                        @if($blogPost->category)
                            <span class="dot"></span>
                            <a href="{{ route('blog.category', $blogPost->category->slug) }}">{{ $blogPost->category->name }}</a>
                        @endif
                        @if($blogPost->author)
                            <span class="dot"></span>
                            <span>por {{ $blogPost->author->name }}</span>
                        @endif
                    </div>

                    <h1 class="blog-article-title">{{ $blogPost->title }}</h1>

                    @if($blogPost->excerpt)
                        <p class="blog-article-excerpt">{{ $blogPost->excerpt }}</p>
                    @endif
                </header>

                <div class="blog-article-cover" @if($coverImage) style="background-image:url('{{ $coverImage }}');" @endif></div>

                <div class="blog-article-content">
                    {!! $articleHtml !!}
                </div>
            </article>

            <aside class="blog-article-side">
                <section class="blog-side-box">
                    <h2>Resumo</h2>
                    <div class="small muted" style="line-height:1.5;">
                        {{ max(1, (int) $blogPost->reading_time_minutes) }} min de leitura<br>
                        {{ $blogPost->views_count }} visualizaç{{ $blogPost->views_count === 1 ? 'ão' : 'ões' }}
                    </div>
                </section>

                <section class="blog-side-box">
                    <h2>Tags do artigo</h2>
                    <div class="blog-tag-cloud">
                        @forelse($blogPost->tags as $tag)
                            <a href="{{ route('blog.tag', $tag->slug) }}">#{{ $tag->name }}</a>
                        @empty
                            <span class="small muted">Sem tags.</span>
                        @endforelse
                    </div>
                </section>

                <section class="blog-side-box">
                    <h2>Explorar mais</h2>
                    <a class="btn btn-secondary btn-sm" href="{{ route('blog.index') }}">Ver vitrine do blog</a>
                    <a class="btn btn-primary btn-sm" href="{{ route('catalog.index') }}">Conhecer catálogo</a>
                </section>
            </aside>
        </section>

        @if($relatedPosts->isNotEmpty())
            <section class="card card-pad stack" style="margin-top:4px;">
                <div class="section-head">
                    <div class="copy">
                        <span class="section-kicker">Continue lendo</span>
                        <h2>Artigos relacionados</h2>
                    </div>
                </div>

                <div class="blog-related-grid">
                    @foreach($relatedPosts as $related)
                        @php($relatedImage = $resolveBlogImage($related->cover_image_url))
                        <article class="blog-related-card">
                            <a href="{{ route('blog.show', $related->slug) }}" class="blog-related-cover" @if($relatedImage) style="background-image:url('{{ $relatedImage }}');" @endif></a>
                            <div class="blog-related-body">
                                <p class="blog-related-meta">{{ optional($related->published_at)->format('d/m/Y') ?: 'Sem data' }} • {{ max(1, (int) $related->reading_time_minutes) }} min</p>
                                <h3 class="blog-related-title"><a href="{{ route('blog.show', $related->slug) }}">{{ $related->title }}</a></h3>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    </section>
@endsection
