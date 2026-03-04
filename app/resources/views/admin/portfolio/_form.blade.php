@php
    $isEdit = $portfolioProject->exists;
    $publishedAtValue = old('published_at', optional($portfolioProject->published_at)->format('Y-m-d\TH:i'));
    $currentCoverImageUrl = old('cover_image_url', $portfolioProject->cover_image_url);
    $servicesText = old('services_text', implode("\n", $portfolioProject->serviceItems()));
    $toolsText = old('tools_text', implode("\n", $portfolioProject->toolItems()));
    $metricsText = old('metrics_text', collect($portfolioProject->metricItems())
        ->map(fn ($item) => trim($item['label'].' | '.$item['value'].' | '.($item['highlight'] ? 'sim' : '')))
        ->implode("\n"));
    $galleryText = old('gallery_text', collect($portfolioProject->galleryItems())
        ->map(fn ($item) => trim($item['url'].' | '.$item['alt'].' | '.$item['caption']))
        ->implode("\n"));
@endphp

<div class="card card-pad stack-lg">
    <div class="section-head">
        <div class="copy">
            <span class="section-kicker">Portfólio</span>
            <h2>{{ $isEdit ? 'Editar case' : 'Novo case' }}</h2>
            <p class="small muted">Estruture o case com contexto, execução e resultado para gerar confiança e conversão.</p>
        </div>
    </div>

    <form method="POST" action="{{ $isEdit ? route('admin.portfolio.update', $portfolioProject) : route('admin.portfolio.store') }}" class="stack-lg" enctype="multipart/form-data">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <section class="stack">
            <h3 style="margin:0; font-size:1.05rem;">Identificação e publicação</h3>
            <div class="form-grid-3">
                <div class="field">
                    <label for="portfolio_category_id">
                        Categoria
                        @include('partials.help-hint', ['text' => 'Agrupamento temático para facilitar navegação e filtros na vitrine pública.'])
                    </label>
                    <select id="portfolio_category_id" class="select" name="category_id">
                        <option value="">Sem categoria</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected((int) old('category_id', $portfolioProject->category_id) === $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="portfolio_status">
                        Status
                        @include('partials.help-hint', ['text' => 'Rascunho não aparece na vitrine. Agendado publica automaticamente na data definida.'])
                    </label>
                    <select id="portfolio_status" class="select" name="status" required>
                        <option value="draft" @selected(old('status', $portfolioProject->status ?: 'draft') === 'draft')>Rascunho</option>
                        <option value="scheduled" @selected(old('status', $portfolioProject->status) === 'scheduled')>Agendado</option>
                        <option value="published" @selected(old('status', $portfolioProject->status) === 'published')>Publicado</option>
                    </select>
                </div>

                <div class="field">
                    <label for="portfolio_published_at">
                        Data/hora de publicação
                        @include('partials.help-hint', ['text' => 'Obrigatória para agendamento. Se publicado e vazio, usamos a data atual.'])
                    </label>
                    <input id="portfolio_published_at" class="input" type="datetime-local" name="published_at" value="{{ $publishedAtValue }}">
                </div>

                <div class="field full">
                    <label for="portfolio_title">
                        Título do case
                        @include('partials.help-hint', ['text' => 'Nome principal exibido no card e no cabeçalho da página de detalhe.'])
                    </label>
                    <input id="portfolio_title" class="input" name="title" value="{{ old('title', $portfolioProject->title) }}" required>
                </div>

                <div class="field full">
                    <label for="portfolio_slug">
                        Slug
                        @include('partials.help-hint', ['text' => 'Parte final da URL. Se vazio, o sistema gera automaticamente a partir do título.'])
                    </label>
                    <input id="portfolio_slug" class="input mono" name="slug" value="{{ old('slug', $portfolioProject->slug) }}" placeholder="gerado automaticamente se vazio">
                </div>

                <label class="radio-card" for="portfolio_is_featured" style="margin-top: 24px;">
                    <input id="portfolio_is_featured" type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $portfolioProject->is_featured))>
                    <span>
                        Destacar na vitrine principal
                        @include('partials.help-hint', ['text' => 'Cases destacados ganham prioridade na seção principal do portfólio.'])
                    </span>
                </label>
            </div>
        </section>

        <section class="stack">
            <h3 style="margin:0; font-size:1.05rem;">Contexto do projeto</h3>
            <div class="form-grid-3">
                <div class="field">
                    <label for="portfolio_client_name">
                        Cliente
                        @include('partials.help-hint', ['text' => 'Nome da empresa ou marca atendida no case.'])
                    </label>
                    <input id="portfolio_client_name" class="input" name="client_name" value="{{ old('client_name', $portfolioProject->client_name) }}">
                </div>

                <div class="field">
                    <label for="portfolio_industry">
                        Segmento
                        @include('partials.help-hint', ['text' => 'Ex.: gastronomia, educação, varejo, indústria etc.'])
                    </label>
                    <input id="portfolio_industry" class="input" name="industry" value="{{ old('industry', $portfolioProject->industry) }}">
                </div>

                <div class="field">
                    <label for="portfolio_location">
                        Localidade
                        @include('partials.help-hint', ['text' => 'Cidade/região do projeto, útil para prova social geográfica e SEO local.'])
                    </label>
                    <input id="portfolio_location" class="input" name="location" value="{{ old('location', $portfolioProject->location) }}">
                </div>

                <div class="field">
                    <label for="portfolio_project_year">
                        Ano do projeto
                        @include('partials.help-hint', ['text' => 'Ano de referência do case para contexto temporal da vitrine.'])
                    </label>
                    <input id="portfolio_project_year" class="input" type="number" min="1980" max="2100" name="project_year" value="{{ old('project_year', $portfolioProject->project_year) }}">
                </div>

                <div class="field full">
                    <label for="portfolio_project_url">
                        URL do projeto (opcional)
                        @include('partials.help-hint', ['text' => 'Link externo do projeto no ar, quando existir.'])
                    </label>
                    <input id="portfolio_project_url" class="input" type="url" name="project_url" value="{{ old('project_url', $portfolioProject->project_url) }}" placeholder="https://...">
                </div>

                <div class="field full">
                    <label for="portfolio_summary">
                        Resumo executivo
                        @include('partials.help-hint', ['text' => 'Resumo curto para card e abertura do case. Foque no objetivo e impacto.'])
                    </label>
                    <textarea id="portfolio_summary" class="textarea" name="summary" style="min-height: 92px;">{{ old('summary', $portfolioProject->summary) }}</textarea>
                </div>
            </div>
        </section>

        <section class="stack">
            <h3 style="margin:0; font-size:1.05rem;">Narrativa (desafio, solução e resultados)</h3>
            <div class="form-grid">
                <div class="field full">
                    <label for="portfolio_challenge">
                        Desafio
                        @include('partials.help-hint', ['text' => 'Contexto inicial e problema de negócio/comunicação enfrentado pelo cliente.'])
                    </label>
                    <textarea id="portfolio_challenge" class="textarea mono" name="challenge" style="min-height: 140px;">{{ old('challenge', $portfolioProject->challenge) }}</textarea>
                </div>

                <div class="field full">
                    <label for="portfolio_solution">
                        Solução aplicada
                        @include('partials.help-hint', ['text' => 'Estratégia criativa, técnica de impressão, acabamento e execução adotada.'])
                    </label>
                    <textarea id="portfolio_solution" class="textarea mono" name="solution" style="min-height: 140px;">{{ old('solution', $portfolioProject->solution) }}</textarea>
                </div>

                <div class="field full">
                    <label for="portfolio_results">
                        Resultados
                        @include('partials.help-hint', ['text' => 'Impacto final e ganhos observados, com linguagem objetiva e mensurável.'])
                    </label>
                    <textarea id="portfolio_results" class="textarea mono" name="results" style="min-height: 140px;">{{ old('results', $portfolioProject->results) }}</textarea>
                </div>

                <div class="field full">
                    <label for="portfolio_content">
                        Conteúdo adicional (markdown)
                        @include('partials.help-hint', ['text' => 'Bloco extra para bastidores, aprendizados e informações complementares do case.'])
                    </label>
                    <textarea id="portfolio_content" class="textarea mono" name="content" style="min-height: 160px;">{{ old('content', $portfolioProject->content) }}</textarea>
                </div>
            </div>
        </section>

        <section class="stack">
            <h3 style="margin:0; font-size:1.05rem;">Mídia, serviços e métricas</h3>
            <div class="form-grid-3">
                <div class="field full">
                    <label for="portfolio_cover_image">
                        Capa (upload)
                        @include('partials.help-hint', ['text' => 'Imagem de destaque do case. Recomendado 1600x900 ou maior.'])
                    </label>
                    <input id="portfolio_cover_image" class="input" type="file" name="cover_image" accept="image/png,image/jpeg,image/webp,image/avif">
                </div>

                <div class="field full" id="portfolio_cover_preview_field" style="{{ $currentCoverImageUrl ? '' : 'display:none;' }}">
                    <label>
                        Preview da capa
                        @include('partials.help-hint', ['text' => 'Pré-visualização da capa atual ou da nova imagem antes de salvar.'])
                    </label>
                    <div class="card" style="padding:10px; display:grid; gap:10px;">
                        <img
                            id="portfolio_cover_preview_image"
                            src="{{ $currentCoverImageUrl ?: '' }}"
                            alt="Preview da capa"
                            style="width:100%; max-height:220px; object-fit:cover; border-radius:12px; border:1px solid rgba(0,0,0,.08);"
                        >
                        @if($currentCoverImageUrl)
                            <label class="radio-card" for="portfolio_remove_cover" style="margin:0;">
                                <input id="portfolio_remove_cover" type="checkbox" name="remove_cover_image" value="1" @checked(old('remove_cover_image'))>
                                <span>Remover capa atual</span>
                            </label>
                        @endif
                    </div>
                </div>

                <div class="field full">
                    <label for="portfolio_cover_image_url">
                        Capa por URL (fallback)
                        @include('partials.help-hint', ['text' => 'Use quando a capa estiver hospedada externamente.'])
                    </label>
                    <input id="portfolio_cover_image_url" class="input" name="cover_image_url" value="{{ $currentCoverImageUrl }}" placeholder="https://...">
                </div>

                <div class="field">
                    <label for="portfolio_services_text">
                        Serviços (1 por linha)
                        @include('partials.help-hint', ['text' => 'Ex.: Branding impresso, catálogo institucional, acabamento premium.'])
                    </label>
                    <textarea id="portfolio_services_text" class="textarea" name="services_text" style="min-height: 120px;">{{ $servicesText }}</textarea>
                </div>

                <div class="field">
                    <label for="portfolio_tools_text">
                        Ferramentas/processos (1 por linha)
                        @include('partials.help-hint', ['text' => 'Ex.: offset, digital, corte especial, verniz localizado, dobra etc.'])
                    </label>
                    <textarea id="portfolio_tools_text" class="textarea" name="tools_text" style="min-height: 120px;">{{ $toolsText }}</textarea>
                </div>

                <div class="field">
                    <label for="portfolio_metrics_text">
                        Métricas (linha = rótulo | valor | destaque)
                        @include('partials.help-hint', ['text' => 'Ex.: Taxa de resposta | +38% | sim. Use destaque "sim" para métrica principal.'])
                    </label>
                    <textarea id="portfolio_metrics_text" class="textarea mono" name="metrics_text" style="min-height: 120px;">{{ $metricsText }}</textarea>
                </div>

                <div class="field full">
                    <label for="portfolio_gallery_text">
                        Galeria (linha = URL | ALT | legenda)
                        @include('partials.help-hint', ['text' => 'Ex.: https://.../foto.webp | Folder aberto sobre mesa | Versão final aprovada.'])
                    </label>
                    <textarea id="portfolio_gallery_text" class="textarea mono" name="gallery_text" style="min-height: 140px;">{{ $galleryText }}</textarea>
                </div>
            </div>
        </section>

        <section class="stack">
            <h3 style="margin:0; font-size:1.05rem;">SEO avançado</h3>
            <div class="form-grid-3">
                <div class="field">
                    <label for="portfolio_focus_keyword">
                        Palavra-chave foco
                        @include('partials.help-hint', ['text' => 'Termo principal deste case para estratégia de SEO on-page.'])
                    </label>
                    <input id="portfolio_focus_keyword" class="input" name="focus_keyword" value="{{ old('focus_keyword', $portfolioProject->focus_keyword) }}">
                </div>

                <div class="field">
                    <label for="portfolio_seo_title">
                        Meta title
                        @include('partials.help-hint', ['text' => 'Título para buscadores e compartilhamentos.'])
                    </label>
                    <input id="portfolio_seo_title" class="input" name="seo_title" value="{{ old('seo_title', $portfolioProject->seo_title) }}">
                </div>

                <div class="field">
                    <label for="portfolio_seo_description">
                        Meta description
                        @include('partials.help-hint', ['text' => 'Resumo otimizado para snippet de busca.'])
                    </label>
                    <input id="portfolio_seo_description" class="input" name="seo_description" value="{{ old('seo_description', $portfolioProject->seo_description) }}">
                </div>

                <div class="field">
                    <label for="portfolio_seo_canonical_url">
                        Canonical URL
                        @include('partials.help-hint', ['text' => 'Define a versão principal da página em cenários de duplicidade de conteúdo.'])
                    </label>
                    <input id="portfolio_seo_canonical_url" class="input" name="seo_canonical_url" value="{{ old('seo_canonical_url', $portfolioProject->seo_canonical_url) }}" placeholder="https://...">
                </div>

                <div class="field">
                    <label for="portfolio_seo_og_title">
                        OG title
                        @include('partials.help-hint', ['text' => 'Título customizado para compartilhamento em redes sociais.'])
                    </label>
                    <input id="portfolio_seo_og_title" class="input" name="seo_og_title" value="{{ old('seo_og_title', $portfolioProject->seo_og_title) }}">
                </div>

                <div class="field">
                    <label for="portfolio_seo_og_description">
                        OG description
                        @include('partials.help-hint', ['text' => 'Descrição customizada para redes sociais.'])
                    </label>
                    <input id="portfolio_seo_og_description" class="input" name="seo_og_description" value="{{ old('seo_og_description', $portfolioProject->seo_og_description) }}">
                </div>

                <div class="field full">
                    <label for="portfolio_seo_og_image_url">
                        OG image URL
                        @include('partials.help-hint', ['text' => 'Imagem usada em compartilhamentos sociais. Se vazio, usamos a capa do case.'])
                    </label>
                    <input id="portfolio_seo_og_image_url" class="input" name="seo_og_image_url" value="{{ old('seo_og_image_url', $portfolioProject->seo_og_image_url) }}">
                </div>

                <label class="radio-card" for="portfolio_seo_noindex" style="margin-top: 24px;">
                    <input id="portfolio_seo_noindex" type="checkbox" name="seo_noindex" value="1" @checked(old('seo_noindex', $portfolioProject->seo_noindex))>
                    <span>Marcar como noindex (não indexar)</span>
                </label>
            </div>
        </section>

        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Salvar case' : 'Criar case' }}</button>
            <a href="{{ route('admin.portfolio.index') }}" class="btn btn-secondary">Voltar</a>
            @if($isEdit)
                <a href="{{ route('portfolio.show', $portfolioProject->slug) }}" class="btn btn-secondary" target="_blank" rel="noreferrer">Ver case</a>
            @endif
        </div>
    </form>
