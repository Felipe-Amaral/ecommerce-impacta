@extends('layouts.store')

@section('title', 'Pedido '.$order->order_number.' | Área do Cliente')
@section('meta_description', 'Detalhes do pedido, upload de arte final e acompanhamento de produção.')

@section('content')
    @php
        $payment = $order->payments->first();
        $shippingLabel = $order->shipping_method_label ?: 'Entrega';
    @endphp

    <section class="hero hero-studio reveal-up" style="margin-bottom: 18px;">
        <div class="hero-studio-grid">
            <div class="hero-pane">
                <div class="stack-xl">
                    <div class="pill-list">
                        <span class="badge">Pedido {{ $order->order_number }}</span>
                        @include('partials.status-badge', ['label' => 'Pagamento: '.$order->payment_status->label(), 'icon' => $order->payment_status->icon(), 'tone' => $order->payment_status->tone(), 'size' => 'sm'])
                        @include('partials.status-badge', ['label' => 'Produção: '.$order->fulfillment_status->label(), 'icon' => $order->fulfillment_status->icon(), 'tone' => $order->fulfillment_status->tone(), 'size' => 'sm'])
                    </div>

                    <div class="stack">
                        <h1 class="text-gradient" style="font-size: clamp(1.75rem, 3vw, 2.6rem);">Envio de arte e acompanhamento do pedido</h1>
                        <p class="lead">
                            Envie os arquivos por item, informe observações técnicas e acompanhe a revisão da pré-impressão.
                        </p>
                    </div>

                    <div class="hero-actions">
                        <a href="{{ route('account.orders.index') }}" class="btn btn-secondary">Voltar para pedidos</a>
                        <a href="#chat-pedido" class="btn btn-secondary">Abrir conversa</a>
                        <a href="{{ route('catalog.index') }}" class="btn btn-primary">Novo pedido</a>
                    </div>
                </div>
            </div>

            <div class="hero-pane">
                <div class="metric-grid">
                    <div class="metric-card">
                        <strong>{{ $order->items->count() }}</strong>
                        <span>Itens</span>
                    </div>
                    <div class="metric-card">
                        <strong>R$ {{ number_format((float) $order->total, 2, ',', '.') }}</strong>
                        <span>Total</span>
                    </div>
                    <div class="metric-card">
                        <strong>{{ optional($order->placed_at)->format('d/m') ?: $order->created_at->format('d/m') }}</strong>
                        <span>Data do pedido</span>
                    </div>
                </div>

                <div class="board-card" style="margin-top:12px;">
                    <div class="board-title">
                        <strong>Entrega / retirada</strong>
                        <span class="tiny muted">{{ $order->shipping_provider ?: 'pedido' }}</span>
                    </div>
                    <div class="stack small">
                        <div><strong>{{ $shippingLabel }}</strong></div>
                        <div class="muted">
                            Custo: R$ {{ number_format((float) $order->shipping_total, 2, ',', '.') }}
                            @if($order->shipping_delivery_days)
                                • Prazo estimado: {{ $order->shipping_delivery_days }} dia(s) útil(eis)
                            @endif
                        </div>
                        @if($order->shipping_method_code === 'pickup_counter')
                            <div class="ribbon-note">Retirada no balcão após liberação da produção.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="split" style="margin-bottom: 28px;">
        <section class="stack-lg">
            @foreach($order->items as $item)
                <article class="card card-pad stack-lg">
                    <div class="link-row">
                        <div class="stack" style="gap:4px;">
                            <h2 style="font-size:1.15rem;">{{ $item->product_name }}</h2>
                            <p class="small muted">{{ $item->variant_name }} • {{ $item->quantity }} un • R$ {{ number_format((float) $item->total, 2, ',', '.') }}</p>
                        </div>
                        @include('partials.status-badge', ['status' => (string) $item->production_status, 'size' => 'sm'])
                    </div>

                    @if(!empty($item->configuration) || $item->artwork_notes)
                        <div class="grid grid-2">
                            <div class="glass-panel stack">
                                <strong style="font-size:.9rem;">Configuração</strong>
                                @if(!empty($item->configuration))
                                    <ul class="clean-list small muted">
                                        @foreach($item->configuration as $key => $value)
                                            <li>{{ ucfirst(str_replace('_', ' ', (string) $key)) }}: {{ $value }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="small muted">Sem observações de configuração.</p>
                                @endif
                            </div>
                            <div class="glass-panel stack">
                                <strong style="font-size:.9rem;">Observações de arte</strong>
                                <p class="small muted">{{ $item->artwork_notes ?: 'Sem observações informadas no carrinho.' }}</p>
                            </div>
                        </div>
                    @endif

                    <section class="card card-pad stack">
                        <div class="section-head">
                            <div class="copy">
                                <span class="section-kicker">Arte final</span>
                                <h3>Enviar arquivo para este item</h3>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('account.orders.items.artwork.store', [$order, $item]) }}" enctype="multipart/form-data" class="stack">
                            @csrf
                            <div class="field">
                                <label for="file_item_{{ $item->id }}">Arquivo (PDF, imagem ou ZIP)</label>
                                <input id="file_item_{{ $item->id }}" class="input" type="file" name="file" required />
                            </div>

                            <div class="field">
                                <label for="customer_notes_item_{{ $item->id }}">Observações da arte (opcional)</label>
                                <textarea id="customer_notes_item_{{ $item->id }}" class="textarea" name="customer_notes" placeholder="Ex.: frente e verso, aplicar verniz localizado no logo, referência de versão">{{ old('customer_notes') }}</textarea>
                            </div>

                            <div class="grid grid-2">
                                <label class="radio-card" for="checklist_{{ $item->id }}_cmyk">
                                    <input id="checklist_{{ $item->id }}_cmyk" type="checkbox" name="checklist[cmyk]" value="1" />
                                    <span>Arquivo em CMYK</span>
                                </label>
                                <label class="radio-card" for="checklist_{{ $item->id }}_bleed">
                                    <input id="checklist_{{ $item->id }}_bleed" type="checkbox" name="checklist[bleed]" value="1" />
                                    <span>Sangria / margem conferida</span>
                                </label>
                                <label class="radio-card" for="checklist_{{ $item->id }}_fonts">
                                    <input id="checklist_{{ $item->id }}_fonts" type="checkbox" name="checklist[outlined_fonts]" value="1" />
                                    <span>Fontes em curva</span>
                                </label>
                                <label class="radio-card" for="checklist_{{ $item->id }}_resolution">
                                    <input id="checklist_{{ $item->id }}_resolution" type="checkbox" name="checklist[high_resolution_images]" value="1" />
                                    <span>Imagens em boa resolução</span>
                                </label>
                            </div>

                            <div class="link-row">
                                <span class="tiny muted">A equipe fará conferência técnica antes da impressão.</span>
                                <button type="submit" class="btn btn-primary btn-sm">Enviar arte</button>
                            </div>
                        </form>
                    </section>

                    <section class="card card-pad stack">
                        <div class="link-row">
                            <h3 style="margin:0;">Arquivos enviados</h3>
                            <span class="tiny muted">{{ $item->artworkFiles->count() }} arquivo(s)</span>
                        </div>

                        @if($item->artworkFiles->isEmpty())
                            <p class="small muted">Nenhum arquivo enviado ainda para este item.</p>
                        @else
                            <div class="stack">
                                @foreach($item->artworkFiles as $file)
                                    @php
                                        $statusValue = $file->status->value;
                                        $customerNotes = data_get($file->metadata, 'customer_notes');
                                    @endphp
                                    <div class="glass-panel stack">
                                        <div class="link-row">
                                            <div class="stack" style="gap:3px;">
                                                <strong style="font-size:.9rem;">{{ $file->original_name }}</strong>
                                                <span class="tiny muted">
                                                    {{ $file->created_at->format('d/m/Y H:i') }}
                                                    @if($file->size_bytes)
                                                        • {{ number_format($file->size_bytes / 1024, 0, ',', '.') }} KB
                                                    @endif
                                                </span>
                                            </div>
                                            @include('partials.status-badge', ['status' => $statusValue, 'size' => 'sm'])
                                        </div>

                                        @if(!empty($file->checklist))
                                            <div class="tiny muted">
                                                Checklist:
                                                {{ !empty($file->checklist['cmyk']) ? 'CMYK' : 'Sem CMYK' }} •
                                                {{ !empty($file->checklist['bleed']) ? 'Com sangria' : 'Sem sangria' }} •
                                                {{ !empty($file->checklist['outlined_fonts']) ? 'Fontes em curva' : 'Fontes não confirmadas' }} •
                                                {{ !empty($file->checklist['high_resolution_images']) ? 'Boa resolução' : 'Resolução não confirmada' }}
                                            </div>
                                        @endif

                                        @if($customerNotes)
                                            <div class="small muted"><strong>Obs. cliente:</strong> {{ $customerNotes }}</div>
                                        @endif

                                        @if($file->review_notes)
                                            <div class="small muted"><strong>Revisão da gráfica:</strong> {{ $file->review_notes }}</div>
                                        @endif

                                        <div>
                                            <a href="{{ route('artwork-files.download', $file) }}" class="btn btn-secondary btn-sm">Baixar arquivo</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </section>
                </article>
            @endforeach
        </section>

        <aside class="stack-lg">
            @include('orders.partials.chat-widget', [
                'order' => $order,
                'viewer' => 'client',
                'widgetId' => 'chat-pedido',
                'title' => 'Fale com a gráfica sobre este pedido',
                'subtitle' => 'Alinhe arte final, prazo, acabamento e qualquer detalhe do pedido diretamente por aqui.',
            ])

            <section class="card card-pad stack-lg floating-sticky">
                <div class="section-head">
                    <div class="copy">
                        <span class="section-kicker">Resumo</span>
                        <h2 style="font-size:1.3rem;">Pedido</h2>
                    </div>
                </div>
                <div class="stack">
                    <div class="summary-row"><span class="muted">Subtotal</span><strong>R$ {{ number_format((float) $order->subtotal, 2, ',', '.') }}</strong></div>
                    <div class="summary-row"><span class="muted">Entrega / retirada</span><strong>R$ {{ number_format((float) $order->shipping_total, 2, ',', '.') }}</strong></div>
                    <div class="summary-row total"><span>Total</span><span>R$ {{ number_format((float) $order->total, 2, ',', '.') }}</span></div>
                </div>

                <div class="glass-panel stack">
                    <strong style="font-size:.9rem;">Cobrança</strong>
                    <div class="small muted">
                        Status: {{ $order->payment_status->label() }}<br>
                        Método: {{ \App\Support\UiStatus::label(optional($payment?->method)->value ?? 'nao informado') }}
                    </div>
                    @php
                        $checkoutUrl = data_get($payment?->gateway_payload, 'mercadopago.checkout_url');
                        $ticketUrl = data_get($payment?->gateway_payload, 'mercadopago.ticket_url') ?: data_get($payment?->gateway_payload, 'mercadopago.pix_ticket_url');
                    @endphp
                    @if($checkoutUrl)
                        <a href="{{ $checkoutUrl }}" target="_blank" rel="noreferrer" class="btn btn-primary btn-sm">Pagar agora</a>
                    @elseif($ticketUrl)
                        <a href="{{ $ticketUrl }}" target="_blank" rel="noreferrer" class="btn btn-primary btn-sm">Abrir cobrança</a>
                    @endif
                    <span class="tiny muted">Se precisar de ajuste na cobrança ou prazo, envie uma mensagem no chat acima.</span>
                </div>
            </section>

            <section class="card card-pad stack">
                <div class="section-head">
                    <div class="copy">
                        <span class="section-kicker">Histórico</span>
                        <h3>Atualizações do pedido</h3>
                    </div>
                </div>
                @if($order->statusHistory->isEmpty())
                    <p class="small muted">Sem atualizações registradas ainda.</p>
                @else
                    <ul class="timeline-list">
                        @foreach($order->statusHistory as $history)
                            <li class="timeline-item">
                                <span class="marker">{{ $loop->iteration }}</span>
                                <div>
                                    <div class="title">{{ $history->message ?: 'Atualização de status' }}</div>
                                    <div class="desc">
                                        {{ $history->created_at?->format('d/m/Y H:i') }}
                                        @if($history->to_status)
                                            • Status: {{ \App\Support\UiStatus::label($history->to_status) }}
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </aside>
    </section>
@endsection
