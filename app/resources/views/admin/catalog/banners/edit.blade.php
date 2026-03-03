@extends('layouts.store')

@section('title', 'Editar Banner | Cadastros')
@section('meta_description', 'Editar banner rotativo da home.')

@section('content')
    <section class="hero reveal-up" style="margin-bottom: 18px;">
        <div class="link-row" style="align-items:flex-start;">
            <div class="stack" style="gap:6px;">
                <p class="eyebrow">Cadastros</p>
                <h1 style="font-size: clamp(1.5rem, 3vw, 2.2rem);">Editar banner da home</h1>
                <p class="lead">{{ $banner->name }} • ajuste texto, tema, ordem e status de exibição.</p>
            </div>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <a href="{{ route('admin.catalog.banners.index') }}" class="btn btn-secondary">Voltar</a>
                <a href="{{ route('home') }}" class="btn btn-secondary" target="_blank" rel="noreferrer">Ver home</a>
            </div>
        </div>
    </section>

    <section style="margin-bottom: 28px;">
        @include('admin.catalog.banners._form', ['banner' => $banner])
    </section>
@endsection
