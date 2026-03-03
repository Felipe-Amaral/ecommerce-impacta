@extends('layouts.store')

@section('title', 'Produtos | Cadastros')

@section('content')
    <section class="hero reveal-up" style="margin-bottom: 18px;">
        <div class="link-row" style="align-items:flex-start;">
            <div class="stack" style="gap:6px;">
                <p class="eyebrow">Cadastros</p>
                <h1 style="font-size: clamp(1.6rem, 3vw, 2.3rem);">Produtos e variações</h1>
                <p class="lead">Gerencie produtos da gráfica, preços base e variantes por tiragem/acabamento.</p>
            </div>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <a href="{{ route('admin.catalog.index') }}" class="btn btn-secondary">Voltar aos cadastros</a>
                <a href="{{ route('admin.catalog.products.create') }}" class="btn btn-primary">Novo produto</a>
            </div>
        </div>
    </section>

    <section class="card card-pad stack-lg" style="margin-bottom: 28px;">
        <form method="GET" action="{{ route('admin.catalog.products.index') }}" class="form-grid">
            <div class="field">
                <label for="filter_q">Buscar</label>
                <input id="filter_q" class="input" type="search" name="q" value="{{ $filters['q'] }}" placeholder="Nome, slug ou SKU">
            </div>
            <div class="field">
                <label for="filter_category">Categoria</label>
                <select id="filter_category" class="select" name="category_id">
                    <option value="">Todas</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected($filters['category_id'] !== '' && (int) $filters['category_id'] === $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field" style="justify-content:end;">
                <label>&nbsp;</label>
                <div style="display:flex; gap:8px;">
                    <button class="btn btn-secondary" type="submit">Filtrar</button>
                    <a href="{{ route('admin.catalog.products.index') }}" class="btn btn-secondary">Limpar</a>
                </div>
            </div>
        </form>

        <div class="table-wrap">
            <table class="table-compact">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Categoria</th>
                        <th>Preço base</th>
                        <th>Prazo</th>
                        <th>Variações</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            <strong>{{ $product->name }}</strong>
                            <div class="tiny muted">{{ $product->sku }} • {{ $product->slug }}</div>
                        </td>
                        <td>{{ $product->category?->name ?: 'Sem categoria' }}</td>
                        <td>R$ {{ number_format((float) $product->base_price, 2, ',', '.') }}</td>
                        <td>{{ $product->lead_time_days }} dia(s)</td>
                        <td>{{ $product->variants_count }}</td>
                        <td>
                            <div class="tiny">{{ $product->is_active ? 'Ativo' : 'Inativo' }}</div>
                            <div class="tiny muted">{{ $product->is_featured ? 'Destaque' : 'Normal' }}</div>
                        </td>
                        <td><a href="{{ route('admin.catalog.products.edit', $product) }}" class="btn btn-secondary btn-sm">Editar</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="small muted">Nenhum produto encontrado.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $products->links() }}
    </section>
@endsection