</div>

@push('scripts')
    <script>
        (function () {
            const titleInput = document.getElementById('portfolio_title');
            const slugInput = document.getElementById('portfolio_slug');
            const fileInput = document.getElementById('portfolio_cover_image');
            const previewField = document.getElementById('portfolio_cover_preview_field');
            const previewImage = document.getElementById('portfolio_cover_preview_image');
            const removeCheckbox = document.getElementById('portfolio_remove_cover');

            if (titleInput && slugInput) {
                let slugTouched = slugInput.value.trim() !== '';
                slugInput.addEventListener('input', () => {
                    slugTouched = slugInput.value.trim() !== '';
                });

                titleInput.addEventListener('input', () => {
                    if (slugTouched) return;
                    const slug = titleInput.value
                        .toLowerCase()
                        .normalize('NFD')
                        .replace(/[\u0300-\u036f]/g, '')
                        .replace(/[^a-z0-9]+/g, '-')
                        .replace(/^-+|-+$/g, '')
                        .replace(/-{2,}/g, '-');
                    slugInput.value = slug;
                });
            }

            if (!fileInput || !previewField || !previewImage) {
                return;
            }

            const initialPreview = previewImage.getAttribute('src') || '';
            let objectUrl = null;

            const revokeObjectUrl = () => {
                if (!objectUrl) return;
                URL.revokeObjectURL(objectUrl);
                objectUrl = null;
            };

            fileInput.addEventListener('change', () => {
                const [file] = fileInput.files || [];
                revokeObjectUrl();

                if (!file) {
                    if (initialPreview) {
                        previewImage.src = initialPreview;
                        previewField.style.display = '';
                    } else {
                        previewImage.removeAttribute('src');
                        previewField.style.display = 'none';
                    }
                    return;
                }

                objectUrl = URL.createObjectURL(file);
                previewImage.src = objectUrl;
                previewField.style.display = '';

                if (removeCheckbox) {
                    removeCheckbox.checked = false;
                }
            });

            window.addEventListener('beforeunload', revokeObjectUrl);
        })();
    </script>
@endpush
