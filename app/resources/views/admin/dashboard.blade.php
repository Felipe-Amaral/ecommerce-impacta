@extends('layouts.store')

@section('title', 'Painel da Gráfica | Gráfica Uriah Criativa')
@section('meta_description', 'Painel administrativo com pedidos, cobrança e fila de produção.')

@section('content')
    <section class="hero hero-studio reveal-up" style="margin-bottom: 18px;">
        <div class="hero-studio-grid">
            <div class="hero-pane">
                <div class="stack-xl">
                    <div class="pill-list">
                        <span class="badge badge-brand">Painel da Gráfica</span>
                        <span class="badge">Operação</span>
                    </div>

                    <div class="stack">
                        <h1 class="text-gradient" style="font-size: clamp(1.9rem, 3vw, 2.9rem);">Cobrança, pré-impressão e produção em uma visão</h1>
                        <p class="lead">
                            Acompanhe rapidamente o que precisa de cobrança, conferência de arquivo e liberação para impressão.
                        </p>
                    </div>

                    <div class="timeline-card">
                        <span class="section-kicker">Rotina operacional</span>
                        <ul class="timeline-list">
                            <li class="timeline-item">
                                <span class="marker">1</span>
                                <div>
                                    <div class="title">Cobrança</div>
                                    <div class="desc">Validar pagamento e atualizar status do pedido.</div>
                                </div>
                            </li>
                            <li class="timeline-item">
                                <span class="marker">2</span>
                                <div>
                                    <div class="title">Conferência de arquivo</div>
                                    <div class="desc">Checar arte, especificações e observações do item.</div>
                                </div>
                            </li>
                            <li class="timeline-item">
                                <span class="marker">3</span>
                                <div>
                                    <div class="title">Produção e expedição</div>
                                    <div class="desc">Organizar fila por etapa e liberar pedidos finalizados.</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="hero-pane">
                <div class="metric-grid">
                    <div class="metric-card">
                        <strong>{{ $todayOrders }}</strong>
                        <span>Pedidos hoje</span>
                    </div>
                    <div class="metric-card">
                        <strong>{{ $pendingPayment }}</strong>
                        <span>Aguardando cobrança</span>
                    </div>
                    <div class="metric-card">
                        <strong>R$ {{ number_format($todayRevenue, 2, ',', '.') }}</strong>
                        <span>Total do dia</span>
                    </div>
                </div>

                <div class="board-card stack" style="margin-top: 12px;">
                    <div class="board-title">
                        <strong>Fila aberta</strong>
                        <span class="tiny muted">produção</span>
                    </div>
                    <div class="process-rail">
                        <div class="process-step">
                            <span class="num">{{ $productionOpen }}</span>
                            <span class="label">Pedidos em andamento</span>
                            <span class="eta">ativos</span>
                        </div>
                        <div class="process-step">
                            <span class="num">{{ $pendingFileItems->count() }}</span>
                            <span class="label">Itens aguardando arte</span>
                            <span class="eta">atenção</span>
                        </div>
                        <div class="process-step">
                            <span class="num">{{ $recentOrders->count() }}</span>
                            <span class="label">Pedidos recentes</span>
                            <span class="eta">últimos</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="split" style="margin-bottom: 24px;">
        <section class="card card-pad stack-lg">
            <div class="section-head">
                <div class="copy">
                    <span class="section-kicker">Pedidos recentes</span>
                    <h2>Atendimento e produção</h2>
                    <p class="muted">Lista rápida para priorizar cobrança, conferência e liberação.</p>
                </div>
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">Ver todos os pedidos</a>
                    <a href="{{ route('admin.catalog.index') }}" class="btn btn-secondary btn-sm">Cadastros</a>
                    <a href="{{ route('admin.blog.index') }}" class="btn btn-secondary btn-sm">Blog</a>
                    <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary btn-sm">Contatos</a>
                </div>
            </div>

            @if($recentOrders->isEmpty())
                <p class="small muted">Sem pedidos registrados ainda.</p>
            @else
                <div class="table-wrap">
                    <table class="table-compact">
                        <thead>
                            <tr>
                                <th>Pedido</th>
                                <th>Cliente</th>
                                <th>Status</th>
                                <th>Itens</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($recentOrders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}"><strong>{{ $order->order_number }}</strong></a>
                                    <div class="tiny muted">{{ optional($order->placed_at)->format('d/m/Y H:i') ?: $order->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td>
                                    <strong>{{ $order->customer_name }}</strong>
                                    <div class="tiny muted">{{ $order->customer_email }}</div>
                                </td>
                                <td>
                                    @include('partials.status-badge', ['status' => $order->status, 'size' => 'sm'])
                                    <div class="tiny muted" style="margin-top:4px;">
                                        Produção: {{ $order->fulfillment_status->label() }}
                                    </div>
                                    <div class="tiny muted">
                                        Pagamento: {{ $order->payment_status->label() }}
                                    </div>
                                </td>
                                <td>{{ $order->items_count }}</td>
                                <td><strong>R$ {{ number_format((float) $order->total, 2, ',', '.') }}</strong></td>
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
                        <span class="section-kicker">Fila por etapa</span>
                        <h2 style="font-size:1.35rem;">Produção</h2>
                    </div>
                </div>

                <div class="stack">
                    @foreach($queueByFulfillment as $row)
                        <div class="summary-row">
                            @include('partials.status-badge', ['status' => $row['status'], 'size' => 'sm'])
                            <strong>{{ $row['count'] }}</strong>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="card card-pad stack">
                <div class="section-head">
                    <div class="copy">
                        <span class="section-kicker">Pré-impressão</span>
                        <h3>Itens aguardando arte</h3>
                    </div>
                </div>
                @if($pendingFileItems->isEmpty())
                    <p class="small muted">Nenhum item aguardando arquivo no momento.</p>
                @else
                    <ul class="timeline-list">
                        @foreach($pendingFileItems as $item)
                            <li class="timeline-item">
                                <span class="marker">{{ $loop->iteration }}</span>
                                <div>
                                    <div class="title">{{ $item->product_name }}</div>
                                    <div class="desc">
                                        @if($item->order)
                                            <a href="{{ route('admin.orders.show', $item->order) }}">{{ $item->order->order_number }}</a> •
                                        @endif
                                        {{ $item->quantity }} un • {{ $item->variant_name }}
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            <section class="card card-pad stack">
                <div class="section-head">
                    <div class="copy">
                        <span class="section-kicker">Contato</span>
                        <h3>Inbox comercial</h3>
                    </div>
                </div>
                <div class="summary-row">
                    <span class="muted">Mensagens não lidas</span>
                    <strong>{{ $unreadContacts }}</strong>
                </div>
                <div class="summary-row">
                    <span class="muted">Mensagens de hoje</span>
                    <strong>{{ $todayContacts }}</strong>
                </div>
                <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary btn-sm">Abrir caixa de entrada</a>
            </section>
        </aside>
    </section>
@endsection
