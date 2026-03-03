@php
    $variant = $product->variants->sortBy('sort_order')->first();
    $price = $variant?->promotional_price ?: $variant?->price ?: $product->base_price;
    $accent = match($product->category?->slug) {
        'cartoes-e-papelaria' => '#c33a1d',
        'promocionais' => '#0f5df5',
        'comunicacao-visual' => '#0f766e',
        'rotulos-e-etiquetas' => '#d97706',
        default => '#475569',
    };
@endphp

<a href="{{ route('catalog.show', $product->slug) }}" class="card product-card reveal-up" aria-label="Ver {{ $product->name }}" style="--accent: {{ $accent }};">
    <div class="product-media-frame">
        <div class="product-thumb" style="background:
            radial-gradient(circle at 12% 18%, color-mix(in srgb, {{ $accent }} 14%, white 86%), transparent 42%),
            radial-gradient(circle at 88% 14%, rgba(200,164,78,.12), transparent 44%),
            linear-gradient(145deg, rgba(255,255,255,.90), rgba(246,241,232,.96));">
            <div class="stack" style="gap:8px; position:relative; z-index:1; width:100%;">
                <div style="display:flex; justify-content:space-between; gap:8px; align-items:flex-start;">
                    <span class="product-brow">{{ $product->category?->name ?: 'Grafica' }}</span>
                    @if($product->is_featured)
                        <span class="badge badge-brand" style="font-size:.65rem; padding:5px 8px;">Top</span>
                    @endif
                </div>
                @include('store.partials.print-mockup', ['product' => $product, 'size' => 'sm', 'title' => $product->name])
            </div>
        </div>
    </div>

    <div class="stack">
        <div class="pill-list">
            @if($product->is_featured)
                <span class="badge badge-brand">Destaque</span>
            @endif
            <span class="pill">{{ $product->lead_time_days }} dias úteis</span>
        </div>

        <div class="stack" style="gap:6px;">
            <h3>{{ $product->name }}</h3>
            @if($product->short_description)
                <p class="small muted">{{ \Illuminate\Support\Str::limit($product->short_description, 95) }}</p>
            @endif
        </div>

        <div class="product-meta">
            <div class="price">
                R$ {{ number_format((float) $price, 2, ',', '.') }}
                <small>a partir de</small>
            </div>
            <span class="product-card-cta">
                <span class="bullet">→</span>
                Ver detalhes
            </span>
        </div>
    </div>
</a>
