@extends('layouts.store')

@section('title', 'Checkout | Gráfica Uriah Criativa')

@section('content')
    @php
        $oldShippingOption = (array) old('shipping_option', []);
    @endphp

    <section class="hero reveal-up" style="margin-bottom: 18px;">
        <div class="split">
            <div class="stack-lg">
                <p class="eyebrow">Checkout</p>
                <h1 style="font-size: clamp(1.6rem, 3vw, 2.4rem);">Fechar pedido</h1>
                <p class="lead">Preencha os dados de cobrança e entrega para concluir o pedido com rapidez e segurança.</p>
                <div class="checkout-progress">
                    <span class="step-chip"><span class="n">1</span> Dados</span>
                    <span class="step-chip"><span class="n">2</span> Endereço</span>
                    <span class="step-chip"><span class="n">3</span> Pagamento</span>
                    <span class="step-chip"><span class="n">4</span> Pedido</span>
                </div>
            </div>
            <div class="card card-pad stack" style="box-shadow:none;">
                <span class="small muted">Itens no pedido</span>
                <strong>{{ $cart['count'] }} item(ns)</strong>
                <span class="price" id="checkout-header-total">R$ {{ number_format((float) $cart['total'], 2, ',', '.') }}</span>
            </div>
        </div>
    </section>

    <form method="POST" action="{{ route('checkout.store') }}" class="split" style="margin-bottom: 28px;">
        @csrf
        <input type="hidden" name="shipping_option[code]" id="shipping_option_code" value="{{ (string) ($oldShippingOption['code'] ?? '') }}">
        <input type="hidden" name="shipping_option[label]" id="shipping_option_label" value="{{ (string) ($oldShippingOption['label'] ?? '') }}">
        <input type="hidden" name="shipping_option[provider]" id="shipping_option_provider" value="{{ (string) ($oldShippingOption['provider'] ?? '') }}">
        <input type="hidden" name="shipping_option[cost]" id="shipping_option_cost" value="{{ (string) ($oldShippingOption['cost'] ?? $cart['shipping_total']) }}">
        <input type="hidden" name="shipping_option[delivery_days]" id="shipping_option_delivery_days" value="{{ (string) ($oldShippingOption['delivery_days'] ?? '') }}">
        <input type="hidden" name="shipping_option[is_pickup]" id="shipping_option_is_pickup" value="{{ (string) ((int) (($oldShippingOption['is_pickup'] ?? false) ? 1 : 0)) }}">

        <div class="stack-lg">
            <section class="card card-pad stack-lg">
                <div class="form-section-head">
                    <div>
                        <h2>Contato</h2>
                        <p>Quem receberá atualizações do pedido e do atendimento.</p>
                    </div>
                </div>
                <div class="form-grid">
                    <div class="field full">
                        <label for="customer_name">Nome / Razão social</label>
                        <input id="customer_name" class="input" name="customer[name]" autocomplete="name" value="{{ old('customer.name') }}" />
                    </div>
                    <div class="field">
                        <label for="customer_email">E-mail</label>
                        <input id="customer_email" class="input" type="email" name="customer[email]" autocomplete="email" value="{{ old('customer.email') }}" />
                    </div>
                    <div class="field">
                        <label for="customer_phone">Telefone / WhatsApp</label>
                        <input id="customer_phone" class="input" name="customer[phone]" autocomplete="tel" inputmode="tel" data-mask="phone-br" value="{{ old('customer.phone') }}" />
                    </div>
                    <div class="field">
                        <label for="customer_document">CPF/CNPJ (opcional)</label>
                        <input id="customer_document" class="input" name="customer[document]" inputmode="numeric" data-mask="document-br" value="{{ old('customer.document') }}" />
                    </div>
                    <div class="field full">
                        <label for="notes">Observações do pedido</label>
                        <textarea id="notes" class="textarea" name="notes" placeholder="Ex.: retirada em balcão, urgência, referência da arte ou observações de acabamento">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </section>

            <section class="card card-pad stack-lg" id="shipping-method-section">
                <div class="form-section-head">
                    <div>
                        <h2>Entrega ou retirada</h2>
                        <p>Calcule as opções por CEP ou selecione retirada no balcão.</p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-secondary btn-sm" id="calculate-shipping-btn">Calcular frete</button>
                    </div>
                </div>

                <div class="stack">
                    <div class="ribbon-note" id="shipping-options-status">
                        Informe o CEP de entrega para ver Correios, transportadora e retirada no balcão.
                    </div>
                    <div class="radio-list" id="shipping-options-list" aria-live="polite"></div>
                    <div class="glass-panel small" id="shipping-pickup-info" hidden>
                        <strong>Retirada no balcão</strong>
                        <div class="muted">Disponível após confirmação do pedido e liberação da produção.</div>
                    </div>
                </div>
            </section>

            <section class="card card-pad stack-lg">
                <div class="form-section-head">
                    <div>
                        <h2>Endereço de cobrança</h2>
                        <p>Dados fiscais e de faturamento do pedido.</p>
                    </div>
                </div>
                <div class="form-grid">
                    <div class="field full">
                        <label for="billing_recipient_name">Responsável</label>
                        <input id="billing_recipient_name" class="input" name="billing[recipient_name]" autocomplete="name" value="{{ old('billing.recipient_name') }}" />
                    </div>
                    <div class="field">
                        <label for="billing_phone">Telefone</label>
                        <input id="billing_phone" class="input" name="billing[phone]" autocomplete="tel" inputmode="tel" data-mask="phone-br" value="{{ old('billing.phone') }}" />
                    </div>
                    <div class="field">
                        <label for="billing_zipcode">CEP</label>
                        <input id="billing_zipcode" class="input" name="billing[zipcode]" autocomplete="postal-code" inputmode="numeric" data-mask="cep-br" data-cep-scope="billing" value="{{ old('billing.zipcode') }}" />
                        <div class="tiny muted" id="billing_zipcode_status">Informe o CEP para preencher endereço automaticamente.</div>
                    </div>
                    <div class="field full">
                        <label for="billing_street">Rua / Avenida</label>
                        <input id="billing_street" class="input" name="billing[street]" autocomplete="address-line1" value="{{ old('billing.street') }}" />
                    </div>
                    <div class="field">
                        <label for="billing_number">Número</label>
                        <input id="billing_number" class="input" name="billing[number]" autocomplete="address-line2" value="{{ old('billing.number') }}" />
                    </div>
                    <div class="field">
                        <label for="billing_complement">Complemento</label>
                        <input id="billing_complement" class="input" name="billing[complement]" value="{{ old('billing.complement') }}" />
                    </div>
                    <div class="field">
                        <label for="billing_district">Bairro</label>
                        <input id="billing_district" class="input" name="billing[district]" value="{{ old('billing.district') }}" />
                    </div>
                    <div class="field">
                        <label for="billing_city">Cidade</label>
                        <input id="billing_city" class="input" name="billing[city]" autocomplete="address-level2" value="{{ old('billing.city') }}" />
                    </div>
                    <div class="field">
                        <label for="billing_state">UF</label>
                        <input id="billing_state" class="input" maxlength="4" style="text-transform: uppercase;" data-force-uppercase name="billing[state]" value="{{ old('billing.state') }}" />
                    </div>
                    <div class="field">
                        <label for="billing_country">País</label>
                        <input id="billing_country" class="input" maxlength="2" style="text-transform: uppercase;" data-force-uppercase name="billing[country]" value="{{ old('billing.country', 'BR') }}" />
                    </div>
                </div>
            </section>

            <section class="card card-pad stack-lg">
                <div class="stack">
                    <div class="form-section-head">
                        <div>
                            <h2>Endereço de entrega</h2>
                            <p>Destino da produção após aprovação e finalização.</p>
                        </div>
                    </div>
                    <label class="radio-card" for="same_as_billing" style="max-width: 360px;">
                        <input id="same_as_billing" type="checkbox" name="same_as_billing" value="1" @checked(old('same_as_billing')) />
                        <span>Usar o mesmo endereço da cobrança</span>
                    </label>
                </div>

                <div class="form-grid">
                    <div class="field full">
                        <label for="shipping_recipient_name">Responsável</label>
                        <input id="shipping_recipient_name" class="input" name="shipping[recipient_name]" autocomplete="name" value="{{ old('shipping.recipient_name') }}" />
                    </div>
                    <div class="field">
                        <label for="shipping_phone">Telefone</label>
                        <input id="shipping_phone" class="input" name="shipping[phone]" autocomplete="tel" inputmode="tel" data-mask="phone-br" value="{{ old('shipping.phone') }}" />
                    </div>
                    <div class="field">
                        <label for="shipping_zipcode">CEP</label>
                        <input id="shipping_zipcode" class="input" name="shipping[zipcode]" autocomplete="postal-code" inputmode="numeric" data-mask="cep-br" data-cep-scope="shipping" value="{{ old('shipping.zipcode') }}" />
                        <div class="tiny muted" id="shipping_zipcode_status">Use o CEP de entrega para sugerir rua, bairro e cidade.</div>
                    </div>
                    <div class="field full">
                        <label for="shipping_street">Rua / Avenida</label>
                        <input id="shipping_street" class="input" name="shipping[street]" autocomplete="address-line1" value="{{ old('shipping.street') }}" />
                    </div>
                    <div class="field">
                        <label for="shipping_number">Número</label>
                        <input id="shipping_number" class="input" name="shipping[number]" autocomplete="address-line2" value="{{ old('shipping.number') }}" />
                    </div>
                    <div class="field">
                        <label for="shipping_complement">Complemento</label>
                        <input id="shipping_complement" class="input" name="shipping[complement]" value="{{ old('shipping.complement') }}" />
                    </div>
                    <div class="field">
                        <label for="shipping_district">Bairro</label>
                        <input id="shipping_district" class="input" name="shipping[district]" value="{{ old('shipping.district') }}" />
                    </div>
                    <div class="field">
                        <label for="shipping_city">Cidade</label>
                        <input id="shipping_city" class="input" name="shipping[city]" autocomplete="address-level2" value="{{ old('shipping.city') }}" />
                    </div>
                    <div class="field">
                        <label for="shipping_state">UF</label>
                        <input id="shipping_state" class="input" maxlength="4" style="text-transform: uppercase;" data-force-uppercase name="shipping[state]" value="{{ old('shipping.state') }}" />
                    </div>
                    <div class="field">
                        <label for="shipping_country">País</label>
                        <input id="shipping_country" class="input" maxlength="2" style="text-transform: uppercase;" data-force-uppercase name="shipping[country]" value="{{ old('shipping.country', 'BR') }}" />
                    </div>
                </div>
            </section>

            <section class="card card-pad stack-lg">
                <div class="form-section-head">
                    <div>
                        <h2>Pagamento</h2>
                        <p>Escolha a forma de pagamento para registro da cobrança e liberação do atendimento.</p>
                    </div>
                </div>
                <div class="radio-list">
                    @foreach ($paymentOptions as $value => $label)
                        <label class="radio-card" for="payment_method_{{ $value }}">
                            <input id="payment_method_{{ $value }}" type="radio" name="payment[method]" value="{{ $value }}" @checked(old('payment.method', 'pix') === $value) />
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="field" style="max-width: 220px;">
                    <label for="payment_installments">Parcelas (cartão)</label>
                    <input id="payment_installments" class="input" type="number" min="1" max="12" name="payment[installments]" value="{{ old('payment.installments', 1) }}" />
                </div>

                <p class="small muted">Após criar o pedido, a cobrança fica registrada para acompanhamento e confirmação do pagamento.</p>
            </section>
        </div>

        <aside class="stack-lg">
            <section class="card card-pad stack-lg summary-sticky-card">
                <h2>Resumo do pedido</h2>
                <div class="stack">
                    @foreach ($cart['items'] as $item)
                        <div class="order-line">
                            <strong>{{ $item['product_name'] }}</strong>
                            <span class="tiny muted">{{ $item['variant_name'] }}</span>
                            <div class="summary-row tiny">
                                <span class="muted">{{ $item['quantity'] }} x R$ {{ number_format((float) $item['unit_price'], 2, ',', '.') }}</span>
                                <strong>R$ {{ number_format((float) $item['line_total'], 2, ',', '.') }}</strong>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="stack">
                    <div class="summary-row"><span class="muted">Subtotal</span><strong>R$ {{ number_format((float) $cart['subtotal'], 2, ',', '.') }}</strong></div>
                    <div class="summary-row"><span class="muted">Descontos</span><strong>- R$ {{ number_format((float) $cart['discount_total'], 2, ',', '.') }}</strong></div>
                    <div class="summary-row"><span class="muted">Entrega / retirada</span><strong id="checkout-summary-shipping">R$ {{ number_format((float) $cart['shipping_total'], 2, ',', '.') }}</strong></div>
                    <div class="summary-row total"><span>Total</span><span id="checkout-summary-total">R$ {{ number_format((float) $cart['total'], 2, ',', '.') }}</span></div>
                </div>

                <div class="ribbon-note">Após criar o pedido, a equipe segue para cobrança, conferência de arquivo e liberação da produção.</div>

                <button type="submit" class="btn btn-primary">Criar pedido</button>
                <a href="{{ route('cart.index') }}" class="btn btn-secondary">Voltar ao carrinho</a>
            </section>

            <section class="card card-pad stack">
                <h3>Procedimento padrão da gráfica</h3>
                <ul class="clean-list small muted">
                    <li>1. Confirmação da cobrança</li>
                    <li>2. Conferência técnica da arte final</li>
                    <li>3. Produção e acabamento</li>
                    <li>4. Expedição ou retirada</li>
                </ul>
            </section>
        </aside>
    </form>
