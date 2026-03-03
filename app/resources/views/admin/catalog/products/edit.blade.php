@extends('layouts.store')

@section('title', 'Editar Produto | Cadastros')

@section('content')
    <section class="hero reveal-up" style="margin-bottom: 18px;">
        <div class="link-row" style="align-items:flex-start;">
            <div class="stack" style="gap:6px;">
                <p class="eyebrow">Cadastros</p>
                <h1 style="font-size: clamp(1.6rem, 3vw, 2.3rem);">Editar produto e variações</h1>
                <p class="lead">{{ $product->name }} • gerencie dados comerciais, SEO e variações de impressão.</p>
            </div>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <a href="{{ route('catalog.show', $product->slug) }}" class="btn btn-secondary" target="_blank" rel="noreferrer">Ver na loja</a>
                <a href="{{ route('admin.catalog.products.index') }}" class="btn btn-secondary">Voltar</a>
            </div>
        </div>
    </section>

    <section class="stack-lg" style="margin-bottom: 28px;">
        @include('admin.catalog.products._form', compact('product', 'categories', 'specificationsText'))

        <div class="card card-pad stack-lg">
            <div class="section-head">
                <div class="copy">
                    <span class="section-kicker">Variações</span>
                    <h2>Preço, tiragem e acabamento</h2>
                    <p class="small muted">Cadastre as combinações comerciais que o cliente escolhe no produto.</p>
                </div>
            </div>

            @if($product->variants->isEmpty())
                <p class="small muted">Nenhuma variação cadastrada ainda.</p>
            @else
                <div class="stack">
                    @foreach($product->variants as $variant)
                        <article class="glass-panel stack">
                            <div class="link-row">
                                <div class="stack" style="gap:2px;">
                                    <strong>{{ $variant->name }}</strong>
                                    <span class="tiny muted">{{ $variant->sku }} • ordem {{ $variant->sort_order }}</span>
                                </div>
                                <span class="badge">{{ $variant->is_active ? 'ativa' : 'inativa' }}</span>
                            </div>

                            <form method="POST" action="{{ route('admin.catalog.products.variants.update', [$product, $variant]) }}" class="stack">
                                @csrf
                                @method('PUT')
                                <div class="form-grid-3">
                                    <div class="field">
                                        <label>Nome</label>
                                        <input class="input" name="name" value="{{ old('name', $variant->name) }}">
                                    </div>
                                    <div class="field">
                                        <label>SKU</label>
                                        <input class="input" name="sku" value="{{ old('sku', $variant->sku) }}">
                                    </div>
                                    <div class="field">
                                        <label>Ordem</label>
                                        <input class="input" type="number" min="0" name="sort_order" value="{{ old('sort_order', $variant->sort_order) }}">
                                    </div>
                                </div>

                                <div class="form-grid-3">
                                    <div class="field">
                                        <label>Preço</label>
                                        <input class="input" type="number" step="0.01" min="0" name="price" value="{{ old('price', $variant->price) }}">
                                    </div>
                                    <div class="field">
                                        <label>Promoção (opcional)</label>
                                        <input class="input" type="number" step="0.01" min="0" name="promotional_price" value="{{ old('promotional_price', $variant->promotional_price) }}">
                                    </div>
                                    <div class="field">
                                        <label>Prazo (dias)</label>
                                        <input class="input" type="number" min="0" name="production_days" value="{{ old('production_days', $variant->production_days) }}">
                                    </div>
                                </div>

                                <div class="form-grid-3">
                                    <div class="field">
                                        <label>Peso (g)</label>
                                        <input class="input" type="number" min="0" name="weight_grams" value="{{ old('weight_grams', $variant->weight_grams) }}">
                                    </div>
                                    <div class="field">
                                        <label>Estoque (opcional)</label>
                                        <input class="input" type="number" min="0" name="stock_qty" value="{{ old('stock_qty', $variant->stock_qty) }}">
                                    </div>
                                    <label class="radio-card" style="align-self:end;" for="variant_active_{{ $variant->id }}">
                                        <input id="variant_active_{{ $variant->id }}" type="checkbox" name="is_active" value="1" @checked(old('is_active', $variant->is_active))>
                                        <span>Variação ativa</span>
                                    </label>
                                </div>

                                <div class="field">
                                    <label>Atributos (JSON)</label>
                                    <textarea class="textarea mono" name="attributes_json" style="min-height:110px;">{{ old('attributes_json', json_encode($variant->attributes ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) }}</textarea>
                                    <div class="tiny muted">Ex.: {"tiragem":"500","papel":"Couchê 300g","acabamento":"Laminação fosca"}</div>
                                </div>

                                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                    <button type="submit" class="btn btn-primary btn-sm">Salvar variação</button>
                                </div>
                            </form>

                            <form method="POST" action="{{ route('admin.catalog.products.variants.destroy', [$product, $variant]) }}" onsubmit="return confirm('Remover esta variação?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-secondary btn-sm">Remover variação</button>
                            </form>
                        </article>
                    @endforeach
                </div>
            @endif

            <section class="card card-pad stack">
                <div class="section-head">
                    <div class="copy">
                        <span class="section-kicker">Nova variação</span>
                        <h3>Cadastrar nova opção</h3>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.catalog.products.variants.store', $product) }}" class="stack">
                    @csrf
                    <div class="form-grid-3">
                        <div class="field">
                            <label for="new_variant_name">Nome</label>
                            <input id="new_variant_name" class="input" name="name" placeholder="500 un | Couchê 300g | Laminação fosca" required>
                        </div>
                        <div class="field">
                            <label for="new_variant_sku">SKU</label>
                            <input id="new_variant_sku" class="input" name="sku" placeholder="CVP-500-C300-LF" required>
                        </div>
                        <div class="field">
                            <label for="new_variant_sort_order">Ordem</label>
                            <input id="new_variant_sort_order" class="input" type="number" min="0" name="sort_order" value="{{ ($product->variants->max('sort_order') ?? 0) + 1 }}">
                        </div>
                    </div>
                    <div class="form-grid-3">
                        <div class="field">
                            <label for="new_variant_price">Preço</label>
                            <input id="new_variant_price" class="input" type="number" step="0.01" min="0" name="price" required>
                        </div>
                        <div class="field">
                            <label for="new_variant_promotional_price">Promoção (opcional)</label>
                            <input id="new_variant_promotional_price" class="input" type="number" step="0.01" min="0" name="promotional_price">
                        </div>
                        <div class="field">
                            <label for="new_variant_production_days">Prazo (dias)</label>
                            <input id="new_variant_production_days" class="input" type="number" min="0" name="production_days">
                        </div>
                    </div>
                    <div class="form-grid-3">
                        <div class="field">
                            <label for="new_variant_weight_grams">Peso (g)</label>
                            <input id="new_variant_weight_grams" class="input" type="number" min="0" name="weight_grams">
                        </div>
                        <div class="field">
                            <label for="new_variant_stock_qty">Estoque</label>
                            <input id="new_variant_stock_qty" class="input" type="number" min="0" name="stock_qty">
                        </div>
                        <label class="radio-card" style="align-self:end;" for="new_variant_is_active">
                            <input id="new_variant_is_active" type="checkbox" name="is_active" value="1" checked>
                            <span>Variação ativa</span>
                        </label>
                    </div>
                    <div class="field">
                        <label for="new_variant_attributes_json">Atributos (JSON)</label>
                        <textarea id="new_variant_attributes_json" class="textarea mono" name="attributes_json" style="min-height:110px;" placeholder='{"tiragem":"500","papel":"Couchê 300g","acabamento":"Laminação fosca"}'></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Adicionar variação</button>
                </form>
            </section>
        </div>
    </section>
@endsection
