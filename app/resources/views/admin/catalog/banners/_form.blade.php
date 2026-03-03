@php
    $isEdit = $banner->exists;
    $startsAtValue = old('starts_at', optional($banner->starts_at)->format('Y-m-d\TH:i'));
    $endsAtValue = old('ends_at', optional($banner->ends_at)->format('Y-m-d\TH:i'));
    $currentBackgroundImageUrl = old('background_image_url', $banner->background_image_url);
    $textSideValue = old('text_side', data_get($banner->metadata, 'text_side', 'left'));
@endphp

<div class="card card-pad stack-lg">
    <div class="section-head">
        <div class="copy">
            <span class="section-kicker">Banner</span>
            <h2>{{ $isEdit ? 'Editar banner' : 'Novo banner para a home' }}</h2>
            <p class="small muted">Banner rotativo exibido logo abaixo do menu superior da página inicial.</p>
        </div>
    </div>

    <form method="POST" action="{{ $isEdit ? route('admin.catalog.banners.update', $banner) : route('admin.catalog.banners.store') }}" class="stack-lg" enctype="multipart/form-data">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <div class="form-grid">
            <div class="field">
                <label for="banner_name">Nome interno</label>
                <input id="banner_name" class="input" name="name" value="{{ old('name', $banner->name) }}" required>
            </div>
            <div class="field">
                <label for="banner_badge">Badge (opcional)</label>
                <input id="banner_badge" class="input" name="badge" value="{{ old('badge', $banner->badge) }}" placeholder="Ex.: Premium • Papelaria">
            </div>
            <div class="field full">
                <label for="banner_headline">Título principal</label>
                <input id="banner_headline" class="input" name="headline" value="{{ old('headline', $banner->headline) }}" required>
                <div class="tiny muted">Esse texto aparece no topo do banner, com a foto em largura total logo abaixo, adaptando para desktop, tablet e celular.</div>
            </div>
            <div class="field full">
                <label for="banner_subheadline">Subtítulo (opcional)</label>
                <input id="banner_subheadline" class="input" name="subheadline" value="{{ old('subheadline', $banner->subheadline) }}">
            </div>
            <div class="field full">
                <label for="banner_description">Descrição (opcional)</label>
                <textarea id="banner_description" class="textarea" name="description" style="min-height:120px;">{{ old('description', $banner->description) }}</textarea>
            </div>
        </div>

        <div class="form-grid">
            <div class="field">
                <label for="banner_cta_label">CTA principal (texto)</label>
                <input id="banner_cta_label" class="input" name="cta_label" value="{{ old('cta_label', $banner->cta_label) }}" placeholder="Ex.: Explorar catálogo">
            </div>
            <div class="field">
                <label for="banner_cta_url">CTA principal (link)</label>
                <input id="banner_cta_url" class="input" name="cta_url" value="{{ old('cta_url', $banner->cta_url) }}" placeholder="/catalogo">
            </div>
            <div class="field">
                <label for="banner_secondary_cta_label">CTA secundário (texto)</label>
                <input id="banner_secondary_cta_label" class="input" name="secondary_cta_label" value="{{ old('secondary_cta_label', $banner->secondary_cta_label) }}" placeholder="Ex.: Área do cliente">
            </div>
            <div class="field">
                <label for="banner_secondary_cta_url">CTA secundário (link)</label>
                <input id="banner_secondary_cta_url" class="input" name="secondary_cta_url" value="{{ old('secondary_cta_url', $banner->secondary_cta_url) }}" placeholder="/entrar">
            </div>
        </div>

        <div class="form-grid">
            <div class="field">
                <label for="banner_theme">Tema</label>
                <select id="banner_theme" class="select" name="theme">
                    @foreach(['gold' => 'Dourado Premium', 'obsidian' => 'Preto sofisticado', 'ivory' => 'Claro elegante'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('theme', $banner->theme ?: 'gold') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="banner_text_side">Alinhamento do texto</label>
                <select id="banner_text_side" class="select" name="text_side">
                    <option value="left" @selected($textSideValue === 'left')>Esquerda</option>
                    <option value="right" @selected($textSideValue === 'right')>Direita</option>
                </select>
                <div class="tiny muted">No mobile o layout mantém leitura otimizada automaticamente.</div>
            </div>
            <div class="field">
                <label for="banner_sort_order">Ordem</label>
                <input id="banner_sort_order" class="input" type="number" min="0" name="sort_order" value="{{ old('sort_order', $banner->sort_order ?? 0) }}">
            </div>
            <div class="field">
                <label for="banner_starts_at">Início (opcional)</label>
                <input id="banner_starts_at" class="input" type="datetime-local" name="starts_at" value="{{ $startsAtValue }}">
            </div>
            <div class="field">
                <label for="banner_ends_at">Fim (opcional)</label>
                <input id="banner_ends_at" class="input" type="datetime-local" name="ends_at" value="{{ $endsAtValue }}">
            </div>
            <div class="field full">
                <label for="banner_background_image">Foto do banner (upload)</label>
                <input id="banner_background_image" class="input" type="file" name="background_image" accept="image/png,image/jpeg,image/webp,image/avif">
                <div class="tiny muted">Envie uma foto horizontal (JPG/PNG/WebP/AVIF). Recomendado: 1600x700px ou maior.</div>
            </div>
            <div
                class="field full"
                id="banner_background_preview_field"
                style="{{ $currentBackgroundImageUrl ? '' : 'display:none;' }}"
            >
                <label>Preview da foto</label>
                <div class="card" style="padding:10px; display:grid; gap:10px;">
                    <img
                        id="banner_background_preview_image"
                        src="{{ $currentBackgroundImageUrl ?: '' }}"
                        alt="Preview da foto do banner"
                        style="width:100%; max-height:220px; object-fit:cover; border-radius:12px; border:1px solid rgba(0,0,0,.08);"
                    >
                    @if($currentBackgroundImageUrl)
                        <label class="radio-card" for="banner_remove_background_image" style="margin:0;">
                            <input id="banner_remove_background_image" type="checkbox" name="remove_background_image" value="1" @checked(old('remove_background_image'))>
                            <span>Remover foto atual (usar gradiente ou URL abaixo)</span>
                        </label>
                    @endif
                </div>
            </div>
            <div class="field full">
                <label for="banner_background_image_url">Imagem de fundo por URL (opcional, fallback)</label>
                <input id="banner_background_image_url" class="input" name="background_image_url" value="{{ $currentBackgroundImageUrl }}" placeholder="https://... (opcional, se não quiser upload)">
                <div class="tiny muted">Se enviar uma foto acima, o upload tem prioridade. Se não informar nada, o banner usa composição premium com gradiente.</div>
            </div>
        </div>

        <div class="grid grid-2">
            <label class="radio-card" for="banner_is_active">
                <input id="banner_is_active" type="checkbox" name="is_active" value="1" @checked(old('is_active', $banner->is_active ?? true))>
                <span>Banner ativo para exibição</span>
            </label>
        </div>

        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Salvar banner' : 'Cadastrar banner' }}</button>
            <a href="{{ route('admin.catalog.banners.index') }}" class="btn btn-secondary">Voltar</a>
            @if($isEdit)
                <a href="{{ route('home') }}" class="btn btn-secondary" target="_blank" rel="noreferrer">Ver home</a>
            @endif
        </div>
    </form>
</div>

@push('scripts')
    <script>
        (function () {
            const fileInput = document.getElementById('banner_background_image');
            const previewField = document.getElementById('banner_background_preview_field');
            const previewImage = document.getElementById('banner_background_preview_image');
            const removeCheckbox = document.getElementById('banner_remove_background_image');

            if (!fileInput || !previewField || !previewImage) {
                return;
            }

            const initialPreview = previewImage.getAttribute('src') || '';
            let objectUrl = null;

            const revokeObjectUrl = () => {
                if (!objectUrl) {
                    return;
                }

                URL.revokeObjectURL(objectUrl);
                objectUrl = null;
            };

            const showPreview = (src) => {
                previewImage.src = src;
                previewField.style.display = '';
            };

            const hidePreview = () => {
                previewImage.removeAttribute('src');
                previewField.style.display = 'none';
            };

            fileInput.addEventListener('change', () => {
                const [file] = fileInput.files || [];

                revokeObjectUrl();

                if (!file) {
                    if (initialPreview) {
                        showPreview(initialPreview);
                    } else {
                        hidePreview();
                    }

                    return;
                }

                objectUrl = URL.createObjectURL(file);
                showPreview(objectUrl);

                if (removeCheckbox) {
                    removeCheckbox.checked = false;
                }
            });

            window.addEventListener('beforeunload', revokeObjectUrl);
        })();
    </script>
@endpush
