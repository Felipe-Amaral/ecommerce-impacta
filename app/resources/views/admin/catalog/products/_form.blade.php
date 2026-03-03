@php
    $isEdit = $product->exists;
@endphp

<div class="card card-pad stack-lg">
    <div class="section-head">
        <div class="copy">
            <span class="section-kicker">Produto</span>
            <h2>{{ $isEdit ? 'Editar produto' : 'Novo produto' }}</h2>
        </div>
    </div>

    <form method="POST" action="{{ $isEdit ? route('admin.catalog.products.update', $product) : route('admin.catalog.products.store') }}" class="stack-lg">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <div class="form-grid">
            <div class="field">
                <label for="product_category_id">Categoria</label>
                <select id="product_category_id" class="select" name="category_id">
                    <option value="">Sem categoria</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((int) old('category_id', $product->category_id) === $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="product_type">Tipo</label>
                <input id="product_type" class="input" name="product_type" value="{{ old('product_type', $product->product_type ?: 'print') }}">
            </div>
            <div class="field full">
                <label for="product_name">Nome</label>
                <input id="product_name" class="input" name="name" value="{{ old('name', $product->name) }}" required>
            </div>
            <div class="field">
                <label for="product_slug">Slug</label>
                <input id="product_slug" class="input" name="slug" value="{{ old('slug', $product->slug) }}" placeholder="gerado automaticamente se vazio">
            </div>
            <div class="field">
                <label for="product_sku">SKU</label>
                <input id="product_sku" class="input" name="sku" value="{{ old('sku', $product->sku) }}" required>
            </div>
            <div class="field">
                <label for="product_base_price">Preço base</label>
                <input id="product_base_price" class="input" type="number" step="0.01" min="0" name="base_price" value="{{ old('base_price', $product->base_price) }}">
            </div>
            <div class="field">
                <label for="product_min_quantity">Quantidade mínima</label>
                <input id="product_min_quantity" class="input" type="number" min="1" name="min_quantity" value="{{ old('min_quantity', $product->min_quantity) }}">
            </div>
            <div class="field">
                <label for="product_lead_time_days">Prazo base (dias)</label>
                <input id="product_lead_time_days" class="input" type="number" min="0" name="lead_time_days" value="{{ old('lead_time_days', $product->lead_time_days) }}">
            </div>
            <div class="field full">
                <label for="product_short_description">Descrição curta</label>
                <textarea id="product_short_description" class="textarea" name="short_description">{{ old('short_description', $product->short_description) }}</textarea>
            </div>
            <div class="field full">
                <label for="product_description">Descrição completa</label>
                <textarea id="product_description" class="textarea" name="description" style="min-height:140px;">{{ old('description', $product->description) }}</textarea>
            </div>
            <div class="field full">
                <label for="product_specifications_json">Especificações (JSON)</label>
                <textarea id="product_specifications_json" class="textarea mono" name="specifications_json" style="min-height:180px;" placeholder='{"formato":"9x5 cm","impressao":"4x4","acabamentos":["Laminação fosca","Verniz localizado"]}'>{{ old('specifications_json', $specificationsText) }}</textarea>
                <div class="tiny muted">Use JSON válido para especificações exibidas na página do produto.</div>
            </div>
            <div class="field">
                <label for="product_seo_title">SEO title</label>
                <input id="product_seo_title" class="input" name="seo_title" value="{{ old('seo_title', $product->seo_title) }}">
            </div>
            <div class="field">
                <label for="product_seo_description">SEO description</label>
                <input id="product_seo_description" class="input" name="seo_description" value="{{ old('seo_description', $product->seo_description) }}">
            </div>
        </div>

        <div class="grid grid-3">
            <label class="radio-card" for="product_is_customizable">
                <input id="product_is_customizable" type="checkbox" name="is_customizable" value="1" @checked(old('is_customizable', $product->is_customizable))>
                <span>Customizável</span>
            </label>
            <label class="radio-card" for="product_is_active">
                <input id="product_is_active" type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active))>
                <span>Ativo na loja</span>
            </label>
            <label class="radio-card" for="product_is_featured">
                <input id="product_is_featured" type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured))>
                <span>Em destaque</span>
            </label>
        </div>

        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Salvar produto' : 'Criar produto' }}</button>
            <a href="{{ route('admin.catalog.products.index') }}" class="btn btn-secondary">Voltar</a>
        </div>
    </form>
</div>
