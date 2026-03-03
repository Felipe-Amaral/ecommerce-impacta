@props([
    'variant' => 'header', // header | footer
])

@php
    $isFooter = $variant === 'footer';
@endphp

<span class="vertice-logo {{ $isFooter ? 'footer' : 'header' }}" aria-hidden="true">
    <span class="vertice-logo-mark">
        <svg viewBox="0 0 96 96" role="img" aria-hidden="true">
            <defs>
                <linearGradient id="uriahGold" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%" stop-color="#efe2a0"></stop>
                    <stop offset="30%" stop-color="#d6ba5c"></stop>
                    <stop offset="62%" stop-color="#be9830"></stop>
                    <stop offset="100%" stop-color="#9f7a1f"></stop>
                </linearGradient>
            </defs>

            <g fill="none" stroke="url(#uriahGold)" stroke-linecap="round" stroke-linejoin="round">
                <path d="M34 36a14 14 0 0 1 28 0" stroke-width="3.8"></path>

                <path d="M48 13v10" stroke-width="3"></path>
                <path d="M37 16l6 9" stroke-width="3"></path>
                <path d="M27 23l9 6" stroke-width="3"></path>
                <path d="M20 36h8" stroke-width="3"></path>
                <path d="M59 25l6-9" stroke-width="3"></path>
                <path d="M60 29l9-6" stroke-width="3"></path>
                <path d="M68 36h8" stroke-width="3"></path>
            </g>

            <g fill="none" stroke="url(#uriahGold)" stroke-linecap="round" stroke-linejoin="round">
                <path d="M27 45v19c0 14 8 21 21 21s21-7 21-21V45" stroke-width="6.4"></path>
                <path d="M23 44h8" stroke-width="4.2"></path>
                <path d="M65 44h8" stroke-width="4.2"></path>
            </g>
        </svg>
    </span>
    <span class="vertice-logo-text">
        <strong>Uriah Criativa</strong>
        <small>{{ $isFooter ? 'Gráfica' : 'Gráfica' }}</small>
    </span>
</span>
