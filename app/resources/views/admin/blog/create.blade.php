@extends('layouts.store')

@section('title', 'Novo Artigo | Blog Admin')
@section('meta_description', 'Criação de novo artigo no blog com foco em SEO e publicação.')

@section('content')
    <section class="hero reveal-up" style="margin-bottom: 18px;">
        <div class="link-row" style="align-items:flex-start;">
            <div class="stack" style="gap:6px;">
                <p class="eyebrow">Blog</p>
                <h1 style="font-size: clamp(1.6rem, 3vw, 2.3rem);">Novo artigo</h1>
                <p class="lead">Monte conteúdo editorial com vitrine forte e SEO otimizado desde a criação.</p>
            </div>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <a href="{{ route('admin.blog.index') }}" class="btn btn-secondary">Voltar aos artigos</a>
                <a href="{{ route('admin.blog.categories.index') }}" class="btn btn-secondary">Categorias</a>
                <a href="{{ route('admin.blog.tags.index') }}" class="btn btn-secondary">Tags</a>
            </div>
        </div>
    </section>

    <section style="margin-bottom: 28px;">
        @include('admin.blog._form', compact('blogPost', 'categories', 'tags', 'selectedTagIds'))
    </section>
@endsection
