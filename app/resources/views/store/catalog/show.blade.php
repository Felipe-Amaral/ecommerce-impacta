@extends('layouts.store')

@php
    $resolveProductImage = static function (?string $rawPath): ?string {
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

    $variantOptions = $product->variants->sortBy('sort_order')->values();
    $firstVariant = $variantOptions->first();
    $firstVariantPrice = (float) ($firstVariant?->promotional_price ?: $firstVariant?->price ?: $product->base_price);
    $initialQuantity = max((int) old('quantity', $product->min_quantity), 1);
    $firstVariantProductionDays = (int) ($firstVariant?->production_days ?: $product->lead_time_days);
    $productCanonical = route('catalog.show', $product->slug);

    $galleryItems = $product->images
        ->sortBy('sort_order')
        ->values()
        ->map(function ($image) use ($resolveProductImage): array {
            return [
                'url' => $resolveProductImage($image->path),
                'alt' => trim((string) ($image->alt_text ?: 'Foto do produto')),
                'sort_order' => (int) ($image->sort_order ?? 0),
            ];
        })
        ->filter(fn (array $image): bool => ! empty($image['url']))
        ->values();

    $productPrimaryImage = $product->images->firstWhere('is_primary', true) ?: $product->images->first();
    $productOgImagePath = $productPrimaryImage?->path;
    $productOgImage = $resolveProductImage($productOgImagePath) ?: asset('favicon.svg?v=uriah2');

    $productMetaTitle = (string) ($product->seo_title ?: ($product->name.' | Uriah Criativa'));
    $productMetaDescription = (string) ($product->seo_description ?: ($product->short_description ?: 'Produto gráfico disponível para compra online.'));

    $quoteUrl = route('pages.contact', [
        'service_interest' => 'orcamento',
        'subject' => 'Orcamento: '.$product->name,
        'message' => 'Quero solicitar um orcamento para '.$product->name.'.',
    ]);

    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => array_values(array_filter([
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Início',
                'item' => route('home'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => 'Catálogo',
                'item' => route('catalog.index'),
            ],
            $product->category ? [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $product->category->name,
                'item' => route('catalog.index', ['categoria' => $product->category->slug]),
            ] : null,
            [
                '@type' => 'ListItem',
                'position' => $product->category ? 4 : 3,
                'name' => $product->name,
                'item' => $productCanonical,
            ],
        ])),
    ];

    $productSchema = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $product->name,
        'description' => strip_tags((string) ($product->seo_description ?: $product->short_description ?: $product->description ?: 'Produto gráfico configurável para produção sob demanda.')),
        'sku' => $product->sku ?: null,
        'category' => $product->category?->name,
        'brand' => [
            '@type' => 'Brand',
            'name' => 'Uriah Criativa',
        ],
        'url' => $productCanonical,
        'image' => [$productOgImage],
        'offers' => [
            '@type' => 'Offer',
            'url' => $productCanonical,
            'priceCurrency' => 'BRL',
            'price' => number_format($firstVariantPrice, 2, '.', ''),
            'availability' => 'https://schema.org/InStock',
            'itemCondition' => 'https://schema.org/NewCondition',
            'seller' => [
                '@type' => 'Organization',
                'name' => 'Uriah Criativa',
            ],
        ],
        'additionalProperty' => collect((array) $product->specifications)->map(function ($value, $name) {
            return [
                '@type' => 'PropertyValue',
                'name' => ucfirst(str_replace('_', ' ', (string) $name)),
                'value' => is_array($value) ? implode(', ', $value) : (string) $value,
            ];
        })->values()->all(),
    ], fn ($value) => $value !== null && $value !== '');
@endphp

