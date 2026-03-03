@extends('layouts.store')

@section('title', 'Minha Conta | Gráfica Uriah Criativa')
@section('meta_description', 'Área do cliente para acompanhar pedidos e dados de entrega.')

@section('content')
    @php
        $totalPedidos = $orders->count();
        $pedidosPendentes = $orders->where('status.value', 'pending_payment')->count();
        $totalInvestido = (float) $orders->sum('total');
    @endphp

    <section class="hero hero-studio reveal-up">
        <div class="hero-studio-grid">
            <div class="hero-pane">
                <div class="stack-xl">
                    <div class="pill-list">
                        <span class="badge">Minha Conta</span>
                        @if($user->is_admin)
                            <span class="badge badge-brand">Administrador</span>
                        @endif
                    </div>

                    <div class="stack">
                        <h1 class="text-gradient" style="font-size: clamp(1.85rem, 3vw, 2.8rem);">Olá, {{ strtok($user->name, ' ') }}</h1>
                        <p class="lead">
                            Acompanhe pedidos, status de produção e próximos passos da operação.
                            Consulte cobranças, andamento da pré-impressão, produção e entrega em um só lugar.
                        </p>
                    </div>

                    <div class="glass-panel">
                        <div class="link-row">
                            <div class="small muted">
                                <strong>{{ $user->email }}</strong>
                                @if($user->phone)
                                    • {{ $user->phone }}
                                @endif
                            </div>
                            <div style="display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end;">
                                @if($user->is_admin)
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">Painel da gráfica</a>
                                @endif
                                <a href="{{ route('catalog.index') }}" class="btn btn-secondary btn-sm">Comprar mais</a>
                            </div>
                        </div>
                    </div>

                    <div class="timeline-card">
                        <span class="section-kicker">Fluxo do cliente</span>
                        <ul class="timeline-list">
                            <li class="timeline-item">
                                <span class="marker">1</span>
                                <div>
                                    <div class="title">Pedido</div>
                                    <div class="desc">Itens e configurações registrados no momento da compra.</div>
                                </div>
                            </li>
                            <li class="timeline-item">
                                <span class="marker">2</span>
                                <div>
                                    <div class="title">Pagamento</div>
                                    <div class="desc">Cobrança registrada com status para acompanhamento.</div>
                                </div>
                            </li>
                            <li class="timeline-item">
                                <span class="marker">3</span>
                                <div>
                                    <div class="title">Produção</div>
                                    <div class="desc">Pré-impressão, produção e expedição atualizadas por etapa.</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="hero-pane">
                <div class="metric-grid">
                    <div class="metric-card">
                        <strong>{{ $totalPedidos }}</strong>
                        <span>Pedidos recentes</span>
                    </div>
                    <div class="metric-card">
                        <strong>{{ $pedidosPendentes }}</strong>
                        <span>Aguardando pagamento</span>
                    </div>
                    <div class="metric-card">
                        <strong>R$ {{ number_format($totalInvestido, 2, ',', '.') }}</strong>
                        <span>Total (amostra)</span>
                    </div>
                </div>

                <div class="board-card stack" style="margin-top: 12px;">
                    <div class="board-title">
                        <strong>Status de conta</strong>
                        <span class="tiny muted">resumo</span>
                    </div>
                    <div class="process-rail">
                        <div class="process-step">
                            <span class="num">{{ $totalPedidos > 0 ? '✓' : '1' }}</span>
                            <span class="label">Histórico de pedidos</span>
                            <span class="eta">{{ $totalPedidos }}</span>
                        </div>
                        <div class="process-step">
                            <span class="num">{{ $addresses->count() > 0 ? '✓' : '2' }}</span>
                            <span class="label">Endereços salvos</span>
                            <span class="eta">{{ $addresses->count() }}</span>
                        </div>
                        <div class="process-step">
                            <span class="num">3</span>
                            <span class="label">Uploads de arte</span>
                            <span class="eta">em breve</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="split" style="margin-bottom: 28px;">
        <section class="card card-pad stack-lg">
                <div class="link-row">
                    <div class="stack" style="gap:4px;">
                        <h2>Pedidos</h2>
                        <p class="small muted">Últimos pedidos vinculados à sua conta.</p>
                    </div>
                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                        <a href="{{ route('account.orders.index') }}" class="btn btn-secondary btn-sm">Ver todos</a>
                        <a href="{{ route('catalog.index') }}" class="btn btn-secondary btn-sm">Novo pedido</a>
                    </div>
                </div>

            @if($orders->isEmpty())
                <div class="card card-pad stack">
                    <h3>Nenhum pedido ainda</h3>
                    <p class="small muted">Faça seu primeiro pedido pelo catálogo para acompanhar aqui.</p>
                    <div><a href="{{ route('catalog.index') }}" class="btn btn-primary">Ir para o catálogo</a></div>
                </div>
            @else
                <div class="table-wrap">
                    <table class="table-compact">
                        <thead>
                            <tr>
                                <th>Pedido</th>
                                <th>Status</th>
                                <th>Itens</th>
                                <th>Total</th>
                                <th>Pagamento</th>
                                <th>Conversa</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('account.orders.show', $order) }}"><strong>{{ $order->order_number }}</strong></a>
                                    <div class="tiny muted">{{ optional($order->placed_at)->format('d/m/Y H:i') ?: $order->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td>
                                    @include('partials.status-badge', ['status' => $order->status, 'size' => 'sm'])
                                    <div class="tiny muted" style="margin-top:4px;">
                                        Produção: {{ $order->fulfillment_status->label() }}
                                    </div>
                                </td>
                                <td>{{ $order->items_count }}</td>
                                <td><strong>R$ {{ number_format((float) $order->total, 2, ',', '.') }}</strong></td>
                                <td class="tiny">
                                    <div>{{ $order->payment_status->label() }}</div>
                                    <div class="muted">
                                        {{ \App\Support\UiStatus::label(optional($order->payments->first()?->method)->value ?? 'manual') }}
                                    </div>
                                </td>
                                <td class="tiny">
                                    @if(($order->messages_count ?? 0) > 0)
                                        <a href="{{ route('account.orders.show', $order) }}#chat-pedido" class="btn btn-secondary btn-sm">
                                            Chat ({{ $order->messages_count }})
                                        </a>
                                    @else
                                        <a href="{{ route('account.orders.show', $order) }}#chat-pedido" class="btn btn-secondary btn-sm">
                                            Abrir chat
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <aside class="stack-lg">
            <section class="card card-pad stack-lg floating-sticky">
                <div class="section-head">
                    <div class="copy">
                        <span class="section-kicker">Cadastro</span>
                        <h2 style="font-size:1.35rem;">Endereços</h2>
                    </div>
                </div>
                @if($addresses->isEmpty())
                    <p class="small muted">Nenhum endereço salvo ainda. Os dados informados no checkout ficam registrados no pedido para atendimento e entrega.</p>
                @else
                    <div class="stack">
                        @foreach($addresses as $address)
                            <div class="glass-panel stack">
                                <div class="link-row">
                                    <strong>{{ $address->label ?: 'Endereço' }}</strong>
                                    @if($address->is_default_shipping)
                                        <span class="badge">Entrega padrão</span>
                                    @endif
                                </div>
                                <div class="small muted">
                                    {{ $address->recipient_name }}<br>
                                    {{ $address->street }}, {{ $address->number }} @if($address->complement)- {{ $address->complement }} @endif<br>
                                    {{ $address->district }} - {{ $address->city }}/{{ $address->state }}<br>
                                    CEP {{ $address->zipcode }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            <section class="card card-pad stack">
                <div class="section-head">
                    <div class="copy">
                        <span class="section-kicker">Atendimento</span>
                        <h3>Procedimentos do pedido</h3>
                    </div>
                </div>
                <ul class="timeline-list">
                    <li class="timeline-item">
                        <span class="marker">1</span>
                        <div><div class="title">Cobrança e confirmação</div><div class="desc">Pagamento confirmado para liberação da produção.</div></div>
                    </li>
                    <li class="timeline-item">
                        <span class="marker">2</span>
                        <div><div class="title">Conferência de arte</div><div class="desc">Validação técnica antes de imprimir.</div></div>
                    </li>
                    <li class="timeline-item">
                        <span class="marker">3</span>
                        <div><div class="title">Produção e acabamento</div><div class="desc">Etapas internas acompanhadas por status.</div></div>
                    </li>
                    <li class="timeline-item">
                        <span class="marker">4</span>
                        <div><div class="title">Expedição e entrega</div><div class="desc">Retirada local ou envio com atualização ao cliente.</div></div>
                    </li>
                </ul>
            </section>
        </aside>
    </section>
@endsection
