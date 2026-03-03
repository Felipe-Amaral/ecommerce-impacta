@extends('layouts.store')

@section('title', 'Tags do Blog | Admin')
@section('meta_description', 'Gestão de tags para agrupamento e SEO interno do blog.')

@section('content')
    <section class="hero reveal-up" style="margin-bottom: 18px;">
        <div class="link-row" style="align-items:flex-start;">
            <div class="stack" style="gap:6px;">
                <p class="eyebrow">Blog</p>
                <h1 style="font-size: clamp(1.6rem, 3vw, 2.3rem);">Tags editoriais</h1>
                <p class="lead">Crie agrupamentos transversais para melhorar descoberta de conteúdo no blog.</p>
            </div>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <a href="{{ route('admin.blog.index') }}" class="btn btn-secondary">Artigos</a>
                <a href="{{ route('admin.blog.categories.index') }}" class="btn btn-secondary">Categorias</a>
            </div>
        </div>
    </section>

    <section class="split" style="margin-bottom: 28px;">
        <section class="card card-pad stack-lg">
            <div class="section-head">
                <div class="copy">
                    <span class="section-kicker">Lista</span>
                    <h2>Tags cadastradas</h2>
                </div>
            </div>

            @if($tags->isEmpty())
                <p class="small muted">Nenhuma tag cadastrada ainda.</p>
            @else
                <div class="stack" style="gap:10px;">
                    @foreach($tags as $item)
                        <form method="POST" action="{{ route('admin.blog.tags.update', $item) }}" class="card" style="padding:12px; display:grid; gap:10px;">
                            @csrf
                            @method('PUT')

                            <div class="form-grid">
                                <div class="field">
                                    <label for="tag_name_{{ $item->id }}">Nome</label>
                                    <input id="tag_name_{{ $item->id }}" class="input" name="name" value="{{ old('name.'.$item->id, $item->name) }}" required>
                                </div>

                                <div class="field">
                                    <label for="tag_slug_{{ $item->id }}">Slug</label>
                                    <input id="tag_slug_{{ $item->id }}" class="input mono" name="slug" value="{{ old('slug.'.$item->id, $item->slug) }}">
                                </div>

                                <div class="field full">
                                    <label for="tag_desc_{{ $item->id }}">Descrição</label>
                                    <textarea id="tag_desc_{{ $item->id }}" class="textarea" name="description" style="min-height:80px;">{{ old('description.'.$item->id, $item->description) }}</textarea>
                                </div>

                                <label class="radio-card" for="tag_featured_{{ $item->id }}" style="margin-top: 22px;">
                                    <input id="tag_featured_{{ $item->id }}" type="checkbox" name="is_featured" value="1" @checked(old('is_featured.'.$item->id, $item->is_featured))>
                                    <span>Tag destacada</span>
                                </label>
                            </div>

                            <div class="link-row">
                                <div class="tiny muted">{{ $item->posts_count }} post(s) vinculado(s)</div>
                                <button type="submit" class="btn btn-secondary btn-sm">Salvar</button>
                            </div>
                        </form>
                    @endforeach
                </div>
            @endif
        </section>

        <aside class="stack-lg">
            <div class="floating-sticky">
                <div class="card card-pad stack-lg">
                    <div class="section-head">
                        <div class="copy">
                            <span class="section-kicker">Nova tag</span>
                            <h2 style="font-size:1.15rem;">Cadastrar</h2>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.blog.tags.store') }}" class="stack">
                        @csrf

                        <div class="field">
                            <label for="new_tag_name">Nome</label>
                            <input id="new_tag_name" class="input" name="name" value="{{ old('name', $tag->name) }}" required>
                        </div>

                        <div class="field">
                            <label for="new_tag_slug">Slug</label>
                            <input id="new_tag_slug" class="input" name="slug" value="{{ old('slug', $tag->slug) }}">
                        </div>

                        <div class="field">
                            <label for="new_tag_desc">Descrição</label>
                            <textarea id="new_tag_desc" class="textarea" name="description" style="min-height:90px;">{{ old('description', $tag->description) }}</textarea>
                        </div>

                        <label class="radio-card" for="new_tag_featured">
                            <input id="new_tag_featured" type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $tag->is_featured))>
                            <span>Tag destacada</span>
                        </label>

                        <button type="submit" class="btn btn-primary">Criar tag</button>
                    </form>
                </div>
            </div>
        </aside>
    </section>
@endsection