@section('title', $productMetaTitle)
@section('meta_description', $productMetaDescription)
@section('canonical_url', $productCanonical)
@section('og_type', 'product')
@section('og_image', $productOgImage)
@section('seo_json_ld')
    <script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
    <script type="application/ld+json">{!! json_encode($productSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
@endsection

@push('head')
<style>
    .product-gallery {
        display: grid;
        gap: 10px;
    }

    .product-gallery-main {
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid rgba(22,20,19,.08);
        background: rgba(255,255,255,.86);
        min-height: 320px;
    }

    .product-gallery-main img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .product-gallery-thumbs {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 8px;
    }

    .product-gallery-thumb {
        border-radius: 10px;
        border: 1px solid rgba(22,20,19,.10);
        overflow: hidden;
        padding: 0;
        background: rgba(255,255,255,.9);
        cursor: pointer;
    }

    .product-gallery-thumb img {
        width: 100%;
        aspect-ratio: 4/3;
        object-fit: cover;
        display: block;
    }

    .product-gallery-thumb.is-active {
        border-color: rgba(198,161,74,.35);
        box-shadow: 0 8px 14px rgba(198,161,74,.16);
    }
</style>
@endpush

@section('content')
    <nav aria-label="Breadcrumb" class="small muted" style="margin: 2px 0 12px;">
        <a href="{{ route('home') }}">Início</a>
        <span aria-hidden="true"> / </span>
        <a href="{{ route('catalog.index') }}">Catálogo</a>
        @if($product->category)
            <span aria-hidden="true"> / </span>
            <a href="{{ route('catalog.index', ['categoria' => $product->category->slug]) }}">{{ $product->category->name }}</a>
        @endif
        <span aria-hidden="true"> / </span>
        <span aria-current="page">{{ $product->name }}</span>
    </nav>

    <section class="hero reveal-up" style="margin-bottom: 18px;">
        <div class="details-grid">
            <div class="stack-xl">
                <div class="stack">
                    <div class="pill-list">
                        @if($product->category)
                            <a class="pill" href="{{ route('catalog.index', ['categoria' => $product->category->slug]) }}">{{ $product->category->name }}</a>
                        @endif
                        <span class="pill">{{ $product->lead_time_days }} dias úteis</span>
                        <span class="pill">Pedido mínimo: {{ $product->min_quantity }}</span>
                    </div>

                    <h1 style="font-size: clamp(1.8rem, 3vw, 2.7rem);">{{ $product->name }}</h1>

                    @if($product->short_description)
                        <p class="lead">{{ $product->short_description }}</p>
                    @endif
                </div>

                <div class="card card-pad" style="overflow:hidden;">
                    @if($galleryItems->isNotEmpty())
                        <div class="product-gallery" data-product-gallery>
                            <div class="product-gallery-main">
                                <img
                                    id="product-gallery-main-image"
                                    src="{{ $galleryItems->first()['url'] }}"
                                    alt="{{ $galleryItems->first()['alt'] }}"
                                    loading="eager"
                                >
                            </div>

                            <div class="product-gallery-thumbs" role="tablist" aria-label="Galeria do produto">
                                @foreach($galleryItems->take(6) as $image)
                                    <button
                                        type="button"
                                        class="product-gallery-thumb {{ $loop->first ? 'is-active' : '' }}"
                                        data-gallery-thumb
                                        data-image-url="{{ $image['url'] }}"
                                        data-image-alt="{{ $image['alt'] }}"
                                        aria-label="Visual {{ $loop->iteration }} do produto"
                                        aria-current="{{ $loop->first ? 'true' : 'false' }}"
                                    >
                                        <img src="{{ $image['url'] }}" alt="{{ $image['alt'] }}" loading="lazy">
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="product-thumb" style="min-height: 260px; position:relative; background:
                            radial-gradient(circle at 10% 16%, rgba(195,58,29,.12), transparent 44%),
                            radial-gradient(circle at 88% 8%, rgba(15,93,245,.12), transparent 46%),
                            linear-gradient(140deg, rgba(255,255,255,.9), rgba(247,240,231,.95));">
                            <div class="stack" style="gap:8px; position:relative; z-index:1;">
                                <span class="product-brow">Pre-visualizacao conceitual</span>
                                @include('store.partials.print-mockup', ['product' => $product, 'size' => 'lg', 'title' => $product->name])
                                <span class="small muted">Imagem ilustrativa da peca. A arte final e conferida antes da producao.</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <aside class="card card-pad stack-lg floating-sticky">
                <div class="stack" style="gap:4px;">
                    <span class="small muted">Preço inicial</span>
                    <div class="price" id="product-selected-price" style="font-size:1.45rem;">
                        R$ {{ number_format($firstVariantPrice, 2, ',', '.') }}
                        <small>a partir de</small>
                    </div>
                    <div class="tiny muted" id="product-selected-variant-line">
                        {{ $firstVariant?->name ?: 'Selecione uma variação' }} • {{ $firstVariantProductionDays }} dias úteis
                    </div>
                </div>

                <form method="POST" action="{{ route('cart.items.store') }}" class="stack" id="product-config-form" enctype="multipart/form-data">
                    @csrf

                    <div class="field">
                        <label for="variant_id">Variação</label>
                        <select id="variant_id" name="variant_id" class="select" required>
                            @foreach ($variantOptions as $variant)
                                <option
                                    value="{{ $variant->id }}"
                                    data-variant-name="{{ $variant->name }}"
                                    data-price="{{ (float) ($variant->promotional_price ?: $variant->price) }}"
                                    data-production-days="{{ (int) ($variant->production_days ?: $product->lead_time_days) }}"
                                >
                                    {{ $variant->name }} - R$ {{ number_format((float) ($variant->promotional_price ?: $variant->price), 2, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label for="quantity">Quantidade</label>
                        <input id="quantity" name="quantity" class="input" type="number" min="{{ $product->min_quantity }}" value="{{ $initialQuantity }}" required />
                    </div>

                    <div class="field">
                        <label for="artwork_file">Upload da arte (opcional)</label>
                        <input
                            id="artwork_file"
                            name="artwork_file"
                            class="input"
                            type="file"
                            accept=".pdf,.ai,.eps,.psd,.cdr,.zip,.jpg,.jpeg,.png"
                        />
                        <span class="tiny muted">Formatos aceitos: PDF, AI, EPS, PSD, CDR, ZIP, JPG e PNG (ate 20MB).</span>
                        @error('artwork_file')
                            <span class="tiny" style="color:#8f221c;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="configuration_briefing">Briefing / observações do material</label>
                        <textarea id="configuration_briefing" name="configuration[briefing]" class="textarea" placeholder="Ex.: frente e verso, verniz localizado, acabamento..." >{{ old('configuration.briefing') }}</textarea>
                    </div>

                    <div class="field">
                        <label for="artwork_notes">Arte final (observações)</label>
                        <textarea id="artwork_notes" name="artwork_notes" class="textarea" placeholder="Ex.: enviar em PDF, aplicar sangria e manter texto em curva">{{ old('artwork_notes') }}</textarea>
                    </div>

                    <button class="btn btn-primary" type="submit">Adicionar ao carrinho</button>
                    <a href="{{ $quoteUrl }}" class="btn btn-secondary">Solicitar orçamento</a>
                    <span class="small muted">Após o pedido, a equipe confirma pagamento e orienta o envio da arte final para conferência.</span>
                </form>

                <div class="glass-panel stack" id="product-live-summary">
                    <div class="link-row">
                        <strong style="font-size:.92rem;">Resumo da configuração</strong>
                        <span class="badge">Prévia</span>
                    </div>
                    <div class="summary-row small">
                        <span class="muted">Variação</span>
                        <strong id="live-variant-name">{{ $firstVariant?->name ?: 'Selecione' }}</strong>
                    </div>
                    <div class="summary-row small">
                        <span class="muted">Prazo estimado</span>
                        <strong id="live-production-days">{{ $firstVariantProductionDays }} dias úteis</strong>
                    </div>
                    <div class="summary-row small">
                        <span class="muted">Quantidade</span>
                        <strong id="live-quantity">{{ $initialQuantity }}</strong>
                    </div>
                    <div class="summary-row total">
                        <span>Total estimado</span>
                        <span id="live-line-total">R$ {{ number_format($firstVariantPrice * $initialQuantity, 2, ',', '.') }}</span>
                    </div>
                    <p class="tiny muted">Estimativa para comparação rápida. O total final considera frete e condições do pedido.</p>
                </div>

                <div class="card card-pad stack" style="box-shadow:none;">
                    <div class="small" style="font-weight:800;">O que acontece depois?</div>
                    <ul class="clean-list small muted">
                        <li>1. Pedido e cobrança</li>
                        <li>2. Conferência da arte final</li>
                        <li>3. Aprovação para produção</li>
                        <li>4. Impressão, acabamento e entrega/retirada</li>
                    </ul>
                </div>
            </aside>
        </div>
    </section>

    <section class="split" style="margin-bottom: 18px;">
        <div class="card card-pad stack-lg">
            <h2>Descrição do produto</h2>
            <p class="muted" style="white-space: pre-line;">{{ $product->description ?: 'Produto configurável para produção gráfica com variações de tiragem, papel e acabamento.' }}</p>
        </div>

        <div class="card card-pad stack-lg">
            <h2>Especificações</h2>
            <ul class="spec-list">
                @forelse ((array) $product->specifications as $label => $value)
                    <li>
                        <span>{{ ucfirst(str_replace('_', ' ', (string) $label)) }}</span>
                        <strong>{{ is_array($value) ? implode(', ', $value) : $value }}</strong>
                    </li>
                @empty
                    <li>
                        <span>Tipo</span>
                        <strong>{{ $product->product_type }}</strong>
                    </li>
                    <li>
                        <span>Prazo base</span>
                        <strong>{{ $product->lead_time_days }} dias úteis</strong>
                    </li>
                    <li>
                        <span>Pedido mínimo</span>
                        <strong>{{ $product->min_quantity }}</strong>
                    </li>
                @endforelse
            </ul>
        </div>
    </section>

    <section class="card card-pad stack-lg" style="margin-bottom: 28px;">
        <div class="section-head">
            <div class="copy">
                <span class="section-kicker">Variações</span>
                <h2>Opções disponíveis para este material</h2>
                <p class="muted">Apresentação em cards para leitura rápida, sem esconder informações em abas.</p>
            </div>
        </div>

        <div class="variant-grid">
            @foreach ($product->variants as $variant)
                <article class="variant-card reveal-up">
                    <div class="row">
                        <strong>{{ $variant->name }}</strong>
                        <span class="variant-price">R$ {{ number_format((float) ($variant->promotional_price ?: $variant->price), 2, ',', '.') }}</span>
                    </div>

                    <div class="tiny muted mono">{{ $variant->sku }}</div>

                    @if(!empty($variant->attributes))
                        <div class="variant-tags">
                            @foreach ($variant->attributes as $attribute => $value)
                                <span class="pill">{{ ucfirst((string) $attribute) }}: {{ $value }}</span>
                            @endforeach
                        </div>
                    @else
                        <span class="small muted">Sem atributos adicionais</span>
                    @endif

                    <div class="row tiny">
                        <span class="muted">Prazo</span>
                        <strong>{{ $variant->production_days ?: $product->lead_time_days }} dias úteis</strong>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    @if($relatedProducts->isNotEmpty())
        <section class="stack-xl" style="margin-bottom: 28px;">
            <h2>Produtos relacionados</h2>
            <div class="grid grid-4">
                @foreach ($relatedProducts as $related)
                    @include('store.partials.product-card', ['product' => $related])
                @endforeach
            </div>
        </section>
    @endif
@endsection

@push('scripts')
<script>
    (function () {
        const galleryRoot = document.querySelector('[data-product-gallery]');
        const mainImage = document.getElementById('product-gallery-main-image');
        const thumbButtons = Array.from(document.querySelectorAll('[data-gallery-thumb]'));

        if (galleryRoot && mainImage && thumbButtons.length) {
            thumbButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const url = button.getAttribute('data-image-url') || '';
                    const alt = button.getAttribute('data-image-alt') || '';
                    if (!url) return;

                    mainImage.src = url;
                    mainImage.alt = alt;

                    thumbButtons.forEach((thumb) => {
                        const isActive = thumb === button;
                        thumb.classList.toggle('is-active', isActive);
                        thumb.setAttribute('aria-current', isActive ? 'true' : 'false');
                    });
                });
            });
        }
    })();

    (function () {
        const variantSelect = document.getElementById('variant_id');
        const quantityInput = document.getElementById('quantity');
        if (!variantSelect || !quantityInput) return;

        const priceEl = document.getElementById('product-selected-price');
        const variantLineEl = document.getElementById('product-selected-variant-line');
        const liveVariantNameEl = document.getElementById('live-variant-name');
        const liveProductionDaysEl = document.getElementById('live-production-days');
        const liveQuantityEl = document.getElementById('live-quantity');
        const liveLineTotalEl = document.getElementById('live-line-total');

        const brl = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });

        const getSelectedMeta = () => {
            const option = variantSelect.options[variantSelect.selectedIndex];
            if (!option) return null;

            return {
                name: option.dataset.variantName || option.textContent || 'Variação',
                price: Number(option.dataset.price || 0),
                productionDays: Number(option.dataset.productionDays || 0),
            };
        };

        const sync = () => {
            const meta = getSelectedMeta();
            if (!meta) return;

            const quantity = Math.max(Number(quantityInput.value || 1), 1);
            const lineTotal = meta.price * quantity;

            if (priceEl) {
                priceEl.innerHTML = brl.format(meta.price) + ' <small>a partir de</small>';
            }

            if (variantLineEl) {
                variantLineEl.textContent = meta.name + ' • ' + meta.productionDays + ' dias úteis';
            }

            if (liveVariantNameEl) liveVariantNameEl.textContent = meta.name;
            if (liveProductionDaysEl) liveProductionDaysEl.textContent = meta.productionDays + ' dias úteis';
            if (liveQuantityEl) liveQuantityEl.textContent = String(quantity);
            if (liveLineTotalEl) liveLineTotalEl.textContent = brl.format(lineTotal);
        };

        variantSelect.addEventListener('change', sync);
        quantityInput.addEventListener('input', sync);
        sync();
    })();
</script>
@endpush
