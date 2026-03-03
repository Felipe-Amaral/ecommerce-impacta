@extends('layouts.store')

@section('title', 'Pedidos | Painel da Gráfica')
@section('meta_description', 'Visualização completa de pedidos com filtros de cobrança, produção e conferência de arte.')

@section('content')
    <section class="hero hero-studio reveal-up" style="margin-bottom: 18px;">
        <div class="hero-studio-grid">
            <div class="hero-pane">
                <div class="stack-xl">
                    <div class="pill-list">
                        <span class="badge badge-brand">Painel da gráfica</span>
                        <span class="pill">Pedidos</span>
                        <span class="pill">Cobrança + Produção + Chat</span>
                    </div>

                    <div class="stack">
                        <h1 class="text-gradient" style="font-size: clamp(1.8rem, 3vw, 2.7rem);">Visualização operacional completa dos pedidos</h1>
                        <p class="lead">
                            Filtre por cobrança, etapa de produção e pedidos aguardando arte.
                            Abra cada pedido para revisar arquivos, atualizar workflow e conversar com o cliente pelo chat.
                        </p>
                    </div>

                    <div class="hero-actions">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Voltar ao painel</a>
                        <a href="{{ route('admin.catalog.index') }}" class="btn btn-secondary">Cadastros</a>
                    </div>
                </div>
            </div>

            <div class="hero-pane">
                <div class="metric-grid">
                    <div class="metric-card">
                        <strong>{{ $stats['total_orders'] }}</strong>
                        <span>Pedidos totais</span>
                    </div>
                    <div class="metric-card">
                        <strong>{{ $stats['pending_payment'] }}</strong>
                        <span>Cobrança pendente</span>
                    </div>
                    <div class="metric-card">
                        <strong>{{ $stats['awaiting_artwork_items'] }}</strong>
                        <span>Itens sem arte</span>
                    </div>
                </div>

                <div class="board-card stack" style="margin-top:12px;">
                    <div class="board-title">
                        <strong>Hoje</strong>
                        <span class="tiny muted">operação</span>
                    </div>
                    <div class="process-rail">
                        <div class="process-step">
                            <span class="num">{{ $stats['today_orders'] }}</span>
                            <span class="label">Pedidos de hoje</span>
                            <span class="eta">entrada</span>
                        </div>
                        <div class="process-step">
                            <span class="num">{{ $stats['in_production'] }}</span>
                            <span class="label">Em produção</span>
                            <span class="eta">fila</span>
                        </div>
                        <div class="process-step">
                            <span class="num">R$ {{ number_format((float) $stats['today_revenue'], 0, ',', '.') }}</span>
                            <span class="label">Receita do dia</span>
                            <span class="eta">amostra</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="card card-pad stack-lg" style="margin-bottom: 18px;">
        <div class="section-head">
            <div class="copy">
                <span class="section-kicker">Filtros</span>
                <h2>Triagem operacional</h2>
                <p class="muted">Encontre rapidamente pedidos para cobrança, conferência de arte e produção.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.orders.index') }}" class="form-grid">
            <div class="field full">
                <label for="admin_orders_filter_q">Busca</label>
                <input id="admin_orders_filter_q" type="search" name="q" class="input" value="{{ $filters['q'] }}" placeholder="Pedido, cliente ou e-mail">
            </div>

            <div class="field">
                <label for="admin_orders_filter_status">Status do pedido</label>
                <select id="admin_orders_filter_status" name="status" class="select">
                    <option value="">Todos</option>
                    @foreach(\App\Enums\OrderStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected($filters['status'] === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="admin_orders_filter_payment">Pagamento</label>
                <select id="admin_orders_filter_payment" name="payment_status" class="select">
                    <option value="">Todos</option>
                    @foreach(\App\Enums\PaymentStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected($filters['payment_status'] === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="admin_orders_filter_fulfillment">Produção</label>
                <select id="admin_orders_filter_fulfillment" name="fulfillment_status" class="select">
                    <option value="">Todas as etapas</option>
                    @foreach(\App\Enums\FulfillmentStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected($filters['fulfillment_status'] === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field" style="align-content:end;">
                <label class="radio-card" for="admin_orders_awaiting_artwork">
                    <input id="admin_orders_awaiting_artwork" type="checkbox" name="awaiting_artwork" value="1" @checked($filters['awaiting_artwork'])>
                    <span>Somente pedidos aguardando arte</span>
                </label>
            </div>

            <div class="field full" style="justify-content:end;">
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <button type="submit" class="btn btn-secondary">Aplicar filtros</button>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Limpar</a>
                </div>
            </div>
        </form>
    </section>

    <section class="card card-pad stack" style="margin-bottom: 28px;">
        <div class="link-row">
            <div class="stack" style="gap:4px;">
                <h2>Pedidos cadastrados</h2>
                <p class="small muted">{{ $orders->total() }} pedido(s) encontrado(s)</p>
            </div>
        </div>

        @if($orders->isEmpty())
            <p class="small muted">Nenhum pedido encontrado para os filtros informados.</p>
        @else
            <div class="table-wrap">
                <table class="table-compact">
                    <thead>
                        <tr>
                            <th>Pedido</th>
                            <th>Cliente</th>
                            <th>Status</th>
                            <th>Itens</th>
                            <th>Chat</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($orders as $order)
                        @php
                            $payment = $order->payments->first();
                            $awaitingArtwork = (int) ($order->pending_file_items_count ?? 0) > 0;
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('admin.orders.show', $order) }}"><strong>{{ $order->order_number }}</strong></a>
                                <div class="tiny muted">{{ optional($order->placed_at)->format('d/m/Y H:i') ?: $order->created_at->format('d/m/Y H:i') }}</div>
                                @if($awaitingArtwork)
                                    <div class="tiny" style="color:#7f6120; font-weight:700;">Aguardando arte</div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $order->customer_name }}</strong>
                                <div class="tiny muted">{{ $order->customer_email }}</div>
                                <div class="tiny muted">{{ $order->customer_phone ?: 'Sem telefone' }}</div>
                            </td>
                            <td>
                                @include('partials.status-badge', ['status' => $order->status, 'size' => 'sm'])
                                <div class="tiny muted" style="margin-top:6px;">Pagamento: {{ $order->payment_status->label() }}</div>
                                <div class="tiny muted">Produção: {{ $order->fulfillment_status->label() }}</div>
                                <div class="tiny muted">{{ \App\Support\UiStatus::label(optional($payment?->method)->value ?? 'manual') }}</div>
                            </td>
                            <td>
                                <strong>{{ $order->items_count }}</strong>
                                <div class="tiny muted">{{ $order->items_count === 1 ? 'item' : 'itens' }}</div>
                            </td>
                            <td class="tiny">
                                <a href="{{ route('admin.orders.show', $order) }}#chat-pedido" class="btn btn-secondary btn-sm">
                                    {{ ($order->messages_count ?? 0) > 0 ? 'Chat ('.$order->messages_count.')' : 'Abrir chat' }}
                                </a>
                            </td>
                            <td><strong>R$ {{ number_format((float) $order->total, 2, ',', '.') }}</strong></td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-primary btn-sm">Gerenciar</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{ $orders->links() }}
        @endif
    </section>
@endsection
