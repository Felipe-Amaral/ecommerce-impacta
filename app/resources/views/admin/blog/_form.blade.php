@php
    $isEdit = $blogPost->exists;
    $publishedAtValue = old('published_at', optional($blogPost->published_at)->format('Y-m-d\TH:i'));
    $currentCoverImageUrl = old('cover_image_url', $blogPost->cover_image_url);
    $selectedTagValues = collect(old('tag_ids', $selectedTagIds ?? []))
        ->map(fn ($id) => (int) $id)
        ->filter(fn (int $id) => $id > 0)
        ->values()
        ->all();
@endphp

@once
    @push('head')
        <link rel="stylesheet" href="https://uicdn.toast.com/editor/latest/toastui-editor.min.css">
        <style>
            .blog-editor-shell {
                border-radius: 14px;
                border: 1px solid rgba(22,20,19,.12);
                overflow: hidden;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.74);
                background: rgba(255,255,255,.92);
            }

            .blog-editor-shell .toastui-editor-defaultUI {
                border: 0;
                font-family: "Segoe UI", "Helvetica Neue", Helvetica, Arial, sans-serif;
            }

            .blog-editor-shell .toastui-editor-toolbar {
                border-bottom: 1px solid rgba(22,20,19,.1);
                background: linear-gradient(180deg, rgba(255,255,255,.98), rgba(249,245,238,.94));
            }

            .blog-editor-shell .toastui-editor-mode-switch {
                border-top: 1px solid rgba(22,20,19,.1);
                background: linear-gradient(180deg, rgba(255,255,255,.98), rgba(249,245,238,.94));
            }

            .blog-editor-source-hidden {
                display: none;
            }
        </style>
    @endpush
@endonce

