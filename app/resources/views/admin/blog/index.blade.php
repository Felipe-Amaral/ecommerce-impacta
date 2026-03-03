@extends('layouts.store')

@section('title', 'Blog | Painel Administrativo')
@section('meta_description', 'Gestão de posts do blog com status, SEO e publicação agendada.')

@push('head')
    <style>
        .blog-status-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 5px 10px;
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .04em;
            text-transform: uppercase;
            border: 1px solid transparent;
        }

        .blog-status-chip::before {
            content: "";
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: currentColor;
        }

        .blog-status-chip.published {
            color: #0d7a53;
            border-color: rgba(15,138,95,.22);
            background: rgba(15,138,95,.10);
        }

        .blog-status-chip.draft {
            color: #7a5a14;
            border-color: rgba(198,161,74,.24);
            background: rgba(198,161,74,.13);
        }

        .blog-status-chip.scheduled {
            color: #1f5eff;
            border-color: rgba(31,94,255,.22);
            background: rgba(31,94,255,.11);
        }
    </style>
@endpush

@section('content')
    <section class="hero hero-studio reveal-up" style="margin-bottom: 18px;">
        <div class="hero-studio-grid">
            <div class="hero-pane">
                <div class="stack-xl">
                    <div class="pill-list">
                        <span class="badge badge-brand">Blog</span>
                        <span class="pill">Conteúdo e SEO</span>
                    </div>
                    <div class="stack">
                        <h1 class="text-gradient" style="font-size: clamp(1.8rem, 3vw, 2.7rem);">Vitrine editorial da marca</h1>
                        <p class="lead">Gerencie artigos, destaque conteúdos estratégicos e otimize SEO com título, descrição, canonical, OG e foco por palavra-chave.</p>
                    </div>
                    <div class="hero-actions">
                        <a href="{{ route('admin.blog.create') }}" class="btn btn-primary">Novo artigo</a>
                        <a href="{{ route('admin.blog.categories.index') }}" class="btn btn-secondary">Categorias</a>
                        <a href="{{ route('admin.blog.tags.index') }}" class="btn btn-secondary">Tags</a>
                        <a href="{{ route('blog.index') }}" class="btn btn-secondary" target="_blank" rel="noreferrer">Ver blog</a>
                    </div>
                </div>
            </div>
            <div class="hero-pane">
                <div class="metric-grid">
                    <div class="metric-card"><strong>{{ $stats['total'] }}</strong><span>Total de artigos</span></div>
                    <div class="metric-card"><strong>{{ $stats['published'] }}</strong><span>Publicados</span></div>
                    <div class="metric-card"><strong>{{ $stats['scheduled'] }}</strong><span>Agendados</span></div>
                </div>
                <div class="board-card stack" style="margin-top: 12px;">
                    <div class="board-title"><strong>Pipeline editorial</strong><span class="tiny muted">produção</span></div>
                    <div class="process-rail">
                        <div class="process-step">
                            <span class="num">{{ $stats['draft'] }}</span>
                            <span class="label">Rascunhos</span>
                            <span class="eta">em edição</span>
                        </div>
                        <div class="process-step">
                            <span class="num">{{ $stats['scheduled'] }}</span>
                            <span class="label">Agendados</span>
                            <span class="eta">fila</span>
                        </div>
                        <div class="process-step">
                            <span class="num">{{ $stats['published'] }}</span>
                            <span class="label">No ar</span>
                            <span class="eta">vitrine</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="card card-pad stack-lg" style="margin-bottom: 28px;">
        <form method="GET" action="{{ route('admin.blog.index') }}" class="form-grid-3">
            <div class="field">
                <label for="admin_blog_q">Buscar</label>
                <input id="admin_blog_q" class="input" type="search" name="q" value="{{ $filters['q'] }}" placeholder="Título, slug ou resumo">
            </div>

            <div class="field">
                <label for="admin_blog_status">Status</label>
                <select id="admin_blog_status" class="select" name="status">
                    <option value="">Todos</option>
                    <option value="draft" @selected($filters['status'] === 'draft')>Rascunho</option>
                    <option value="scheduled" @selected($filters['status'] === 'scheduled')>Agendado</option>
                    <option value="published" @selected($filters['status'] === 'published')>Publicado</option>
                </select>
            </div>

            <div class="field">
                <label for="admin_blog_category">Categoria</label>
                <select id="admin_blog_category" class="select" name="category_id">
                    <option value="">Todas</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected($filters['category_id'] !== '' && (int) $filters['category_id'] === $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <label class="radio-card" for="admin_blog_featured" style="margin-top: 20px;">
                <input id="admin_blog_featured" type="checkbox" name="featured" value="1" @checked($filters['featured'])>
                <span>Somente em destaque</span>
            </label>

            <div class="field" style="justify-content:end; margin-top: 20px;">
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <button type="submit" class="btn btn-secondary">Filtrar</button>
                    <a href="{{ route('admin.blog.index') }}" class="btn btn-secondary">Limpar</a>
                </div>
            </div>
        </form>

        <div class="table-wrap">
            <table class="table-compact">
                <thead>
                    <tr>
                        <th>Artigo</th>
                        <th>Status</th>
                        <th>Categoria</th>
                        <th>Publicação</th>
                        <th>SEO</th>
                        <th>Visualizações</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($posts as $post)
                    <tr>
                        <td>
                            <strong>{{ $post->title }}</strong>
                            <div class="tiny muted">/{{ $post->slug }}</div>
                            <div class="tiny muted">Tags: {{ $post->tags->pluck('name')->join(', ') ?: 'sem tags' }}</div>
                        </td>
                        <td>
                            <span class="blog-status-chip {{ $post->status }}">{{ $post->status === 'draft' ? 'Rascunho' : ($post->status === 'scheduled' ? 'Agendado' : 'Publicado') }}</span>
                            @if($post->is_featured)
                                <div class="tiny muted" style="margin-top:4px;">Destaque da vitrine</div>
                            @endif
                        </td>
                        <td>{{ $post->category?->name ?: 'Sem categoria' }}</td>
                        <td>
                            <div class="tiny">{{ optional($post->published_at)->format('d/m/Y H:i') ?: 'Não definido' }}</div>
                            <div class="tiny muted">Atualizado em {{ optional($post->updated_at)->format('d/m/Y H:i') }}</div>
                        </td>
                        <td>
                            <div class="tiny">Title: {{ $post->seo_title ? 'ok' : 'fallback' }}</div>
                            <div class="tiny">Desc: {{ $post->seo_description ? 'ok' : 'fallback' }}</div>
                            <div class="tiny">Noindex: {{ $post->seo_noindex ? 'sim' : 'não' }}</div>
                        </td>
                        <td>{{ (int) $post->views_count }}</td>
                        <td>
                            <div style="display:flex; gap:6px; flex-wrap:wrap;">
                                <a href="{{ route('admin.blog.edit', $post) }}" class="btn btn-secondary btn-sm">Editar</a>
                                @if($post->status !== 'published')
                                    <form method="POST" action="{{ route('admin.blog.publish', $post) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-secondary btn-sm">Publicar</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.blog.draft', $post) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-secondary btn-sm">Rascunho</button>
                                    </form>
                                @endif
                                <a href="{{ route('blog.show', $post->slug) }}" class="btn btn-secondary btn-sm" target="_blank" rel="noreferrer">Ver</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="small muted">Nenhum artigo encontrado.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $posts->links() }}
    </section>
@endsection
