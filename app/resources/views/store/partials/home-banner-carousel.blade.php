@php
    $banners = $heroBanners ?? collect();
@endphp

@if($banners->isNotEmpty())
    <section class="home-banner-rotator reveal-up" data-home-banner-rotator data-autoplay-ms="6500" aria-label="Destaques da home">
        <div class="home-banner-shell">
            <div class="home-banner-stage">
                @foreach($banners as $banner)
                    @php
                        $theme = in_array($banner->theme, ['gold', 'obsidian', 'ivory'], true) ? $banner->theme : 'gold';
                    @endphp
                    <article
                        class="home-banner-slide theme-{{ $theme }}{{ $loop->first ? ' is-active' : '' }}"
                        data-banner-slide
                        data-index="{{ $loop->index }}"
                        aria-hidden="{{ $loop->first ? 'false' : 'true' }}"
                        @if(!empty($banner->background_image_url))
                            style="--banner-image: url('{{ $banner->background_image_url }}');"
                        @endif
                    >
                        <div class="home-banner-overlay" aria-hidden="true"></div>

                        <div class="home-banner-content">
                            <div class="home-banner-copy">
                                <div class="pill-list">
                                    @if($banner->badge)
                                        <span class="home-banner-badge">{{ $banner->badge }}</span>
                                    @endif
                                    <span class="home-banner-chip">{{ $banner->is_active ? 'Ativo' : 'Inativo' }}</span>
                                </div>

                                <div class="stack" style="gap:8px;">
                                    <h2 class="home-banner-title">{{ $banner->headline }}</h2>
                                    @if($banner->subheadline)
                                        <p class="home-banner-subtitle">{{ $banner->subheadline }}</p>
                                    @endif
                                    @if($banner->description)
                                        <p class="home-banner-description">{{ $banner->description }}</p>
                                    @endif
                                </div>

                                @if($banner->cta_label || $banner->secondary_cta_label)
                                    <div class="home-banner-actions">
                                        @if($banner->cta_label && $banner->cta_url)
                                            <a href="{{ $banner->cta_url }}" class="btn btn-primary">{{ $banner->cta_label }}</a>
                                        @endif
                                        @if($banner->secondary_cta_label && $banner->secondary_cta_url)
                                            <a href="{{ $banner->secondary_cta_url }}" class="btn btn-secondary">{{ $banner->secondary_cta_label }}</a>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="home-banner-visual" aria-hidden="true">
                                <div class="home-banner-visual-frame">
                                    <div class="home-banner-visual-line top"></div>
                                    <div class="home-banner-visual-line bottom"></div>
                                    <div class="home-banner-visual-grid">
                                        <div class="tile large">
                                            <span>Premium</span>
                                            <strong>Acabamento</strong>
                                        </div>
                                        <div class="tile">
                                            <span>Fluxo</span>
                                            <strong>Compra online</strong>
                                        </div>
                                        <div class="tile">
                                            <span>Atendimento</span>
                                            <strong>Pré-impressão</strong>
                                        </div>
                                        <div class="tile large accent">
                                            <span>Entrega</span>
                                            <strong>Produção + expedição</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            @if($banners->count() > 1)
                <div class="home-banner-controls" aria-label="Controles do banner">
                    <button type="button" class="home-banner-arrow" data-banner-prev aria-label="Banner anterior">‹</button>
                    <div class="home-banner-dots" role="tablist" aria-label="Selecionar banner">
                        @foreach($banners as $banner)
                            <button
                                type="button"
                                class="home-banner-dot{{ $loop->first ? ' is-active' : '' }}"
                                data-banner-dot
                                data-index="{{ $loop->index }}"
                                role="tab"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                                aria-label="Ir para banner {{ $loop->iteration }}"
                            ></button>
                        @endforeach
                    </div>
                    <button type="button" class="home-banner-arrow" data-banner-next aria-label="Próximo banner">›</button>
                </div>
            @endif
        </div>
    </section>
@endif
