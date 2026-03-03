@extends('layouts.store')

@section('title', 'Banners da Home | Cadastros')
@section('meta_description', 'Cadastre e gerencie banners rotativos da home.')

@section('content')
    <section class="hero reveal-up" style="margin-bottom: 18px;">
        <div class="link-row" style="align-items:flex-start;">
            <div class="stack" style="gap:6px;">
                <p class="eyebrow">Cadastros</p>
                <h1 style="font-size: clamp(1.6rem, 3vw, 2.3rem);">Banners rotativos da home</h1>
                <p class="lead">Gerencie os banners exibidos logo abaixo do menu superior da página inicial.</p>
            </div>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <a href="{{ route('admin.catalog.index') }}" class="btn btn-secondary">Voltar aos cadastros</a>
                <a href="{{ route('home') }}" class="btn btn-secondary" target="_blank" rel="noreferrer">Ver home</a>
            </div>
        </div>
    </section>

    <section class="split" style="margin-bottom: 28px;">
        <section class="card card-pad stack-lg">
            <div class="link-row">
                <div class="stack" style="gap:4px;">
                    <h2 style="font-size:1.2rem;">Banners cadastrados</h2>
                    <p class="small muted">{{ $banners->count() }} banner(s)</p>
                </div>
            </div>

            @if($banners->isEmpty())
                <p class="small muted">Nenhum banner cadastrado ainda.</p>
            @else
                <div class="table-wrap">
                    <table class="table-compact">
                        <thead>
                            <tr>
                                <th>Ordem</th>
                                <th>Banner</th>
                                <th>Tema</th>
                                <th>Status</th>
                                <th>Período</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($banners as $item)
                            <tr>
                                <td>{{ $item->sort_order }}</td>
                                <td>
                                    @if($item->background_image_url)
                                        <div style="margin-bottom:8px;">
                                            <img
                                                src="{{ $item->background_image_url }}"
                                                alt="Miniatura do banner {{ $item->name }}"
                                                style="width:140px; height:64px; object-fit:cover; border-radius:10px; border:1px solid rgba(0,0,0,.08); display:block;"
                                            >
                                        </div>
                                    @endif
                                    <strong>{{ $item->headline }}</strong>
                                    <div class="tiny muted">{{ $item->name }}</div>
                                    @if($item->badge)
                                        <div class="tiny muted">{{ $item->badge }}</div>
                                    @endif
                                    <div class="tiny muted">
                                        Texto: {{ data_get($item->metadata, 'text_side') === 'right' ? 'direita' : 'esquerda' }}
                                    </div>
                                </td>
                                <td>{{ $item->theme }}</td>
                                <td>
                                    <span class="status-dot {{ $item->is_active ? 'production' : 'pending' }}">
                                        {{ $item->is_active ? 'ativo' : 'inativo' }}
                                    </span>
                                    @if(($item->starts_at && $item->starts_at->isFuture()) || ($item->ends_at && $item->ends_at->isPast()))
                                        <div class="tiny muted" style="margin-top:4px;">fora da janela</div>
                                    @endif
                                </td>
                                <td class="tiny">
                                    <div>Início: {{ $item->starts_at?->format('d/m/Y H:i') ?: 'livre' }}</div>
                                    <div>Fim: {{ $item->ends_at?->format('d/m/Y H:i') ?: 'livre' }}</div>
                                </td>
                                <td>
                                    <div style="display:flex; gap:6px; flex-wrap:wrap;">
                                        <a href="{{ route('admin.catalog.banners.edit', $item) }}" class="btn btn-secondary btn-sm">Editar</a>
                                        <form method="POST" action="{{ route('admin.catalog.banners.status', $item) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-secondary btn-sm">{{ $item->is_active ? 'Desativar' : 'Ativar' }}</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.catalog.banners.destroy', $item) }}" onsubmit="return confirm('Remover este banner?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-secondary btn-sm">Remover</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <aside class="stack-lg">
            <div class="floating-sticky">
                @include('admin.catalog.banners._form', ['banner' => $banner])
            </div>
        </aside>
    </section>
@endsection
