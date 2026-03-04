@extends('layouts.store')

@section('title', 'Editar Case | Portfólio Admin')
@section('meta_description', 'Edição avançada de case do portfólio com controles de publicação e SEO.')

@section('content')
    <section class="hero reveal-up" style="margin-bottom: 18px;">
        <div class="link-row" style="align-items:flex-start;">
            <div class="stack" style="gap:6px;">
                <p class="eyebrow">Portfólio</p>
                <h1 style="font-size: clamp(1.6rem, 3vw, 2.3rem);">Editar case</h1>
                <p class="lead">Ajuste dados do projeto, status de publicação e SEO. URL atual: <span class="mono">/portfolio/{{ $portfolioProject->slug }}</span></p>
            </div>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <a href="{{ route('admin.portfolio.index') }}" class="btn btn-secondary">Voltar aos cases</a>
                <a href="{{ route('portfolio.show', $portfolioProject->slug) }}" class="btn btn-secondary" target="_blank" rel="noreferrer">Ver case</a>
            </div>
        </div>
    </section>

    <section style="margin-bottom: 28px;">
        @include('admin.portfolio._form', compact('portfolioProject', 'categories'))

        <form method="POST" action="{{ route('admin.portfolio.destroy', $portfolioProject) }}" onsubmit="return confirm('Remover este case?');" style="margin-top: 10px;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-secondary">Remover case</button>
        </form>
    </section>
@endsection
