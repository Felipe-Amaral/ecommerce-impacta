@extends('layouts.store')

@section('title', 'Painel • Pedido '.$order->order_number)
@section('meta_description', 'Gestão de cobrança, produção e arte final do pedido.')

@section('content')
    <section class="hero hero-studio reveal-up" style="margin-bottom: 18px;">
        <div class="hero-studio-grid">
            <div class="hero-pane">
                <div class="stack-xl">
                    <div class="pill-list">
                        <span class="badge badge-brand">Painel da gráfica</span>
                        <span class="pill">Pedido {{ $order->order_number }}</span>
                    </div>
                    <div class="stack">
                        <h1 class="text-gradient" style="font-size: clamp(1.75rem, 3vw, 2.6rem);">Cobrança, pré-impressão e produção</h1>
                        <p class="lead">
                            Atualize o status do pedido, revise os arquivos enviados e registre observações para a equipe.
                        </p>
                    </div>
                    <div class="hero-actions">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Voltar para pedidos</a>
                        <a href="#chat-pedido" class="btn btn-secondary">Chat do pedido</a>
                        <a href="{{ route('catalog.index') }}" class="btn btn-primary">Abrir catálogo</a>
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
                        <strong>{{ $order->customer_name }}</strong>
                        <span>Cliente</span>
                    </div>
                </div>
                <div class="board-card stack" style="margin-top:12px;">
                    <div class="board-title">
                        <strong>Status atual</strong>
                        <span class="tiny muted">workflow</span>
                    </div>
                    <div class="process-rail">
                        <div class="process-step">
                            <span class="num">P</span>
                            <span class="label">{{ $order->payment_status->label() }}</span>
                            <span class="eta">pagamento</span>
                        </div>
                        <div class="process-step">
                            <span class="num">O</span>
                            <span class="label">{{ $order->status->label() }}</span>
                            <span class="eta">pedido</span>
                        </div>
                        <div class="process-step">
                            <span class="num">F</span>
                            <span class="label">{{ $order->fulfillment_status->label() }}</span>
                            <span class="eta">produção</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="split" style="margin-bottom: 24px;">
        <section class="stack-lg">
            <section class="card card-pad stack-lg">
                <div class="section-head">
                    <div class="copy">
                        <span class="section-kicker">Workflow</span>
                        <h2>Atualizar status do pedido</h2>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.orders.workflow.update', $order) }}" class="stack">
                    @csrf
                    @method('PATCH')
                    <div class="form-grid-3">
                        <div class="field">
                            <label for="admin_order_status">Status do pedido</label>
                            <select id="admin_order_status" name="status" class="select">
                                @foreach(\App\Enums\OrderStatus::cases() as $status)
                                    <option value="{{ $status->value }}" @selected($order->status->value === $status->value)>{{ $status->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="admin_payment_status">Pagamento</label>
                            <select id="admin_payment_status" name="payment_status" class="select">
                                @foreach(\App\Enums\PaymentStatus::cases() as $status)
                                    <option value="{{ $status->value }}" @selected($order->payment_status->value === $status->value)>{{ $status->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="admin_fulfillment_status">Produção</label>
                            <select id="admin_fulfillment_status" name="fulfillment_status" class="select">
                                @foreach(\App\Enums\FulfillmentStatus::cases() as $status)
                                    <option value="{{ $status->value }}" @selected($order->fulfillment_status->value === $status->value)>{{ $status->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="field">
                        <label for="admin_status_message">Observação interna (opcional)</label>
                        <input id="admin_status_message" class="input" type="text" name="message" placeholder="Ex.: pagamento confirmado, pedido liberado para pré-impressão" />
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Salvar workflow</button>
                    </div>
                </form>
            </section>

            @foreach($order->items as $item)
                <article class="card card-pad stack-lg">
                    <div class="link-row">
                        <div class="stack" style="gap:4px;">
                            <h3 style="margin:0;">{{ $item->product_name }}</h3>
                            <p class="small muted">{{ $item->variant_name }} • {{ $item->quantity }} un • {{ $item->sku }}</p>
                        </div>
                        @include('partials.status-badge', ['status' => (string) $item->production_status, 'size' => 'sm'])
                    </div>

                    @if(!empty($item->configuration) || $item->artwork_notes)
                        <div class="glass-panel stack">
                            @if(!empty($item->configuration))
                                <div class="small muted">
                                    @foreach($item->configuration as $key => $value)
                                        <div>{{ ucfirst(str_replace('_', ' ', (string) $key)) }}: {{ $value }}</div>
                                    @endforeach
                                </div>
                            @endif
                            @if($item->artwork_notes)
                                <div class="small muted"><strong>Obs. cliente no carrinho:</strong> {{ $item->artwork_notes }}</div>
                            @endif
                        </div>
                    @endif

                    <section class="stack">
                        <div class="link-row">
                            <strong>Arquivos de arte</strong>
                            <span class="tiny muted">{{ $item->artworkFiles->count() }} arquivo(s)</span>
                        </div>

                        @if($item->artworkFiles->isEmpty())
                            <p class="small muted">Cliente ainda não enviou arquivo para este item.</p>
                        @else
                            @foreach($item->artworkFiles as $file)
                                @php
                                    $customerNotes = data_get($file->metadata, 'customer_notes');
                                    $statusValue = $file->status->value;
                                @endphp
                                <div class="glass-panel stack">
                                    <div class="link-row">
                                        <div class="stack" style="gap:3px;">
                                            <strong style="font-size:.9rem;">{{ $file->original_name }}</strong>
                                            <span class="tiny muted">
                                                {{ $file->created_at->format('d/m/Y H:i') }}
                                                @if($file->uploadedBy)
                                                    • enviado por {{ $file->uploadedBy->name }}
                                                @endif
                                            </span>
                                        </div>
                                        <a href="{{ route('artwork-files.download', $file) }}" class="btn btn-secondary btn-sm">Baixar</a>
                                    </div>

                                    @if(!empty($file->checklist))
                                        <div class="tiny muted">
                                            Checklist cliente:
                                            {{ !empty($file->checklist['cmyk']) ? 'CMYK' : 'CMYK não confirmado' }} •
                                            {{ !empty($file->checklist['bleed']) ? 'Sangria ok' : 'Sangria não confirmada' }} •
                                            {{ !empty($file->checklist['outlined_fonts']) ? 'Fontes em curva' : 'Fontes não confirmadas' }} •
                                            {{ !empty($file->checklist['high_resolution_images']) ? 'Boa resolução' : 'Resolução não confirmada' }}
                                        </div>
                                    @endif

                                    @if($customerNotes)
                                        <div class="small muted"><strong>Obs. cliente:</strong> {{ $customerNotes }}</div>
                                    @endif

                                    <form method="POST" action="{{ route('admin.artwork.review', $file) }}" class="stack">
                                        @csrf
                                        @method('PATCH')
                                        <div class="form-grid">
                                            <div class="field">
                                                <label for="artwork_status_{{ $file->id }}">Revisão</label>
                                                <select id="artwork_status_{{ $file->id }}" name="status" class="select">
                                                    @foreach([\App\Enums\ArtworkFileStatus::UnderReview, \App\Enums\ArtworkFileStatus::Approved, \App\Enums\ArtworkFileStatus::NeedsAdjustment, \App\Enums\ArtworkFileStatus::Rejected] as $status)
                                                        <option value="{{ $status->value }}" @selected($statusValue === $status->value)>{{ $status->label() }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="field">
                                                <label for="artwork_review_notes_{{ $file->id }}">Notas da revisão</label>
                                                <input id="artwork_review_notes_{{ $file->id }}" class="input" type="text" name="review_notes" value="{{ $file->review_notes }}" placeholder="Ex.: adicionar sangria de 3mm e converter fontes em curva" />
                                            </div>
                                        </div>
                                        <div>
                                            <button type="submit" class="btn btn-primary btn-sm">Salvar revisão</button>
                                        </div>
                                    </form>
                                </div>
                            @endforeach
                        @endif
                    </section>
                </article>
            @endforeach
        </section>

        <aside class="stack-lg">
            @include('orders.partials.chat-widget', [
                'order' => $order,
                'viewer' => 'admin',
                'widgetId' => 'chat-pedido',
                'title' => 'Chat com o cliente',
                'subtitle' => 'Use este canal para alinhar arte, aprovação, cobrança, prazo e entrega deste pedido.',
            ])

            <section class="card card-pad stack-lg floating-sticky">
                <div class="section-head">
                    <div class="copy">
                        <span class="section-kicker">Cliente</span>
                        <h2 style="font-size:1.25rem;">Dados e entrega</h2>
                    </div>
                </div>
                <div class="glass-panel stack">
                    <div class="small muted">
                        <strong>{{ $order->customer_name }}</strong><br>
                        {{ $order->customer_email }}<br>
                        {{ $order->customer_phone ?: 'Telefone não informado' }}
                    </div>
                </div>
                <div class="glass-panel stack">
                    <strong style="font-size:.9rem;">Entrega / retirada</strong>
                    <div class="small muted">
                        {{ $order->shipping_method_label ?: 'Entrega' }}<br>
                        @if($order->shipping_delivery_days)
                            Prazo estimado: {{ $order->shipping_delivery_days }} dia(s) útil(eis)<br>
                        @endif
                        Custo: R$ {{ number_format((float) $order->shipping_total, 2, ',', '.') }}
                    </div>
                    @php($shippingAddress = (array) $order->shipping_address)
                    @if($order->shipping_method_code !== 'pickup_counter')
                        <div class="small muted">
                            {{ data_get($shippingAddress, 'recipient_name') }}<br>
                            {{ data_get($shippingAddress, 'street') }}, {{ data_get($shippingAddress, 'number') }}<br>
                            {{ data_get($shippingAddress, 'district') }} - {{ data_get($shippingAddress, 'city') }}/{{ data_get($shippingAddress, 'state') }}<br>
                            CEP {{ data_get($shippingAddress, 'zipcode') }}
                        </div>
                    @endif
                </div>
            </section>

            <section class="card card-pad stack">
                <div class="section-head">
                    <div class="copy">
                        <span class="section-kicker">Histórico</span>
                        <h3>Eventos recentes</h3>
                    </div>
                </div>
                @if($order->statusHistory->isEmpty())
                    <p class="small muted">Sem eventos registrados.</p>
                @else
                    <ul class="timeline-list">
                        @foreach($order->statusHistory as $event)
                            <li class="timeline-item">
                                <span class="marker">{{ $loop->iteration }}</span>
                                <div>
                                    <div class="title">{{ $event->message ?: 'Atualização' }}</div>
                                    <div class="desc">{{ $event->created_at?->format('d/m/Y H:i') }}</div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </aside>
    </section>
@endsection
