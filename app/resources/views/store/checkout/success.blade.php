@extends('layouts.store')

@section('title', 'Pedido criado | Gráfica Uriah Criativa')

@section('content')
    <section class="hero hero-studio reveal-up" style="margin-bottom: 18px;">
        <div class="hero-studio-grid">
            <div class="hero-pane">
                <div class="stack-xl">
                    <div class="stack">
                        <span class="hero-tagline"><span class="dot"></span> Pedido criado</span>
                        <h1 class="text-gradient" style="font-size: clamp(1.7rem, 3vw, 2.6rem);">Pedido {{ $order->order_number }} recebido</h1>
                        <p class="lead">
                            Seu pedido foi criado com sucesso e está aguardando pagamento.
                            Após a confirmação da cobrança, a equipe segue com conferência de arquivo e liberação da produção.
                        </p>
                    </div>

                    <div class="pill-list">
                        @include('partials.status-badge', ['label' => 'Pedido: '.$order->status->label(), 'icon' => $order->status->icon(), 'tone' => $order->status->tone(), 'size' => 'sm'])
                        @include('partials.status-badge', ['label' => 'Pagamento: '.$order->payment_status->label(), 'icon' => $order->payment_status->icon(), 'tone' => $order->payment_status->tone(), 'size' => 'sm'])
                        @include('partials.status-badge', ['label' => 'Produção: '.$order->fulfillment_status->label(), 'icon' => $order->fulfillment_status->icon(), 'tone' => $order->fulfillment_status->tone(), 'size' => 'sm'])
                    </div>

                    <div class="hero-actions">
                        <a class="btn btn-primary" href="{{ route('catalog.index') }}">Voltar ao catálogo</a>
                        @auth
                            <a class="btn btn-secondary" href="{{ route('account.orders.show', $order) }}">Detalhes do pedido</a>
                            <a class="btn btn-secondary" href="{{ route('account.orders.show', $order) }}#chat-pedido">Abrir chat do pedido</a>
                        @else
                            <a class="btn btn-secondary" href="{{ route('home') }}">Home</a>
                        @endauth
                    </div>

                    <div class="timeline-card">
                        <span class="section-kicker">Próximos passos</span>
                        <ul class="timeline-list">
                            <li class="timeline-item">
                                <span class="marker">1</span>
                                <div>
                                    <div class="title">Pagamento</div>
                                    <div class="desc">Confirmação da cobrança para liberação do atendimento.</div>
                                </div>
                            </li>
                            <li class="timeline-item">
                                <span class="marker">2</span>
                                <div>
                                    <div class="title">Arte final</div>
                                    <div class="desc">Recebimento do arquivo e conferência técnica antes de imprimir.</div>
                                </div>
                            </li>
                            <li class="timeline-item">
                                <span class="marker">3</span>
                                <div>
                                    <div class="title">Produção e expedição</div>
                                    <div class="desc">Impressão, acabamento e envio/retirada conforme o pedido.</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="hero-pane">
                <div class="stack-lg">
                    <div class="glass-panel stack">
                        <span class="section-kicker">Resumo financeiro</span>
                        <div class="stack">
                            <div class="summary-row"><span class="muted">Subtotal</span><strong>R$ {{ number_format((float) $order->subtotal, 2, ',', '.') }}</strong></div>
                            <div class="summary-row"><span class="muted">Frete</span><strong>R$ {{ number_format((float) $order->shipping_total, 2, ',', '.') }}</strong></div>
                            <div class="summary-row total"><span>Total</span><span>R$ {{ number_format((float) $order->total, 2, ',', '.') }}</span></div>
                        </div>
                        <div class="small muted">
                            Método: {{ \App\Support\UiStatus::label(optional($order->payments->first()?->method)->value ?? 'nao informado') }}
                        </div>
                        @php
                            $checkoutUrl = data_get($order->payments->first()?->gateway_payload, 'mercadopago.checkout_url');
                            $ticketUrl = data_get($order->payments->first()?->gateway_payload, 'mercadopago.ticket_url') ?: data_get($order->payments->first()?->gateway_payload, 'mercadopago.pix_ticket_url');
                        @endphp
                        @if($order->payment_status->value !== 'paid' && ($checkoutUrl || $ticketUrl))
                            <div>
                                <a href="{{ $checkoutUrl ?: $ticketUrl }}" target="_blank" rel="noreferrer" class="btn btn-primary btn-sm">Abrir cobrança</a>
                            </div>
                        @endif
                    </div>

                    <div class="board-card">
                        <div class="board-title">
                            <strong>Status atual</strong>
                            <span class="tiny muted">pedido</span>
                        </div>
                        <div class="process-rail">
                            <div class="process-step">
                                <span class="num">✓</span>
                                <span class="label">Pedido registrado</span>
                                <span class="eta">{{ optional($order->created_at)->format('d/m H:i') }}</span>
                            </div>
                            <div class="process-step">
                                <span class="num">2</span>
                                <span class="label">Pagamento pendente</span>
                                <span class="eta">aguardando</span>
                            </div>
                            <div class="process-step">
                                <span class="num">3</span>
                                <span class="label">Fila de produção</span>
                                <span class="eta">próximo</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="split" style="margin-bottom: 28px;">
        <section class="card card-pad stack-lg">
            <div class="section-head">
                <div class="copy">
                    <span class="section-kicker">Itens</span>
                    <h2>Composição do pedido</h2>
                    <p class="muted">Itens, variações e observações registradas no momento da compra.</p>
                </div>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Quantidade</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->product_name }}</strong>
                                <div class="tiny muted">{{ $item->variant_name }}</div>
                                @if(!empty($item->configuration))
                                    <div class="tiny muted" style="margin-top:6px;">
                                        @foreach ($item->configuration as $key => $value)
                                            <div>{{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $value }}</div>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td>{{ $item->quantity }}</td>
                            <td><strong>R$ {{ number_format((float) $item->total, 2, ',', '.') }}</strong></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="stack-lg">
            <section class="card card-pad stack-lg floating-sticky">
                <div class="section-head">
                    <div class="copy">
                        <span class="section-kicker">Procedimento</span>
                        <h2 style="font-size:1.35rem;">Próximas etapas do seu pedido</h2>
                    </div>
                </div>
                <ul class="timeline-list">
                    <li class="timeline-item">
                        <span class="marker">1</span>
                        <div>
                            <div class="title">Cobrança</div>
                            <div class="desc">Confirmação do pagamento para iniciar atendimento.</div>
                        </div>
                    </li>
                    <li class="timeline-item">
                        <span class="marker">2</span>
                        <div>
                            <div class="title">Conferência de arte</div>
                            <div class="desc">Validação técnica e ajustes antes da impressão. O chat do pedido pode ser usado para alinhar detalhes.</div>
                        </div>
                    </li>
                    <li class="timeline-item">
                        <span class="marker">3</span>
                        <div>
                            <div class="title">Produção e acabamento</div>
                            <div class="desc">Impressão conforme especificações do pedido.</div>
                        </div>
                    </li>
                    <li class="timeline-item">
                        <span class="marker">4</span>
                        <div>
                            <div class="title">Entrega ou retirada</div>
                            <div class="desc">Expedição e acompanhamento pelo cliente.</div>
                        </div>
                    </li>
                </ul>
            </section>
        </aside>
    </section>
@endsection
