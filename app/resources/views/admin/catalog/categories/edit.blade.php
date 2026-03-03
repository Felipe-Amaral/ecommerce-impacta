@extends('layouts.store')

@section('title', 'Editar Categoria | Cadastros')

@section('content')
    <section class="hero reveal-up" style="margin-bottom: 18px;">
        <div class="link-row" style="align-items:flex-start;">
            <div class="stack" style="gap:6px;">
                <p class="eyebrow">Cadastros</p>
                <h1 style="font-size: clamp(1.5rem, 3vw, 2.2rem);">Editar categoria</h1>
                <p class="lead">{{ $category->name }} • ajuste nome, slug, ordem e SEO.</p>
            </div>
            <a href="{{ route('admin.catalog.categories.index') }}" class="btn btn-secondary">Voltar</a>
        </div>
    </section>

    <section class="container-narrow" style="margin-bottom: 28px;">
        <div class="card card-pad stack-lg">
            <form method="POST" action="{{ route('admin.catalog.categories.update', $category) }}" class="stack">
                @csrf
                @method('PUT')
                <div class="form-grid">
                    <div class="field full">
                        <label for="category_name">Nome</label>
                        <input id="category_name" class="input" name="name" value="{{ old('name', $category->name) }}" required>
                    </div>
                    <div class="field">
                        <label for="category_slug">Slug</label>
                        <input id="category_slug" class="input" name="slug" value="{{ old('slug', $category->slug) }}">
                    </div>
                    <div class="field">
                        <label for="category_sort_order">Ordem</label>
                        <input id="category_sort_order" class="input" type="number" min="0" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}">
                    </div>
                    <div class="field full">
                        <label for="category_description">Descrição</label>
                        <textarea id="category_description" class="textarea" name="description">{{ old('description', $category->description) }}</textarea>
                    </div>
                    <div class="field">
                        <label for="category_meta_title">Meta title</label>
                        <input id="category_meta_title" class="input" name="meta_title" value="{{ old('meta_title', $category->meta_title) }}">
                    </div>
                    <div class="field">
                        <label for="category_meta_description">Meta description</label>
                        <input id="category_meta_description" class="input" name="meta_description" value="{{ old('meta_description', $category->meta_description) }}">
                    </div>
                </div>
                <label class="radio-card" for="category_is_active">
                    <input id="category_is_active" type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active))>
                    <span>Categoria ativa</span>
                </label>
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <button type="submit" class="btn btn-primary">Salvar alterações</button>
                    <a href="{{ route('admin.catalog.categories.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </section>
@endsection