<div class="card card-pad stack-lg">
    <div class="section-head">
        <div class="copy">
            <span class="section-kicker">Blog</span>
            <h2>{{ $isEdit ? 'Editar artigo' : 'Novo artigo' }}</h2>
            <p class="small muted">Editor robusto com visualização rica, além de controle completo de publicação e SEO.</p>
        </div>
    </div>

    <form method="POST" action="{{ $isEdit ? route('admin.blog.update', $blogPost) : route('admin.blog.store') }}" class="stack-lg" enctype="multipart/form-data">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <section class="stack">
            <h3 style="margin:0; font-size:1.05rem;">Conteúdo</h3>
            <div class="form-grid-3">
                <div class="field">
                    <label for="blog_post_category">
                        Categoria
                        @include('partials.help-hint', ['text' => 'Define o agrupamento principal do artigo no blog e melhora a navegação por tema.'])
                    </label>
                    <select id="blog_post_category" class="select" name="category_id">
                        <option value="">Sem categoria</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected((int) old('category_id', $blogPost->category_id) === $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="blog_post_status">
                        Status
                        @include('partials.help-hint', ['text' => 'Rascunho não aparece para visitantes. Agendado publica no horário definido. Publicado fica visível no blog.'])
                    </label>
                    <select id="blog_post_status" class="select" name="status" required>
                        <option value="draft" @selected(old('status', $blogPost->status ?: 'draft') === 'draft')>Rascunho</option>
                        <option value="scheduled" @selected(old('status', $blogPost->status) === 'scheduled')>Agendado</option>
                        <option value="published" @selected(old('status', $blogPost->status) === 'published')>Publicado</option>
                    </select>
                </div>

                <div class="field">
                    <label for="blog_post_published_at">
                        Data/hora de publicação
                        @include('partials.help-hint', ['text' => 'Para status agendado, informe data futura. Para publicado, deixe vazio para usar agora.'])
                    </label>
                    <input id="blog_post_published_at" class="input" type="datetime-local" name="published_at" value="{{ $publishedAtValue }}">
                </div>

                <div class="field full">
                    <label for="blog_post_title">
                        Título
                        @include('partials.help-hint', ['text' => 'Título principal do artigo. Também é base para SEO e para geração automática do slug.'])
                    </label>
                    <input id="blog_post_title" class="input" name="title" value="{{ old('title', $blogPost->title) }}" required>
                </div>

                <div class="field full">
                    <label for="blog_post_slug">
                        Slug
                        @include('partials.help-hint', ['text' => 'Parte final da URL do artigo. Use termos curtos e descritivos, separados por hífen.'])
                    </label>
                    <input id="blog_post_slug" class="input mono" name="slug" value="{{ old('slug', $blogPost->slug) }}" placeholder="gerado automaticamente se vazio">
                </div>

                <div class="field full">
                    <label for="blog_post_excerpt">
                        Resumo (vitrine)
                        @include('partials.help-hint', ['text' => 'Texto curto para cards do blog. Se não houver meta description, ele pode ser usado como fallback de SEO.'])
                    </label>
                    <textarea id="blog_post_excerpt" class="textarea" name="excerpt" style="min-height:90px;">{{ old('excerpt', $blogPost->excerpt) }}</textarea>
                    <div class="tiny muted">Aparece nos cards do blog e também como fallback de meta description.</div>
                </div>

                <div class="field full">
                    <label for="blog_post_content">
                        Conteúdo do artigo
                        @include('partials.help-hint', ['text' => 'Editor robusto com modo visual e markdown. O conteúdo salvo é markdown para manter performance e consistência.'])
                    </label>
                    <div id="blog_post_editor_shell" class="blog-editor-shell" hidden>
                        <div id="blog_post_editor"></div>
                    </div>
                    <textarea id="blog_post_content" class="textarea mono" name="content" style="min-height:280px;" required>{{ old('content', $blogPost->content) }}</textarea>
                    <div class="tiny muted">Se o editor visual não carregar, este campo funciona como fallback manual em Markdown.</div>
                </div>
            </div>
        </section>

        <section class="stack">
            <h3 style="margin:0; font-size:1.05rem;">Mídia e tags</h3>
            <div class="form-grid-3">
                <div class="field full">
                    <label for="blog_post_cover_image">
                        Capa (upload)
                        @include('partials.help-hint', ['text' => 'Imagem principal do artigo para vitrine e compartilhamento. Prefira proporção horizontal, ex.: 1600x900.'])
                    </label>
                    <input id="blog_post_cover_image" class="input" type="file" name="cover_image" accept="image/png,image/jpeg,image/webp,image/avif">
                    <div class="tiny muted">Recomendado: 1600x900 ou maior.</div>
                </div>

                <div class="field full" id="blog_cover_preview_field" style="{{ $currentCoverImageUrl ? '' : 'display:none;' }}">
                    <label>
                        Preview da capa
                        @include('partials.help-hint', ['text' => 'Pré-visualização da imagem atual ou da nova imagem enviada antes de salvar.'])
                    </label>
                    <div class="card" style="padding:10px; display:grid; gap:10px;">
                        <img
                            id="blog_cover_preview_image"
                            src="{{ $currentCoverImageUrl ?: '' }}"
                            alt="Preview da capa"
                            style="width:100%; max-height:220px; object-fit:cover; border-radius:12px; border:1px solid rgba(0,0,0,.08);"
                        >
                        @if($currentCoverImageUrl)
                            <label class="radio-card" for="blog_post_remove_cover" style="margin:0;">
                                <input id="blog_post_remove_cover" type="checkbox" name="remove_cover_image" value="1" @checked(old('remove_cover_image'))>
                                <span>
                                    Remover capa atual
                                    @include('partials.help-hint', ['text' => 'Se marcado, remove a imagem atual no salvamento.'])
                                </span>
                            </label>
                        @endif
                    </div>
                </div>

                <div class="field full">
                    <label for="blog_post_cover_image_url">
                        Capa por URL (fallback)
                        @include('partials.help-hint', ['text' => 'Use este campo se a capa estiver hospedada externamente. O upload local tem prioridade quando enviado.'])
                    </label>
                    <input id="blog_post_cover_image_url" class="input" name="cover_image_url" value="{{ $currentCoverImageUrl }}" placeholder="https://...">
                </div>

                <div class="field">
                    <label for="blog_post_reading_time_minutes">
                        Tempo de leitura (min)
                        @include('partials.help-hint', ['text' => 'Tempo exibido ao visitante. Se vazio, o sistema calcula automaticamente com base no conteúdo.'])
                    </label>
                    <input id="blog_post_reading_time_minutes" class="input" type="number" min="1" max="120" name="reading_time_minutes" value="{{ old('reading_time_minutes', $blogPost->reading_time_minutes) }}">
                    <div class="tiny muted">Se vazio, calculamos automaticamente.</div>
                </div>

                <label class="radio-card" for="blog_post_is_featured" style="margin-top:24px;">
                    <input id="blog_post_is_featured" type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $blogPost->is_featured))>
                    <span>
                        Destaque da vitrine principal
                        @include('partials.help-hint', ['text' => 'Artigos em destaque podem aparecer no bloco principal da home do blog.'])
                    </span>
                </label>

                <div class="field full">
                    <label>
                        Tags existentes
                        @include('partials.help-hint', ['text' => 'Tags conectam artigos por assunto transversal. Ex.: branding, flyer, acabamento.'])
                    </label>
                    <div class="grid grid-3" style="gap:8px;">
                        @forelse($tags as $tag)
                            <label class="radio-card" for="blog_post_tag_{{ $tag->id }}">
                                <input
                                    id="blog_post_tag_{{ $tag->id }}"
                                    type="checkbox"
                                    name="tag_ids[]"
                                    value="{{ $tag->id }}"
                                    @checked(in_array($tag->id, $selectedTagValues, true))
                                >
                                <span>#{{ $tag->name }}</span>
                            </label>
                        @empty
                            <div class="tiny muted">Nenhuma tag cadastrada ainda.</div>
                        @endforelse
                    </div>
                </div>

                <div class="field full">
                    <label for="blog_post_new_tags">
                        Criar tags novas (separadas por vírgula)
                        @include('partials.help-hint', ['text' => 'Digite tags não cadastradas e o sistema cria automaticamente no salvamento.'])
                    </label>
                    <input id="blog_post_new_tags" class="input" name="new_tags" value="{{ old('new_tags') }}" placeholder="Ex.: impressão digital, papelaria corporativa">
                </div>
            </div>
        </section>

        <section class="stack">
            <div class="link-row" style="align-items:flex-start;">
                <div class="stack" style="gap:4px;">
                    <h3 style="margin:0; font-size:1.05rem;">SEO avançado</h3>
                    <p class="small muted">Título, descrição, keyword foco, canonical e Open Graph.</p>
                </div>
                <div class="card" style="padding:10px 12px; min-width: 180px;">
                    <div class="tiny muted">Health score SEO</div>
                    <strong id="seo-score-value" style="font-size:1.15rem;">0/100</strong>
                </div>
            </div>

            <div class="form-grid-3">
                <div class="field">
                    <label for="blog_post_focus_keyword">
                        Keyword foco
                        @include('partials.help-hint', ['text' => 'Termo principal para ranqueamento. Ajuda o checklist a validar presença no título e URL.'])
                    </label>
                    <input id="blog_post_focus_keyword" class="input" name="focus_keyword" value="{{ old('focus_keyword', $blogPost->focus_keyword) }}" placeholder="Ex.: acabamento cartão premium">
                </div>

                <div class="field">
                    <label for="blog_post_seo_title">
                        Meta title
                        @include('partials.help-hint', ['text' => 'Título para Google e redes. Ideal entre 40 e 65 caracteres.'])
                    </label>
                    <input id="blog_post_seo_title" class="input" name="seo_title" value="{{ old('seo_title', $blogPost->seo_title) }}">
                </div>

                <div class="field">
                    <label for="blog_post_seo_description">
                        Meta description
                        @include('partials.help-hint', ['text' => 'Resumo para resultado de busca. Ideal entre 120 e 160 caracteres.'])
                    </label>
                    <input id="blog_post_seo_description" class="input" name="seo_description" value="{{ old('seo_description', $blogPost->seo_description) }}">
                </div>

                <div class="field">
                    <label for="blog_post_seo_canonical_url">
                        Canonical URL
                        @include('partials.help-hint', ['text' => 'URL canônica para evitar conteúdo duplicado quando houver versões semelhantes do artigo.'])
                    </label>
                    <input id="blog_post_seo_canonical_url" class="input" name="seo_canonical_url" value="{{ old('seo_canonical_url', $blogPost->seo_canonical_url) }}" placeholder="https://...">
                </div>

                <div class="field">
                    <label for="blog_post_seo_og_title">
                        OG title
                        @include('partials.help-hint', ['text' => 'Título específico para compartilhamento em redes sociais.'])
                    </label>
                    <input id="blog_post_seo_og_title" class="input" name="seo_og_title" value="{{ old('seo_og_title', $blogPost->seo_og_title) }}">
                </div>

                <div class="field">
                    <label for="blog_post_seo_og_description">
                        OG description
                        @include('partials.help-hint', ['text' => 'Descrição específica para redes sociais.'])
                    </label>
                    <input id="blog_post_seo_og_description" class="input" name="seo_og_description" value="{{ old('seo_og_description', $blogPost->seo_og_description) }}">
                </div>

                <div class="field full">
                    <label for="blog_post_seo_og_image_url">
                        OG image URL
                        @include('partials.help-hint', ['text' => 'Imagem usada em compartilhamentos sociais. Se vazio, usamos a capa do artigo.'])
                    </label>
                    <input id="blog_post_seo_og_image_url" class="input" name="seo_og_image_url" value="{{ old('seo_og_image_url', $blogPost->seo_og_image_url) }}">
                </div>

                <label class="radio-card" for="blog_post_seo_noindex" style="margin-top: 26px;">
                    <input id="blog_post_seo_noindex" type="checkbox" name="seo_noindex" value="1" @checked(old('seo_noindex', $blogPost->seo_noindex))>
                    <span>
                        Marcar como noindex
                        @include('partials.help-hint', ['text' => 'Impede indexação em buscadores. Útil para conteúdo interno, rascunho avançado ou material temporário.'])
                    </span>
                </label>

                <div class="card" style="padding:12px;">
                    <div class="tiny muted" style="margin-bottom:6px;">
                        Checklist
                        @include('partials.help-hint', ['text' => 'Validação rápida dos principais pontos de SEO on-page.'])
                    </div>
                    <ul id="seo-checklist" class="clean-list small muted" style="display:grid; gap:4px;"></ul>
                </div>

                <div class="card" style="padding:12px;">
                    <div class="tiny muted" style="margin-bottom:6px;">
                        Preview SERP
                        @include('partials.help-hint', ['text' => 'Simulação aproximada de como o artigo aparece no Google.'])
                    </div>
                    <div id="seo-preview-title" style="color:#1f5eff; font-size:.92rem; line-height:1.3; font-weight:700;"></div>
                    <div id="seo-preview-url" class="tiny" style="margin-top:4px; color:#0f8a5f;"></div>
                    <div id="seo-preview-description" class="small muted" style="margin-top:4px;"></div>
                </div>
            </div>
        </section>

        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Salvar artigo' : 'Criar artigo' }}</button>
            <a href="{{ route('admin.blog.index') }}" class="btn btn-secondary">Voltar</a>
            @if($isEdit)
                <a href="{{ route('blog.show', $blogPost->slug) }}" class="btn btn-secondary" target="_blank" rel="noreferrer">Ver artigo</a>
            @endif
        </div>
    </form>
