@extends('layouts.store')

@section('title', 'Meus Pedidos | Área do Cliente')
@section('meta_description', 'Visualização completa dos pedidos com status, pagamento, produção, entrega e chat por pedido.')

@section('content')
    <section class="hero hero-studio reveal-up" style="margin-bottom: 18px;">
        <div class="hero-studio-grid">
            <div class="hero-pane">
                <div class="stack-xl">
                    <div class="pill-list">
                        <span class="badge">Pedidos</span>
                        <span class="pill">Histórico completo</span>
                        <span class="pill">Chat por pedido</span>
                    </div>

                    <div class="stack">
                        <h1 class="text-gradient" style="font-size: clamp(1.8rem, 3vw, 2.7rem);">Acompanhe pedidos, cobrança e produção em uma tela</h1>
                        <p class="lead">
                            Consulte o andamento de cada pedido, acesse o chat com a gráfica e acompanhe o fluxo
                            de pagamento, pré-impressão, produção e entrega.
                        </p>
                    </div>

                    <div class="hero-actions">
                        <a href="{{ route('account.dashboard') }}" class="btn btn-secondary">Resumo da conta</a>
                        <a href="{{ route('catalog.index') }}" class="btn btn-primary">Fazer novo pedido</a>
                    </div>
                </div>
            </div>

            <div class="hero-pane">
                <div class="metric-grid">
                    <div class="metric-card">
                        <strong>{{ $stats['total'] }}</strong>
                        <span>Total de pedidos</span>
                    </div>
                    <div class="metric-card">
                        <strong>{{ $stats['pending_payment'] }}</strong>
                        <span>Aguardando pagamento</span>
                    </div>
                    <div class="metric-card">
                        <strong>{{ $stats['delivered'] }}</strong>
                        <span>Entregues</span>
                    </div>
                </div>

                <div class="board-card stack" style="margin-top: 12px;">
                    <div class="board-title">
                        <strong>Resumo financeiro</strong>
                        <span class="tiny muted">histórico</span>
                    </div>
                    <div class="process-rail">
                        <div class="process-step">
                            <span class="num">{{ $stats['in_production'] }}</span>
                            <span class="label">Em andamento</span>
                            <span class="eta">produção</span>
                        </div>
                        <div class="process-step">
                            <span class="num">R$ {{ number_format((float) $stats['total_value'], 0, ',', '.') }}</span>
                            <span class="label">Total acumulado</span>
                            <span class="eta">amostra</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="card card-pad stack-lg" style="margin-bottom: 28px;">
        <div class="section-head">
            <div class="copy">
                <span class="section-kicker">Filtros</span>
                <h2>Encontrar pedido</h2>
                <p class="muted">Busque por número do pedido, cliente/e-mail e filtre por status.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('account.orders.index') }}" class="form-grid">
            <div class="field">
                <label for="orders_filter_q">Busca</label>
                <input id="orders_filter_q" type="search" name="q" class="input" value="{{ $filters['q'] }}" placeholder="Ex.: PED-2026-00012">
            </div>

            <div class="field">
                <label for="orders_filter_status">Status do pedido</label>
                <select id="orders_filter_status" name="status" class="select">
                    <option value="">Todos</option>
                    @foreach(\App\Enums\OrderStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected($filters['status'] === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="orders_filter_payment_status">Pagamento</label>
                <select id="orders_filter_payment_status" name="payment_status" class="select">
                    <option value="">Todos</option>
                    @foreach(\App\Enums\PaymentStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected($filters['payment_status'] === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field" style="justify-content:end;">
                <label>&nbsp;</label>
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <button class="btn btn-secondary" type="submit">Aplicar</button>
                    <a href="{{ route('account.orders.index') }}" class="btn btn-secondary">Limpar</a>
                </div>
            </div>
        </form>
    </section>

    <section class="card card-pad stack" style="margin-bottom: 28px;">
        <div class="link-row">
            <div class="stack" style="gap:4px;">
                <h2>Pedidos cadastrados</h2>
                <p class="small muted">{{ $orders->total() }} registro(s) encontrado(s)</p>
            </div>
        </div>

        @if($orders->isEmpty())
            <div class="card card-pad stack" style="box-shadow:none;">
                <h3>Nenhum pedido encontrado</h3>
                <p class="small muted">Ajuste os filtros ou faça um novo pedido pelo catálogo.</p>
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <a href="{{ route('account.orders.index') }}" class="btn btn-secondary">Limpar filtros</a>
                    <a href="{{ route('catalog.index') }}" class="btn btn-primary">Ir para o catálogo</a>
                </div>
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
                            <th>Entrega</th>
                            <th>Conversa</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($orders as $order)
                        @php
                            $payment = $order->payments->first();
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('account.orders.show', $order) }}"><strong>{{ $order->order_number }}</strong></a>
                                <div class="tiny muted">{{ optional($order->placed_at)->format('d/m/Y H:i') ?: $order->created_at->format('d/m/Y H:i') }}</div>
                                <div class="tiny muted">{{ $order->customer_email }}</div>
                            </td>
                            <td>
                                @include('partials.status-badge', ['status' => $order->status, 'size' => 'sm'])
                                <div class="tiny muted" style="margin-top:6px;">Pagamento: {{ $order->payment_status->label() }}</div>
                                <div class="tiny muted">Produção: {{ $order->fulfillment_status->label() }}</div>
                            </td>
                            <td>
                                <strong>{{ $order->items_count }}</strong>
                                <div class="tiny muted">{{ $order->items_count === 1 ? 'item' : 'itens' }}</div>
                            </td>
                            <td>
                                <strong>R$ {{ number_format((float) $order->total, 2, ',', '.') }}</strong>
                                <div class="tiny muted">{{ \App\Support\UiStatus::label(optional($payment?->method)->value ?? 'manual') }}</div>
                            </td>
                            <td class="tiny">
                                <div>{{ $order->shipping_method_label ?: 'Entrega' }}</div>
                                <div class="muted">
                                    @if($order->shipping_method_code === 'pickup_counter')
                                        Retirada no balcão
                                    @elseif($order->shipping_delivery_days)
                                        {{ $order->shipping_delivery_days }} dia(s) úteis
                                    @else
                                        Prazo sob consulta
                                    @endif
                                </div>
                            </td>
                            <td class="tiny">
                                <a href="{{ route('account.orders.show', $order) }}#chat-pedido" class="btn btn-secondary btn-sm">
                                    {{ ($order->messages_count ?? 0) > 0 ? 'Chat ('.$order->messages_count.')' : 'Abrir chat' }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('account.orders.show', $order) }}" class="btn btn-primary btn-sm">Detalhes</a>
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
