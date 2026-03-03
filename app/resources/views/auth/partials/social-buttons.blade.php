@php
    $providers = collect((array) config('storefront.auth.social.providers', []))
        ->filter(fn ($value) => is_string($value) && in_array($value, ['google', 'facebook', 'github'], true))
        ->values();

    $providerMeta = [
        'google' => ['label' => 'Google', 'icon' => 'G', 'class' => 'google'],
        'facebook' => ['label' => 'Facebook', 'icon' => 'f', 'class' => 'facebook'],
        'github' => ['label' => 'GitHub', 'icon' => '{ }', 'class' => 'github'],
    ];
@endphp

@if($providers->isNotEmpty())
    <div class="stack" style="gap:10px;">
        <div class="small muted"><strong>Entrar ou criar conta em 1 clique</strong> (quando a rede social estiver conectada no navegador)</div>
        <div class="social-auth-grid">
            @foreach($providers as $provider)
                @php($meta = $providerMeta[$provider])
                <a href="{{ route('auth.social.redirect', ['provider' => $provider]) }}" class="btn btn-secondary social-btn {{ $meta['class'] }}">
                    <span class="social-icon" aria-hidden="true">{{ $meta['icon'] }}</span>
                    Continuar com {{ $meta['label'] }}
                </a>
            @endforeach
        </div>
        <div class="oauth-divider"><span>ou use e-mail e senha</span></div>
    </div>
@endif