</div>

@once
    @push('scripts')
        <script src="https://uicdn.toast.com/editor/latest/toastui-editor-all.min.js"></script>
    @endpush
@endonce

@push('scripts')
    <script>
        (function () {
            const fileInput = document.getElementById('blog_post_cover_image');
            const previewField = document.getElementById('blog_cover_preview_field');
            const previewImage = document.getElementById('blog_cover_preview_image');
            const removeCheckbox = document.getElementById('blog_post_remove_cover');

            if (fileInput && previewField && previewImage) {
                const initialPreview = previewImage.getAttribute('src') || '';
                let objectUrl = null;

                const revokeObjectUrl = () => {
                    if (!objectUrl) return;
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
            }

            const titleInput = document.getElementById('blog_post_title');
            const slugInput = document.getElementById('blog_post_slug');
            const contentInput = document.getElementById('blog_post_content');
            const editorShell = document.getElementById('blog_post_editor_shell');
            const editorRoot = document.getElementById('blog_post_editor');
            const seoTitleInput = document.getElementById('blog_post_seo_title');
            const seoDescriptionInput = document.getElementById('blog_post_seo_description');
            const canonicalInput = document.getElementById('blog_post_seo_canonical_url');
            const keywordInput = document.getElementById('blog_post_focus_keyword');
            const scoreValue = document.getElementById('seo-score-value');
            const checklist = document.getElementById('seo-checklist');
            const previewTitle = document.getElementById('seo-preview-title');
            const previewUrl = document.getElementById('seo-preview-url');
            const previewDescription = document.getElementById('seo-preview-description');

            if (!titleInput || !slugInput || !contentInput || !scoreValue || !checklist || !previewTitle || !previewUrl || !previewDescription) {
                return;
            }

            const syncContentInput = (value) => {
                contentInput.value = value;
                contentInput.dispatchEvent(new Event('input', { bubbles: true }));
            };

            if (editorShell && editorRoot && window.toastui && window.toastui.Editor) {
                const blogEditor = new window.toastui.Editor({
                    el: editorRoot,
                    initialValue: contentInput.value || '',
                    initialEditType: 'wysiwyg',
                    previewStyle: 'vertical',
                    height: '420px',
                    usageStatistics: false,
                    hideModeSwitch: false,
                    toolbarItems: [
                        ['heading', 'bold', 'italic', 'strike'],
                        ['hr', 'quote'],
                        ['ul', 'ol', 'task'],
                        ['table', 'link', 'image'],
                        ['code', 'codeblock'],
                        ['scrollSync'],
                    ],
                });

                editorShell.hidden = false;
                contentInput.classList.add('blog-editor-source-hidden');
                syncContentInput(blogEditor.getMarkdown());

                blogEditor.on('change', () => {
                    syncContentInput(blogEditor.getMarkdown());
                });

                const parentForm = contentInput.closest('form');
                if (parentForm) {
                    parentForm.addEventListener('submit', () => {
                        syncContentInput(blogEditor.getMarkdown());
                    });
                }
            }

            let slugTouched = slugInput.value.trim() !== '';

            const slugify = (value) => value
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9\s-]/g, '')
                .trim()
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');

            const buildCheck = (ok, message) => `<li style="color:${ok ? '#0f8a5f' : '#8f221c'};">${ok ? '✓' : '•'} ${message}</li>`;

            const renderSeo = () => {
                const title = titleInput.value.trim();
                const slug = slugInput.value.trim();
                const seoTitle = (seoTitleInput?.value || '').trim();
                const seoDescription = (seoDescriptionInput?.value || '').trim();
                const canonical = (canonicalInput?.value || '').trim();
                const keyword = (keywordInput?.value || '').trim().toLowerCase();
                const content = contentInput.value.trim();

                const effectiveTitle = seoTitle || title;
                const effectiveDescription = seoDescription || '';
                const titleLen = effectiveTitle.length;
                const descLen = effectiveDescription.length;
                const contentLen = content.length;

                const hasKeywordInTitle = keyword !== '' && effectiveTitle.toLowerCase().includes(keyword);
                const hasKeywordInSlug = keyword !== '' && slug.toLowerCase().includes(slugify(keyword));

                let score = 0;
                score += titleLen >= 40 && titleLen <= 65 ? 22 : (titleLen >= 30 ? 12 : 0);
                score += descLen >= 120 && descLen <= 160 ? 22 : (descLen >= 90 ? 10 : 0);
                score += contentLen >= 900 ? 22 : (contentLen >= 500 ? 12 : 0);
                score += hasKeywordInTitle ? 14 : 0;
                score += hasKeywordInSlug ? 10 : 0;
                score += canonical !== '' ? 10 : 0;

                if (score > 100) score = 100;

                scoreValue.textContent = `${score}/100`;

                checklist.innerHTML = [
                    buildCheck(titleLen >= 40 && titleLen <= 65, `Meta title entre 40 e 65 caracteres (${titleLen})`),
                    buildCheck(descLen >= 120 && descLen <= 160, `Meta description entre 120 e 160 caracteres (${descLen})`),
                    buildCheck(contentLen >= 500, `Conteúdo com pelo menos 500 caracteres (${contentLen})`),
                    buildCheck(keyword === '' || hasKeywordInTitle, 'Keyword foco presente no título'),
                    buildCheck(keyword === '' || hasKeywordInSlug, 'Keyword foco presente no slug'),
                    buildCheck(canonical !== '', 'Canonical definido'),
                ].join('');

                previewTitle.textContent = effectiveTitle || 'Título da página';
                previewUrl.textContent = canonical || `${window.location.origin}/blog/${slug || 'slug-do-artigo'}`;
                previewDescription.textContent = effectiveDescription || 'Descrição para resultados de busca.';
            };

            titleInput.addEventListener('input', () => {
                if (!slugTouched) {
                    slugInput.value = slugify(titleInput.value);
                }
                renderSeo();
            });

            slugInput.addEventListener('input', () => {
                slugTouched = slugInput.value.trim() !== '';
                renderSeo();
            });

            [contentInput, seoTitleInput, seoDescriptionInput, canonicalInput, keywordInput].forEach((input) => {
                if (!input) return;
                input.addEventListener('input', renderSeo);
            });

            renderSeo();
        })();
    </script>
@endpush
