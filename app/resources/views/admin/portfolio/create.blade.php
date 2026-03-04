@extends('layouts.store')

@section('title', 'Novo Case | Portfólio Admin')
@section('meta_description', 'Criação de novo case para o portfólio público com foco em UX e SEO.')

@section('content')
    <section class="hero reveal-up" style="margin-bottom: 18px;">
        <div class="link-row" style="align-items:flex-start;">
            <div class="stack" style="gap:6px;">
                <p class="eyebrow">Portfólio</p>
                <h1 style="font-size: clamp(1.6rem, 3vw, 2.3rem);">Novo case</h1>
                <p class="lead">Cadastre projetos com desafio, solução, resultados e SEO completo para fortalecer a vitrine da marca.</p>
            </div>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <a href="{{ route('admin.portfolio.index') }}" class="btn btn-secondary">Voltar aos cases</a>
                <a href="{{ route('admin.portfolio.categories.index') }}" class="btn btn-secondary">Categorias</a>
            </div>
        </div>
    </section>

    <section style="margin-bottom: 28px;">
        @include('admin.portfolio._form', compact('portfolioProject', 'categories'))
    </section>
@endsection
