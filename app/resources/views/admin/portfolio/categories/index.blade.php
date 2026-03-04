@extends('layouts.store')

@section('title', 'Categorias do Portfólio | Admin')
@section('meta_description', 'Gestão de categorias da vitrine de cases do portfólio.')

@section('content')
    <section class="hero reveal-up" style="margin-bottom: 18px;">
        <div class="link-row" style="align-items:flex-start;">
            <div class="stack" style="gap:6px;">
                <p class="eyebrow">Portfólio</p>
                <h1 style="font-size: clamp(1.6rem, 3vw, 2.3rem);">Categorias de cases</h1>
                <p class="lead">Organize a vitrine por tema e melhore navegação, filtros e SEO por categoria.</p>
            </div>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <a href="{{ route('admin.portfolio.index') }}" class="btn btn-secondary">Cases</a>
            </div>
        </div>
    </section>

    <section class="split" style="margin-bottom: 28px;">
        <section class="card card-pad stack-lg">
            <div class="section-head">
                <div class="copy">
                    <span class="section-kicker">Lista</span>
                    <h2>Categorias cadastradas</h2>
                </div>
            </div>

            @if($categories->isEmpty())
                <p class="small muted">Nenhuma categoria cadastrada ainda.</p>
            @else
                <div class="stack" style="gap:10px;">
                    @foreach($categories as $item)
                        <form method="POST" action="{{ route('admin.portfolio.categories.update', $item) }}" class="card" style="padding:12px; display:grid; gap:10px;">
                            @csrf
                            @method('PUT')

                            <div class="form-grid">
                                <div class="field">
                                    <label for="portfolio_category_name_{{ $item->id }}">
                                        Nome
                                        @include('partials.help-hint', ['text' => 'Nome exibido para visitantes ao filtrar e navegar pelos cases.'])
                                    </label>
                                    <input id="portfolio_category_name_{{ $item->id }}" class="input" name="name" value="{{ old('name.'.$item->id, $item->name) }}" required>
                                </div>
                                <div class="field">
                                    <label for="portfolio_category_slug_{{ $item->id }}">
                                        Slug
                                        @include('partials.help-hint', ['text' => 'Parte da URL da categoria. Ex.: papelaria-corporativa.'])
                                    </label>
                                    <input id="portfolio_category_slug_{{ $item->id }}" class="input mono" name="slug" value="{{ old('slug.'.$item->id, $item->slug) }}">
                                </div>
                                <div class="field">
                                    <label for="portfolio_category_color_{{ $item->id }}">
                                        Cor HEX
                                        @include('partials.help-hint', ['text' => 'Cor de apoio para pílulas e marcações visuais da categoria.'])
                                    </label>
                                    <input id="portfolio_category_color_{{ $item->id }}" class="input" name="color_hex" value="{{ old('color_hex.'.$item->id, $item->color_hex) }}" placeholder="#B56A24">
                                </div>
                                <div class="field">
                                    <label for="portfolio_category_sort_{{ $item->id }}">
                                        Ordem
                                        @include('partials.help-hint', ['text' => 'Menor número aparece primeiro no filtro de categorias.'])
                                    </label>
                                    <input id="portfolio_category_sort_{{ $item->id }}" class="input" type="number" min="0" name="sort_order" value="{{ old('sort_order.'.$item->id, $item->sort_order) }}">
                                </div>
                                <div class="field full">
                                    <label for="portfolio_category_desc_{{ $item->id }}">
                                        Descrição
                                        @include('partials.help-hint', ['text' => 'Texto para contextualizar a categoria na página pública.'])
                                    </label>
                                    <textarea id="portfolio_category_desc_{{ $item->id }}" class="textarea" name="description" style="min-height:80px;">{{ old('description.'.$item->id, $item->description) }}</textarea>
                                </div>
                                <div class="field">
                                    <label for="portfolio_category_seo_title_{{ $item->id }}">
                                        SEO title
                                        @include('partials.help-hint', ['text' => 'Título específico da categoria para buscadores.'])
                                    </label>
                                    <input id="portfolio_category_seo_title_{{ $item->id }}" class="input" name="seo_title" value="{{ old('seo_title.'.$item->id, $item->seo_title) }}">
                                </div>
                                <div class="field">
                                    <label for="portfolio_category_seo_desc_{{ $item->id }}">
                                        SEO description
                                        @include('partials.help-hint', ['text' => 'Resumo da categoria para snippets e redes.'])
                                    </label>
                                    <input id="portfolio_category_seo_desc_{{ $item->id }}" class="input" name="seo_description" value="{{ old('seo_description.'.$item->id, $item->seo_description) }}">
                                </div>
                                <label class="radio-card" for="portfolio_category_is_active_{{ $item->id }}" style="margin-top: 22px;">
                                    <input id="portfolio_category_is_active_{{ $item->id }}" type="checkbox" name="is_active" value="1" @checked(old('is_active.'.$item->id, $item->is_active))>
                                    <span>Ativa</span>
                                </label>
                            </div>

                            <div class="link-row">
                                <div class="tiny muted">{{ $item->projects_count }} case(s) vinculado(s)</div>
                                <div style="display:flex; gap:8px;">
                                    <button type="submit" class="btn btn-secondary btn-sm">Salvar</button>
                                </div>
                            </div>
                        </form>
                    @endforeach
                </div>
            @endif
        </section>

        <aside class="stack-lg">
            <div class="floating-sticky">
                <div class="card card-pad stack-lg">
                    <div class="section-head">
                        <div class="copy">
                            <span class="section-kicker">Nova categoria</span>
                            <h2 style="font-size:1.15rem;">Cadastrar</h2>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.portfolio.categories.store') }}" class="stack">
                        @csrf

                        <div class="field">
                            <label for="new_portfolio_category_name">
                                Nome
                                @include('partials.help-hint', ['text' => 'Nome exibido para visitantes ao filtrar e navegar pelos cases.'])
                            </label>
                            <input id="new_portfolio_category_name" class="input" name="name" value="{{ old('name', $category->name) }}" required>
                        </div>

                        <div class="field">
                            <label for="new_portfolio_category_slug">
                                Slug
                                @include('partials.help-hint', ['text' => 'Parte da URL da categoria. Ex.: papelaria-corporativa.'])
                            </label>
                            <input id="new_portfolio_category_slug" class="input" name="slug" value="{{ old('slug', $category->slug) }}">
                        </div>

                        <div class="field">
                            <label for="new_portfolio_category_color">
                                Cor HEX
                                @include('partials.help-hint', ['text' => 'Cor de apoio para pílulas e marcações visuais da categoria.'])
                            </label>
                            <input id="new_portfolio_category_color" class="input" name="color_hex" value="{{ old('color_hex', $category->color_hex) }}" placeholder="#B56A24">
                        </div>

                        <div class="field">
                            <label for="new_portfolio_category_sort">
                                Ordem
                                @include('partials.help-hint', ['text' => 'Menor número aparece primeiro no filtro de categorias.'])
                            </label>
                            <input id="new_portfolio_category_sort" class="input" type="number" min="0" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}">
                        </div>

                        <div class="field">
                            <label for="new_portfolio_category_desc">
                                Descrição
                                @include('partials.help-hint', ['text' => 'Texto para contextualizar a categoria na página pública.'])
                            </label>
                            <textarea id="new_portfolio_category_desc" class="textarea" name="description" style="min-height:90px;">{{ old('description', $category->description) }}</textarea>
                        </div>

                        <div class="field">
                            <label for="new_portfolio_category_seo_title">
                                SEO title
                                @include('partials.help-hint', ['text' => 'Título específico da categoria para buscadores.'])
                            </label>
                            <input id="new_portfolio_category_seo_title" class="input" name="seo_title" value="{{ old('seo_title', $category->seo_title) }}">
                        </div>

                        <div class="field">
                            <label for="new_portfolio_category_seo_description">
                                SEO description
                                @include('partials.help-hint', ['text' => 'Resumo da categoria para snippets e redes.'])
                            </label>
                            <input id="new_portfolio_category_seo_description" class="input" name="seo_description" value="{{ old('seo_description', $category->seo_description) }}">
                        </div>

                        <label class="radio-card" for="new_portfolio_category_active">
                            <input id="new_portfolio_category_active" type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active))>
                            <span>Categoria ativa</span>
                        </label>

                        <button type="submit" class="btn btn-primary">Criar categoria</button>
                    </form>
                </div>
            </div>
        </aside>
    </section>
@endsection
