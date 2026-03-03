@extends('layouts.store')

@section('title', 'Cadastros | Painel da Gráfica')
@section('meta_description', 'Cadastros de categorias, produtos e variações da loja.')

@section('content')
    <section class="hero hero-studio reveal-up" style="margin-bottom: 18px;">
        <div class="hero-studio-grid">
            <div class="hero-pane">
                <div class="stack-xl">
                    <div class="pill-list">
                        <span class="badge badge-brand">Cadastros</span>
                        <span class="pill">Catálogo da loja</span>
                    </div>
                    <div class="stack">
                        <h1 class="text-gradient" style="font-size: clamp(1.8rem, 3vw, 2.7rem);">Gerencie categorias, produtos e variações</h1>
                        <p class="lead">Cadastre novos itens da gráfica, organize o catálogo e atualize preços, tiragens e prazos de produção.</p>
                    </div>
                    <div class="hero-actions">
                        <a href="{{ route('admin.catalog.categories.index') }}" class="btn btn-secondary">Categorias</a>
                        <a href="{{ route('admin.catalog.products.index') }}" class="btn btn-primary">Produtos e variações</a>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Voltar ao painel</a>
                    </div>
                </div>
            </div>
            <div class="hero-pane">
                <div class="metric-grid">
                    <div class="metric-card"><strong>{{ $stats['categories'] }}</strong><span>Categorias</span></div>
                    <div class="metric-card"><strong>{{ $stats['products'] }}</strong><span>Produtos</span></div>
                    <div class="metric-card"><strong>{{ $stats['variants'] }}</strong><span>Variações</span></div>
                </div>
                <div class="board-card stack" style="margin-top: 12px;">
                    <div class="board-title"><strong>Resumo do catálogo</strong><span class="tiny muted">rápido</span></div>
                    <div class="process-rail">
                        <div class="process-step">
                            <span class="num">{{ $stats['featured_products'] }}</span>
                            <span class="label">Produtos em destaque</span>
                            <span class="eta">vitrine</span>
                        </div>
                        <div class="process-step">
                            <span class="num">{{ $stats['inactive_products'] }}</span>
                            <span class="label">Produtos inativos</span>
                            <span class="eta">ocultos</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="grid grid-2" style="margin-bottom: 28px;">
        <article class="card card-pad stack">
            <div class="link-row">
                <h2 style="font-size:1.2rem;">Categorias</h2>
                <a href="{{ route('admin.catalog.categories.index') }}" class="btn btn-secondary btn-sm">Gerenciar</a>
            </div>
            <p class="small muted">Estruture o catálogo da gráfica por linha comercial (papelaria, promocionais, comunicação visual etc.).</p>
            <ul class="clean-list small muted">
                <li>• Nome, slug, ordem e SEO</li>
                <li>• Ativar / desativar categoria</li>
                <li>• Organização da navegação pública</li>
            </ul>
        </article>

        <article class="card card-pad stack">
            <div class="link-row">
                <h2 style="font-size:1.2rem;">Produtos e variações</h2>
                <a href="{{ route('admin.catalog.products.index') }}" class="btn btn-primary btn-sm">Abrir</a>
            </div>
            <p class="small muted">Cadastre produtos de gráfica e configure variações por tiragem, acabamento, preço e prazo.</p>
            <ul class="clean-list small muted">
                <li>• Dados comerciais e SEO do produto</li>
                <li>• Variações com SKU, preço e prazo</li>
                <li>• Atributos em JSON para tiragem/acabamento</li>
            </ul>
        </article>

        <article class="card card-pad stack">
            <div class="link-row">
                <h2 style="font-size:1.2rem;">Banners da home</h2>
                <a href="{{ route('admin.catalog.banners.index') }}" class="btn btn-secondary btn-sm">Gerenciar</a>
            </div>
            <p class="small muted">Cadastre banners rotativos logo abaixo do menu superior com textos, CTAs, ordem e ativação.</p>
            <ul class="clean-list small muted">
                <li>• Título, subtítulo e chamadas de ação</li>
                <li>• Ordem de exibição e ativar/desativar</li>
                <li>• Tema visual premium por banner</li>
            </ul>
            <div class="tiny muted">Total cadastrado: {{ $stats['banners'] }}</div>
        </article>

        <article class="card card-pad stack">
            <div class="link-row">
                <h2 style="font-size:1.2rem;">Blog e SEO</h2>
                <a href="{{ route('admin.blog.index') }}" class="btn btn-secondary btn-sm">Gerenciar</a>
            </div>
            <p class="small muted">Construa a vitrine editorial da marca com artigos, categorias, tags e campos avançados de SEO.</p>
            <ul class="clean-list small muted">
                <li>• Publicação imediata, rascunho e agendamento</li>
                <li>• Meta title/description, canonical e Open Graph</li>
                <li>• Organização por categorias e tags temáticas</li>
            </ul>
            <div class="tiny muted">
                Artigos: {{ $stats['blog_posts'] }} • Categorias: {{ $stats['blog_categories'] }} • Tags: {{ $stats['blog_tags'] }}
            </div>
        </article>
    </section>

    <section class="card card-pad stack">
        <div class="section-head">
            <div class="copy">
                <span class="section-kicker">Recentes</span>
                <h2>Últimos produtos cadastrados</h2>
            </div>
        </div>

        @if($recentProducts->isEmpty())
            <p class="small muted">Nenhum produto cadastrado ainda.</p>
        @else
            <div class="table-wrap">
                <table class="table-compact">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Categoria</th>
                            <th>Status</th>
                            <th>Base</th>
                            <th>Variações</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($recentProducts as $product)
                        <tr>
                            <td>
                                <strong>{{ $product->name }}</strong>
                                <div class="tiny muted">{{ $product->slug }} • {{ $product->sku }}</div>
                            </td>
                            <td>{{ $product->category?->name ?: 'Sem categoria' }}</td>
                            <td>
                                <span class="status-dot {{ $product->is_active ? 'production' : 'pending' }}">{{ $product->is_active ? 'ativo' : 'inativo' }}</span>
                            </td>
                            <td>R$ {{ number_format((float) $product->base_price, 2, ',', '.') }}</td>
                            <td>{{ $product->variants->count() }}</td>
                            <td><a href="{{ route('admin.catalog.products.edit', $product) }}" class="btn btn-secondary btn-sm">Editar</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
