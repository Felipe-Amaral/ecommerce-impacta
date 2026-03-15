@extends('layouts.store')

@section('title', 'Carrinho | Gráfica Uriah Criativa')

@section('content')
    <section class="hero reveal-up" style="margin-bottom: 18px;">
        <div class="split">
            <div class="stack-lg">
                <p class="eyebrow">Carrinho</p>
                <h1 style="font-size: clamp(1.6rem, 3vw, 2.4rem);">Revise seu pedido</h1>
                <p class="lead">Ajuste quantidades, valide as variações e siga para o checkout.</p>
                <div class="checkout-progress">
                    <span class="step-chip"><span class="n">1</span> Carrinho</span>
                    <span class="step-chip"><span class="n">2</span> Checkout</span>
                    <span class="step-chip"><span class="n">3</span> Pedido</span>
                </div>
            </div>
            <div style="display:flex; justify-content:flex-end; align-items:flex-start;">
                <a class="btn btn-secondary" href="{{ route('catalog.index') }}">Continuar comprando</a>
            </div>
        </div>
    </section>

    @if (empty($cart['items']))
        <section class="card card-pad stack" style="margin-bottom: 28px;">
            <h2>Seu carrinho está vazio</h2>
            <p class="muted">Adicione produtos do catálogo para iniciar um pedido.</p>
            <div>
                <a href="{{ route('catalog.index') }}" class="btn btn-primary">Ir para o catálogo</a>
            </div>
        </section>
    @else
        <section class="split" style="margin-bottom: 28px;">
            <div class="card card-pad stack-lg">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Detalhes</th>
                                <th>Quantidade</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($cart['items'] as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item['product_name'] }}</strong>
                                    <div class="tiny muted">{{ $item['variant_name'] }}</div>
                                    <div class="tiny muted mono">{{ $item['sku'] }}</div>
                                </td>
                                <td>
                                    @if(!empty($item['configuration']))
                                        <div class="stack" style="gap:6px;">
                                            @foreach ($item['configuration'] as $key => $value)
                                                <div class="tiny">
                                                    <span class="muted">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                    <strong>{{ $value }}</strong>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if(!empty($item['artwork_notes']))
                                        <div class="tiny muted" style="margin-top:8px;">
                                            <strong>Arte final:</strong> {{ $item['artwork_notes'] }}
                                        </div>
                                    @endif

                                    @if(!empty($item['artwork_upload']['original_name']))
                                        <div class="tiny muted" style="margin-top:8px;">
                                            <strong>Arquivo enviado:</strong> {{ $item['artwork_upload']['original_name'] }}
                                        </div>
                                    @endif

                                    <div class="tiny muted" style="margin-top:8px;">
                                        Unitário: R$ {{ number_format((float) $item['unit_price'], 2, ',', '.') }}
                                    </div>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('cart.items.update', $item['id']) }}" class="stack" style="gap:8px; min-width: 120px;">
                                        @csrf
                                        @method('PATCH')
                                        <input class="input" type="number" name="quantity" min="1" value="{{ $item['quantity'] }}" />
                                        <button class="btn btn-secondary" type="submit">Atualizar</button>
                                    </form>

                                    <form method="POST" action="{{ route('cart.items.destroy', $item['id']) }}" style="margin-top:8px;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-link" type="submit">Remover</button>
                                    </form>
                                </td>
                                <td>
                                    <strong>R$ {{ number_format((float) $item['line_total'], 2, ',', '.') }}</strong>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <aside class="card card-pad stack-lg floating-sticky">
                <h2>Resumo</h2>

                <div class="stack">
                    <div class="summary-row"><span class="muted">Subtotal</span><strong>R$ {{ number_format((float) $cart['subtotal'], 2, ',', '.') }}</strong></div>
                    <div class="summary-row"><span class="muted">Descontos</span><strong>- R$ {{ number_format((float) $cart['discount_total'], 2, ',', '.') }}</strong></div>
                    <div class="summary-row"><span class="muted">Frete estimado</span><strong>R$ {{ number_format((float) $cart['shipping_total'], 2, ',', '.') }}</strong></div>
                    <div class="summary-row total"><span>Total</span><span>R$ {{ number_format((float) $cart['total'], 2, ',', '.') }}</span></div>
                </div>

                <div class="small muted">
                    Frete exibido como estimativa. A forma de entrega/retirada e o valor final podem ser confirmados no atendimento.
                </div>

                <div class="ribbon-note">Próximo passo: informe cobrança, entrega e forma de pagamento no checkout.</div>

                <a href="{{ route('checkout.index') }}" class="btn btn-primary">Ir para checkout</a>

                <form method="POST" action="{{ route('cart.clear') }}">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-secondary" type="submit" onclick="return confirm('Deseja limpar todo o carrinho?');">
                        Limpar carrinho
                    </button>
                </form>
            </aside>
        </section>
    @endif
@endsection