@endsection

@push('scripts')
<script>
    (function () {
        const onlyDigits = (value) => String(value || '').replace(/\D+/g, '');

        const formatCep = (value) => {
            const digits = onlyDigits(value).slice(0, 8);
            if (digits.length <= 5) return digits;
            return digits.slice(0, 5) + '-' + digits.slice(5);
        };

        const formatPhone = (value) => {
            const digits = onlyDigits(value).slice(0, 11);
            if (!digits) return '';
            if (digits.length <= 2) return '(' + digits;
            if (digits.length <= 6) return '(' + digits.slice(0, 2) + ') ' + digits.slice(2);
            if (digits.length <= 10) {
                return '(' + digits.slice(0, 2) + ') ' + digits.slice(2, 6) + '-' + digits.slice(6);
            }
            return '(' + digits.slice(0, 2) + ') ' + digits.slice(2, 7) + '-' + digits.slice(7);
        };

        const formatDocument = (value) => {
            const digits = onlyDigits(value).slice(0, 14);
            if (digits.length <= 11) {
                if (digits.length <= 3) return digits;
                if (digits.length <= 6) return digits.slice(0, 3) + '.' + digits.slice(3);
                if (digits.length <= 9) return digits.slice(0, 3) + '.' + digits.slice(3, 6) + '.' + digits.slice(6);
                return digits.slice(0, 3) + '.' + digits.slice(3, 6) + '.' + digits.slice(6, 9) + '-' + digits.slice(9);
            }
            if (digits.length <= 2) return digits;
            if (digits.length <= 5) return digits.slice(0, 2) + '.' + digits.slice(2);
            if (digits.length <= 8) return digits.slice(0, 2) + '.' + digits.slice(2, 5) + '.' + digits.slice(5);
            if (digits.length <= 12) return digits.slice(0, 2) + '.' + digits.slice(2, 5) + '.' + digits.slice(5, 8) + '/' + digits.slice(8);
            return digits.slice(0, 2) + '.' + digits.slice(2, 5) + '.' + digits.slice(5, 8) + '/' + digits.slice(8, 12) + '-' + digits.slice(12);
        };

        const bindMask = (selector, formatter) => {
            document.querySelectorAll(selector).forEach((input) => {
                const apply = () => { input.value = formatter(input.value); };
                input.addEventListener('input', apply);
                input.addEventListener('blur', apply);
                apply();
            });
        };

        bindMask('[data-mask="cep-br"]', formatCep);
        bindMask('[data-mask="phone-br"]', formatPhone);
        bindMask('[data-mask="document-br"]', formatDocument);

        document.querySelectorAll('[data-force-uppercase]').forEach((input) => {
            const apply = () => { input.value = String(input.value || '').toUpperCase(); };
            input.addEventListener('input', apply);
            input.addEventListener('blur', apply);
            apply();
        });

        const paymentInstallments = document.getElementById('payment_installments');
        const paymentRadios = Array.from(document.querySelectorAll('input[name="payment[method]"]'));
        const syncInstallmentsState = () => {
            if (!paymentInstallments || paymentRadios.length === 0) return;
            const selected = paymentRadios.find((radio) => radio.checked)?.value;
            const isCard = selected === 'credit_card';
            paymentInstallments.disabled = !isCard;
            paymentInstallments.closest('.field')?.style.setProperty('opacity', isCard ? '1' : '.65');
            if (!isCard) paymentInstallments.value = '1';
        };
        paymentRadios.forEach((radio) => radio.addEventListener('change', syncInstallmentsState));
        syncInstallmentsState();

        const cepFieldMap = {
            billing: {
                street: 'billing_street',
                district: 'billing_district',
                city: 'billing_city',
                state: 'billing_state',
                status: 'billing_zipcode_status',
            },
            shipping: {
                street: 'shipping_street',
                district: 'shipping_district',
                city: 'shipping_city',
                state: 'shipping_state',
                status: 'shipping_zipcode_status',
            },
        };

        const setCepStatus = (scope, message, tone) => {
            const statusEl = document.getElementById(cepFieldMap[scope]?.status || '');
            if (!statusEl) return;
            statusEl.textContent = message;
            statusEl.style.color = tone === 'error'
                ? '#b52b17'
                : tone === 'success'
                    ? '#0f8a5f'
                    : '';
        };

        const fillAddressFromCep = async (input) => {
            const scope = input.dataset.cepScope;
            if (!scope || !cepFieldMap[scope]) return;

            const cep = onlyDigits(input.value);
            if (cep.length !== 8) {
                setCepStatus(scope, 'CEP incompleto.', 'error');
                return;
            }

            if (input.dataset.lookupCep === cep) return;
            input.dataset.lookupCep = cep;

            setCepStatus(scope, 'Consultando CEP...', 'info');

            try {
                const response = await fetch('https://viacep.com.br/ws/' + cep + '/json/');
                if (!response.ok) throw new Error('cep_request_failed');

                const data = await response.json();
                if (data.erro) {
                    setCepStatus(scope, 'CEP não encontrado. Confira e tente novamente.', 'error');
                    return;
                }

                const map = cepFieldMap[scope];
                const setField = (id, value) => {
                    const el = document.getElementById(id);
                    if (!el || !value) return;
                    el.value = String(value);
                    el.dispatchEvent(new Event('input', { bubbles: true }));
                };

                setField(map.street, data.logradouro);
                setField(map.district, data.bairro);
                setField(map.city, data.localidade);
                setField(map.state, String(data.uf || '').toUpperCase());

                setCepStatus(scope, 'Endereço sugerido pelo CEP. Confira número e complemento.', 'success');
            } catch (error) {
                setCepStatus(scope, 'Não foi possível consultar o CEP agora. Preencha manualmente.', 'error');
            }
        };

        document.querySelectorAll('[data-cep-scope]').forEach((input) => {
            input.addEventListener('blur', () => fillAddressFromCep(input));
        });

        const shippingZipInput = document.getElementById('shipping_zipcode');
        const shippingOptionsList = document.getElementById('shipping-options-list');
        const shippingOptionsStatus = document.getElementById('shipping-options-status');
        const shippingPickupInfo = document.getElementById('shipping-pickup-info');
        const calculateShippingBtn = document.getElementById('calculate-shipping-btn');
        const shippingHiddenFields = {
            code: document.getElementById('shipping_option_code'),
            label: document.getElementById('shipping_option_label'),
            provider: document.getElementById('shipping_option_provider'),
            cost: document.getElementById('shipping_option_cost'),
            deliveryDays: document.getElementById('shipping_option_delivery_days'),
            isPickup: document.getElementById('shipping_option_is_pickup'),
        };
        const summaryShipping = document.getElementById('checkout-summary-shipping');
        const summaryTotal = document.getElementById('checkout-summary-total');
        const headerTotal = document.getElementById('checkout-header-total');
        const brl = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });
        const oldSelectionCode = shippingHiddenFields.code?.value || '';
        const summaryBase = {
            subtotal: Number(@json((float) $cart['subtotal'])),
            discountTotal: Number(@json((float) $cart['discount_total'])),
            shippingTotal: Number(@json((float) $cart['shipping_total'])),
            total: Number(@json((float) $cart['total'])),
        };

        const setShippingStatus = (message, tone = 'neutral') => {
            if (!shippingOptionsStatus) return;
            shippingOptionsStatus.textContent = message;
            shippingOptionsStatus.style.color = tone === 'error'
                ? '#b52b17'
                : tone === 'success'
                    ? '#0f8a5f'
                    : '';
        };

        const updateSummaryTotals = (shippingTotal, total) => {
            if (summaryShipping) summaryShipping.textContent = brl.format(Number(shippingTotal || 0));
            if (summaryTotal) summaryTotal.textContent = brl.format(Number(total || 0));
            if (headerTotal) headerTotal.textContent = brl.format(Number(total || 0));
        };

        const applyShippingSelection = (quote, cartTotals) => {
            if (!quote) return;

            if (shippingHiddenFields.code) shippingHiddenFields.code.value = String(quote.code || '');
            if (shippingHiddenFields.label) shippingHiddenFields.label.value = String(quote.label || '');
            if (shippingHiddenFields.provider) shippingHiddenFields.provider.value = String(quote.provider || '');
            if (shippingHiddenFields.cost) shippingHiddenFields.cost.value = String(Number(quote.cost || 0).toFixed(2));
            if (shippingHiddenFields.deliveryDays) shippingHiddenFields.deliveryDays.value = quote.delivery_days == null ? '' : String(quote.delivery_days);
            if (shippingHiddenFields.isPickup) shippingHiddenFields.isPickup.value = quote.is_pickup ? '1' : '0';

            if (shippingPickupInfo) {
                shippingPickupInfo.hidden = !quote.is_pickup;
            }

            const shippingTotal = Number(cartTotals?.shipping_total ?? quote.cost ?? summaryBase.shippingTotal);
            const total = Number(cartTotals?.total ?? (summaryBase.subtotal - summaryBase.discountTotal + shippingTotal));
            updateSummaryTotals(shippingTotal, total);
        };

        const renderShippingOptions = (quotes, selected, pickupAddress) => {
            if (!shippingOptionsList) return;

            if (!Array.isArray(quotes) || quotes.length === 0) {
                shippingOptionsList.innerHTML = '';
                setShippingStatus('Nenhuma opção de entrega encontrada para este CEP. Tente outro CEP ou use retirada.', 'error');
                return;
            }

            shippingOptionsList.innerHTML = '';

            quotes.forEach((quote, index) => {
                const code = String(quote.code || '');
                const checked = String(selected?.code || '') === code;
                const deliveryText = quote.is_pickup
                    ? 'Retirada após liberação da produção'
                    : (quote.delivery_days ? `Prazo estimado: ${quote.delivery_days} dia(s) útil(eis)` : 'Prazo sob consulta');
                const priceText = Number(quote.cost || 0) <= 0 ? 'Grátis' : brl.format(Number(quote.cost || 0));
                const radioId = `shipping_quote_${index}`;

                const label = document.createElement('label');
                label.className = 'radio-card';
                label.setAttribute('for', radioId);
                label.style.alignItems = 'flex-start';
                label.style.justifyContent = 'space-between';

                label.innerHTML = `
                    <div style="display:flex; gap:10px; align-items:flex-start;">
                        <input id="${radioId}" type="radio" name="shipping_quote_choice_ui" value="${code}" ${checked ? 'checked' : ''} />
                        <div style="display:grid; gap:3px;">
                            <strong style="font-size:.92rem;">${quote.label}</strong>
                            <span class="tiny muted">${deliveryText}</span>
                            ${quote.is_pickup && pickupAddress ? `<span class="tiny muted">${pickupAddress}</span>` : ''}
                        </div>
                    </div>
                    <strong style="white-space:nowrap;">${priceText}</strong>
                `;

                const input = label.querySelector('input');
                input?.addEventListener('change', () => {
                    applyShippingSelection(quote);
                    setShippingStatus(`Opção selecionada: ${quote.label}.`, 'success');
                });

                shippingOptionsList.appendChild(label);
            });

            if (selected) {
                applyShippingSelection(selected);
            } else {
                applyShippingSelection(quotes[0]);
            }
        };

        const fetchShippingQuotes = async (forced = false) => {
            if (!shippingZipInput) return;

            const cep = onlyDigits(shippingZipInput.value);
            if (cep.length !== 8) {
                setShippingStatus('Preencha o CEP de entrega para calcular as opções de frete.', 'error');
                return;
            }

            if (!forced && shippingZipInput.dataset.quoteCep === cep) return;
            shippingZipInput.dataset.quoteCep = cep;

            if (calculateShippingBtn) {
                calculateShippingBtn.disabled = true;
                calculateShippingBtn.textContent = 'Calculando...';
            }

            setShippingStatus('Calculando opções de entrega e retirada...', 'neutral');

            try {
                const params = new URLSearchParams({
                    zipcode: shippingZipInput.value,
                    selected_code: shippingHiddenFields.code?.value || oldSelectionCode || '',
                });
                const response = await fetch(`{{ route('checkout.shipping.quotes') }}?${params.toString()}`, {
                    headers: { 'Accept': 'application/json' }
                });

                const data = await response.json();
                if (!response.ok) {
                    setShippingStatus(data?.message || 'Não foi possível calcular o frete agora.', 'error');
                    return;
                }

                renderShippingOptions(data.quotes || [], data.selected || null, data?.pickup?.address || '');
                if (data.cart) {
                    updateSummaryTotals(data.cart.shipping_total, data.cart.total);
                }
                setShippingStatus('Opções calculadas. Escolha como deseja receber o pedido.', 'success');
            } catch (error) {
                setShippingStatus('Falha ao calcular frete. Você pode tentar novamente em instantes.', 'error');
            } finally {
                if (calculateShippingBtn) {
                    calculateShippingBtn.disabled = false;
                    calculateShippingBtn.textContent = 'Calcular frete';
                }
            }
        };

        calculateShippingBtn?.addEventListener('click', () => fetchShippingQuotes(true));
        shippingZipInput?.addEventListener('blur', () => fetchShippingQuotes());

        const sameAsBillingCheckbox = document.getElementById('same_as_billing');
        sameAsBillingCheckbox?.addEventListener('change', () => {
            setTimeout(() => fetchShippingQuotes(true), 30);
        });

        if ((shippingZipInput && onlyDigits(shippingZipInput.value).length === 8) || oldSelectionCode) {
            fetchShippingQuotes(true);
        }
    })();
</script>
@endpush
