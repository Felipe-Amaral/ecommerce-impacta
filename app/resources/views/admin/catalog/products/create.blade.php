@extends('layouts.store')

@section('title', 'Novo Produto | Cadastros')

@section('content')
    <section class="hero reveal-up" style="margin-bottom: 18px;">
        <div class="link-row" style="align-items:flex-start;">
            <div class="stack" style="gap:6px;">
                <p class="eyebrow">Cadastros</p>
                <h1 style="font-size: clamp(1.6rem, 3vw, 2.3rem);">Novo produto</h1>
                <p class="lead">Cadastre um novo item da gráfica e depois inclua as variações por tiragem e acabamento.</p>
            </div>
            <a href="{{ route('admin.catalog.products.index') }}" class="btn btn-secondary">Voltar</a>
        </div>
    </section>

    <section style="margin-bottom: 28px;">
        @include('admin.catalog.products._form', compact('product', 'categories', 'specificationsText'))
    </section>
@endsection
