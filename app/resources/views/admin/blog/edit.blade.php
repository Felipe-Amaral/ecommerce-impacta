@extends('layouts.store')

@section('title', 'Editar Artigo | Blog Admin')
@section('meta_description', 'Edição de artigo do blog com recursos de SEO avançado.')

@section('content')
    <section class="hero reveal-up" style="margin-bottom: 18px;">
        <div class="link-row" style="align-items:flex-start;">
            <div class="stack" style="gap:6px;">
                <p class="eyebrow">Blog</p>
                <h1 style="font-size: clamp(1.6rem, 3vw, 2.3rem);">Editar artigo</h1>
                <p class="lead">Ajuste conteúdo, status e SEO. URL pública atual: <span class="mono">/blog/{{ $blogPost->slug }}</span></p>
            </div>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <a href="{{ route('admin.blog.index') }}" class="btn btn-secondary">Voltar aos artigos</a>
                <a href="{{ route('blog.show', $blogPost->slug) }}" class="btn btn-secondary" target="_blank" rel="noreferrer">Ver artigo</a>
            </div>
        </div>
    </section>

    <section style="margin-bottom: 28px;">
        @include('admin.blog._form', compact('blogPost', 'categories', 'tags', 'selectedTagIds'))

        <form method="POST" action="{{ route('admin.blog.destroy', $blogPost) }}" onsubmit="return confirm('Remover este artigo?');" style="margin-top: 10px;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-secondary">Remover artigo</button>
        </form>
    </section>
@endsection
