@php
    $categorySlug = $categorySlug ?? ($product->category->slug ?? '');
    $size = $size ?? 'sm';

    $scene = match ($categorySlug) {
        'cartoes-e-papelaria' => 'card',
        'comunicacao-visual' => 'banner',
        'rotulos-e-etiquetas' => 'labels',
        'promocionais' => 'flyer',
        'brindes-personalizados' => 'card',
        'produtos-corporativos' => 'card',
        'outros-produtos' => 'banner',
        default => 'card',
    };

    $isLarge = $size === 'lg';
@endphp

<div class="print-scene {{ $scene }} {{ $isLarge ? 'large' : '' }}" aria-hidden="true">
    <div class="board"></div>
    <div class="shadow-ellipse"></div>

    @if ($scene === 'card')
        <div class="scene-object back">
            <div class="scene-label">verso</div>
            <div class="scene-text">
                <span></span>
                <span style="width:72%;"></span>
            </div>
        </div>
        <div class="scene-object main">
            <div class="scene-label">frente</div>
            <div class="scene-text">
                <strong>{{ \Illuminate\Support\Str::limit($title ?? ($product->name ?? 'Cartao'), 22) }}</strong>
                <span></span>
                <span style="width:58%;"></span>
            </div>
        </div>
        <div class="scene-accent"></div>
    @elseif ($scene === 'flyer')
        <div class="scene-object mid">
            <div class="scene-label">campanha</div>
            <div class="scene-text">
                <strong>Oferta visual</strong>
                <span></span>
                <span style="width:66%;"></span>
            </div>
        </div>
        <div class="scene-object main">
            <div class="scene-label">flyer</div>
            <div class="scene-text">
                <strong>{{ \Illuminate\Support\Str::limit($title ?? ($product->name ?? 'Flyer'), 20) }}</strong>
                <span></span>
                <span style="width:52%;"></span>
            </div>
        </div>
        <div class="scene-accent"></div>
    @elseif ($scene === 'banner')
        <div class="scene-object pole"></div>
        <div class="scene-object main">
            <div class="scene-label">banner</div>
            <div class="scene-text" style="bottom:12px;">
                <strong style="color:#f8fafc;">{{ \Illuminate\Support\Str::limit($title ?? ($product->name ?? 'Banner'), 16) }}</strong>
                <span style="background: rgba(248,250,252,.22);"></span>
                <span style="width:60%; background: rgba(248,250,252,.18);"></span>
            </div>
        </div>
        <div class="scene-object base"></div>
        <div class="scene-accent ring"></div>
    @elseif ($scene === 'labels')
        <div class="scene-object sheet">
            <div class="scene-label">etiquetas</div>
            <div class="grid-dots">
                <span></span><span></span><span></span>
                <span></span><span></span><span></span>
            </div>
        </div>
        <div class="scene-object roll">
            <div class="scene-label" style="top:50%; transform:translateY(-50%);">vinil</div>
        </div>
    @endif
</div>
