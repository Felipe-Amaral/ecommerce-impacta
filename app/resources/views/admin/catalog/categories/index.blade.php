@extends('layouts.store')

@section('title', 'Categorias | Cadastros')

@section('content')
    <section class="hero reveal-up" style="margin-bottom: 18px;">
        <div class="link-row" style="align-items:flex-start;">
            <div class="stack" style="gap:6px;">
                <p class="eyebrow">Cadastros</p>
                <h1 style="font-size: clamp(1.6rem, 3vw, 2.3rem);">Categorias do catálogo</h1>
                <p class="lead">Crie e organize as categorias que aparecem na vitrine da gráfica.</p>
            </div>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <a href="{{ route('admin.catalog.index') }}" class="btn btn-secondary">Voltar aos cadastros</a>
                <a href="{{ route('admin.catalog.products.index') }}" class="btn btn-primary">Produtos</a>
            </div>
        </div>
    </section>

    <section class="split" style="margin-bottom: 28px;">
        <section class="card card-pad stack">
            <div class="link-row">
                <h2 style="font-size:1.2rem;">Categorias cadastradas</h2>
                <span class="badge">{{ $categories->count() }}</span>
            </div>
            @if($categories->isEmpty())
                <p class="small muted">Nenhuma categoria cadastrada.</p>
            @else
                <div class="table-wrap">
                    <table class="table-compact">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Slug</th>
                                <th>Ordem</th>
                                <th>Status</th>
                                <th>Produtos</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($categories as $category)
                            <tr>
                                <td>
                                    <strong>{{ $category->name }}</strong>
                                    @if($category->description)
                                        <div class="tiny muted">{{ \Illuminate\Support\Str::limit($category->description, 80) }}</div>
                                    @endif
                                </td>
                                <td class="mono">{{ $category->slug }}</td>
                                <td>{{ $category->sort_order }}</td>
                                <td>{{ $category->is_active ? 'Ativa' : 'Inativa' }}</td>
                                <td>{{ $category->products_count }}</td>
                                <td><a class="btn btn-secondary btn-sm" href="{{ route('admin.catalog.categories.edit', $category) }}">Editar</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <aside class="card card-pad stack-lg floating-sticky">
            <div class="section-head">
                <div class="copy">
                    <span class="section-kicker">Nova categoria</span>
                    <h2 style="font-size:1.25rem;">Cadastrar</h2>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.catalog.categories.store') }}" class="stack">
                @csrf
                <div class="field">
                    <label for="category_name">Nome</label>
                    <input id="category_name" class="input" name="name" value="{{ old('name') }}" required>
                </div>
                <div class="field">
                    <label for="category_slug">Slug (opcional)</label>
                    <input id="category_slug" class="input" name="slug" value="{{ old('slug') }}" placeholder="gerado automaticamente se vazio">
                </div>
                <div class="field">
                    <label for="category_sort_order">Ordem</label>
                    <input id="category_sort_order" class="input" type="number" min="0" name="sort_order" value="{{ old('sort_order', 0) }}">
                </div>
                <div class="field">
                    <label for="category_description">Descrição</label>
                    <textarea id="category_description" class="textarea" name="description">{{ old('description') }}</textarea>
                </div>
                <div class="field">
                    <label for="category_meta_title">Meta title (opcional)</label>
                    <input id="category_meta_title" class="input" name="meta_title" value="{{ old('meta_title') }}">
                </div>
                <div class="field">
                    <label for="category_meta_description">Meta description (opcional)</label>
                    <input id="category_meta_description" class="input" name="meta_description" value="{{ old('meta_description') }}">
                </div>
                <label class="radio-card" for="category_is_active">
                    <input id="category_is_active" type="checkbox" name="is_active" value="1" @checked(old('is_active', true))>
                    <span>Categoria ativa</span>
                </label>
                <button type="submit" class="btn btn-primary">Salvar categoria</button>
            </form>
        </aside>
    </section>
@endsection
