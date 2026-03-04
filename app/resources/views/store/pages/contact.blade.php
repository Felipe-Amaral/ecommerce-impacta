@extends('layouts.store')

@section('title', 'Contato | Uriah Criativa')
@section('meta_description', 'Fale com a equipe da Uriah Criativa para orçamento, suporte de pedido, serviços gráficos e parcerias comerciais.')
@section('canonical_url', route('pages.contact'))
@section('og_type', 'website')

@php
    $selectedServiceInterest = old('service_interest', $prefillServiceInterest ?? '');
    $selectedSubject = old('subject', $prefillSubject ?? '');
    $selectedMessage = old('message', $prefillMessage ?? '');

    $errorTargets = [
        'name' => 'contact_name',
        'email' => 'contact_email',
        'phone' => 'contact_phone',
        'subject' => 'contact_subject',
        'service_interest' => 'contact_service_interest',
        'preferred_contact' => 'contact_preferred_contact',
        'order_reference' => 'contact_order_reference',
        'message' => 'contact_message',
        'lgpd_consent' => 'contact_lgpd_consent',
    ];

    $errorFields = array_values(array_filter(
        array_keys($errorTargets),
        static fn (string $field): bool => $errors->has($field)
    ));

    $contactPageSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'ContactPage',
        'name' => 'Contato | Uriah Criativa',
        'url' => route('pages.contact'),
        'inLanguage' => 'pt-BR',
        'description' => 'Canal de contato para atendimento comercial, suporte de pedidos e orçamento gráfico.',
    ];
@endphp

@section('seo_json_ld')
    <script type="application/ld+json">{!! json_encode($contactPageSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
@endsection

@push('head')
    <style>
        .contact-shell {
            margin: 10px 0 30px;
            display: grid;
            gap: 16px;
        }

        .contact-hero {
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            border: 1px solid rgba(198, 161, 74, .2);
            background:
                radial-gradient(circle at 6% 8%, rgba(198, 161, 74, .2), transparent 44%),
                radial-gradient(circle at 95% 25%, rgba(31, 94, 255, .11), transparent 45%),
                linear-gradient(160deg, rgba(255, 255, 255, .95), rgba(249, 243, 233, .94));
            box-shadow:
                0 25px 42px rgba(13, 11, 8, .08),
                inset 0 1px 0 rgba(255, 255, 255, .78);
            padding: clamp(16px, 2.5vw, 28px);
            display: grid;
            gap: 16px;
        }

        .contact-hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.08fr) minmax(0, .92fr);
            gap: 14px;
            align-items: stretch;
        }

        .contact-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: fit-content;
            padding: 7px 12px;
            border-radius: 999px;
            border: 1px solid rgba(198, 161, 74, .24);
            background: rgba(255, 255, 255, .76);
            color: #614f27;
            font-size: .75rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .contact-kicker::before {
            content: "";
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: linear-gradient(135deg, #b68a2f, #ddb963);
            box-shadow: 0 0 0 3px rgba(198, 161, 74, .18);
        }

        .contact-headline {
            margin: 10px 0 0;
            font-size: clamp(1.65rem, 3vw, 2.55rem);
            line-height: 1.02;
            max-width: 18ch;
        }

        .contact-subheadline {
            margin: 10px 0 0;
            color: #5f574e;
            font-size: .99rem;
            line-height: 1.6;
            max-width: 58ch;
        }

        .contact-point-list {
            margin: 16px 0 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 8px;
        }

        .contact-point-list li {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #3d352d;
            font-weight: 600;
            font-size: .9rem;
        }

        .contact-point-list .mini-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: linear-gradient(135deg, #c33a1d, #f26a29);
            box-shadow: 0 0 0 4px rgba(195, 58, 29, .12);
            flex-shrink: 0;
        }

        .contact-hero-side {
            border-radius: 18px;
            border: 1px solid rgba(17, 15, 12, .08);
            background:
                radial-gradient(circle at 88% 8%, rgba(198,161,74,.18), transparent 40%),
                linear-gradient(170deg, rgba(30,24,19,.95), rgba(16,13,11,.96));
            color: rgba(255, 255, 255, .94);
            padding: 16px;
            display: grid;
            gap: 12px;
            align-content: start;
        }

        .contact-hero-side h2 {
            margin: 0;
            font-size: 1.1rem;
            color: #f7ead0;
        }

        .contact-hero-side p {
            margin: 0;
            font-size: .9rem;
            color: rgba(255, 255, 255, .82);
            line-height: 1.52;
        }

        .contact-step-list {
            margin: 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 10px;
        }

        .contact-step-list li {
            display: grid;
            grid-template-columns: 24px minmax(0, 1fr);
            gap: 8px;
            align-items: start;
            font-size: .86rem;
            color: rgba(255, 255, 255, .87);
        }

        .contact-step-list .n {
            width: 24px;
            height: 24px;
            border-radius: 8px;
            border: 1px solid rgba(212, 173, 88, .32);
            background: rgba(255, 255, 255, .06);
            display: grid;
            place-items: center;
            color: #f7dca2;
            font-weight: 700;
            font-size: .8rem;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: minmax(0, .86fr) minmax(0, 1.14fr);
            gap: 16px;
            align-items: start;
        }

        .contact-side {
            display: grid;
            gap: 12px;
        }

        .contact-channel-card {
            border-radius: 18px;
            border: 1px solid rgba(17, 15, 12, .08);
            background:
                radial-gradient(circle at 90% 0%, rgba(198,161,74,.15), transparent 44%),
                linear-gradient(180deg, rgba(255, 255, 255, .94), rgba(255, 255, 255, .86));
            box-shadow:
                0 12px 22px rgba(12, 10, 8, .06),
                inset 0 1px 0 rgba(255, 255, 255, .72);
            padding: 14px;
            display: grid;
            gap: 10px;
        }

        .contact-channel-card h3 {
            margin: 0;
            font-size: 1rem;
        }

        .contact-channel-card p {
            margin: 0;
            color: #645b52;
            font-size: .9rem;
            line-height: 1.52;
        }

        .contact-mini-list {
            margin: 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 8px;
        }

        .contact-mini-list li {
            display: grid;
            grid-template-columns: 28px minmax(0, 1fr);
            gap: 8px;
            align-items: center;
            color: #3d352d;
            font-size: .88rem;
            font-weight: 600;
        }

        .contact-mini-icon {
            width: 28px;
            height: 28px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            color: #c33a1d;
            background: rgba(195, 58, 29, .08);
            border: 1px solid rgba(195, 58, 29, .14);
        }

        .contact-mini-icon .nav-icon-svg {
            width: 16px;
            height: 16px;
        }

        .contact-shortcuts {
            display: grid;
            gap: 8px;
        }

        .contact-shortcut {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 42px;
            border-radius: 12px;
            border: 1px solid rgba(198, 161, 74, .22);
            background: rgba(255, 255, 255, .82);
            color: #3b322a;
            font-size: .85rem;
            font-weight: 700;
            transition: .16s ease;
        }

        .contact-shortcut .nav-icon-svg {
            width: 18px;
            height: 18px;
            color: #c33a1d;
        }

        .contact-shortcut:hover {
            transform: translateY(-1px);
            border-color: rgba(198, 161, 74, .35);
            background: rgba(255, 255, 255, .96);
            box-shadow: 0 10px 18px rgba(12, 10, 8, .07);
        }

        .contact-form-card {
            border-radius: 20px;
            border: 1px solid rgba(17, 15, 12, .09);
            background:
                radial-gradient(circle at 95% 0%, rgba(198,161,74,.18), transparent 42%),
                linear-gradient(180deg, rgba(255,255,255,.96), rgba(255,255,255,.9));
            box-shadow:
                0 14px 26px rgba(12, 10, 8, .07),
                inset 0 1px 0 rgba(255, 255, 255, .74);
            padding: clamp(16px, 2.2vw, 22px);
            display: grid;
            gap: 16px;
        }

        .contact-form-head h2 {
            margin: 0;
            font-size: clamp(1.2rem, 2.1vw, 1.55rem);
            line-height: 1.08;
        }

        .contact-form-head p {
            margin: 8px 0 0;
            color: #5f564c;
            font-size: .92rem;
            line-height: 1.55;
        }

        .contact-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .contact-field {
            display: grid;
            gap: 6px;
        }

        .contact-field.full {
            grid-column: 1 / -1;
        }

        .contact-label-row {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #453d35;
            font-size: .86rem;
            font-weight: 700;
        }

        .contact-required {
            color: #b3261e;
            font-weight: 800;
        }

        .contact-field .input,
        .contact-field .select,
        .contact-field .textarea {
            min-height: 46px;
            border: 1px solid rgba(112, 92, 70, .22);
            border-radius: 12px;
            background: rgba(255, 255, 255, .9);
            font-size: .94rem;
        }

        .contact-field .textarea {
            min-height: 140px;
        }

        .contact-field .input:focus,
        .contact-field .select:focus,
        .contact-field .textarea:focus {
            border-color: rgba(15, 93, 245, .34);
            outline: 2px solid rgba(15, 93, 245, .14);
            box-shadow: none;
        }

        .contact-field.has-error .input,
        .contact-field.has-error .select,
        .contact-field.has-error .textarea {
            border-color: rgba(179, 38, 30, .44);
            outline-color: rgba(179, 38, 30, .16);
        }

        .contact-help {
            font-size: .78rem;
            color: #73685d;
            line-height: 1.44;
        }

        .contact-error {
            font-size: .78rem;
            color: #8f221c;
            font-weight: 700;
            line-height: 1.4;
        }

        .contact-consent {
            margin-top: 2px;
            display: grid;
            gap: 6px;
        }

        .contact-consent label {
            display: inline-flex;
            align-items: flex-start;
            gap: 10px;
            color: #433a31;
            font-size: .84rem;
            line-height: 1.48;
        }

        .contact-consent input {
            margin-top: 2px;
            width: 17px;
            height: 17px;
            accent-color: #c33a1d;
        }

        .contact-submit {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: space-between;
            padding-top: 8px;
            border-top: 1px solid rgba(112, 92, 70, .16);
        }

        .contact-submit .btn {
            min-height: 44px;
            min-width: 164px;
            border-radius: 14px;
            font-size: .9rem;
        }

        .contact-submit-note {
            margin: 0;
            color: #5f564c;
            font-size: .82rem;
            line-height: 1.45;
            max-width: 42ch;
        }

        .contact-error-summary {
            border-radius: 16px;
            border: 1px solid rgba(179, 38, 30, .26);
            background:
                radial-gradient(circle at 100% 0%, rgba(179,38,30,.1), transparent 46%),
                linear-gradient(180deg, rgba(255,255,255,.94), rgba(255,255,255,.9));
            box-shadow: 0 10px 22px rgba(179, 38, 30, .08);
        }

        .contact-error-summary h2 {
            margin: 0;
            font-size: 1.08rem;
            color: #8f221c;
        }

        .contact-error-summary p {
            margin: 8px 0 0;
            color: #6f2d29;
            font-size: .88rem;
        }

        .contact-error-summary ul {
            margin: 10px 0 0;
            padding-left: 18px;
            display: grid;
            gap: 6px;
        }

        .contact-error-summary a {
            color: #8f221c;
            text-decoration: underline;
            text-underline-offset: 2px;
            font-size: .86rem;
            font-weight: 700;
        }

        .contact-honeypot {
            position: absolute;
            left: -9999px;
            width: 1px;
            height: 1px;
            overflow: hidden;
            opacity: 0;
            pointer-events: none;
        }

        @media (max-width: 1020px) {
            .contact-hero-grid,
            .contact-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 720px) {
            .contact-form-grid {
                grid-template-columns: 1fr;
            }

            .contact-submit {
                align-items: stretch;
            }

            .contact-submit .btn {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $user = auth()->user();
    @endphp

    <div class="contact-shell">
        <section class="contact-hero reveal-up">
            <div class="contact-hero-grid">
                <div>
                    <span class="contact-kicker">Atendimento Uriah Criativa</span>
                    <h1 class="contact-headline">Vamos conversar sobre o seu próximo projeto gráfico.</h1>
                    <p class="contact-subheadline">
                        Formulário objetivo, retorno ágil e acompanhamento claro. Envie os detalhes do que você precisa e nosso time comercial responde com próximos passos.
                    </p>
                    <ul class="contact-point-list" aria-label="Diferenciais do atendimento">
                        <li><span class="mini-dot" aria-hidden="true"></span>Resposta inicial em até 1 dia útil.</li>
                        <li><span class="mini-dot" aria-hidden="true"></span>Orientação de arquivo e acabamento para evitar retrabalho.</li>
                        <li><span class="mini-dot" aria-hidden="true"></span>Fluxo simples para orçamento, suporte e parceria comercial.</li>
                    </ul>
                </div>
                <aside class="contact-hero-side" aria-label="Como funciona o atendimento">
                    <h2>Como funciona</h2>
                    <p>Quanto mais contexto você enviar, mais rápido entregamos uma resposta útil.</p>
                    <ol class="contact-step-list">
                        <li><span class="n">1</span><span>Descreva sua necessidade no formulário abaixo.</span></li>
                        <li><span class="n">2</span><span>O time avalia escopo, prazo e melhor solução de produção.</span></li>
                        <li><span class="n">3</span><span>Você recebe retorno com encaminhamento e próximos passos.</span></li>
                    </ol>
                </aside>
            </div>
        </section>

        @if (! empty($errorFields))
            <section class="contact-error-summary card card-pad reveal-up" aria-labelledby="contact-error-title" tabindex="-1">
                <h2 id="contact-error-title">Revise os campos antes de enviar</h2>
                <p>Encontramos alguns pontos para ajustar:</p>
                <ul>
                    @foreach ($errorFields as $field)
                        <li>
                            <a href="#{{ $errorTargets[$field] }}">{{ $errors->first($field) }}</a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        <section class="contact-grid">
            <aside class="contact-side">
                <article class="contact-channel-card reveal-up">
                    <h3>Canais rápidos</h3>
                    <p>Se você já sabe o que precisa, pode ir direto para os atalhos abaixo.</p>
                    <div class="contact-shortcuts">
                        <a class="contact-shortcut" href="{{ route('pages.quote') }}">
                            @include('partials.nav-icon', ['name' => 'quote', 'class' => 'nav-icon'])
                            Solicitar orçamento
                        </a>
                        <a class="contact-shortcut" href="{{ route('blog.index') }}">
                            @include('partials.nav-icon', ['name' => 'blog', 'class' => 'nav-icon'])
                            Ver conteúdos no blog
                        </a>
                        <a class="contact-shortcut" href="{{ auth()->check() ? route('account.orders.index') : route('login') }}">
                            @include('partials.nav-icon', ['name' => 'orders', 'class' => 'nav-icon'])
                            {{ auth()->check() ? 'Acompanhar pedidos' : 'Entrar para acompanhar pedidos' }}
                        </a>
                    </div>
                </article>

                <article class="contact-channel-card reveal-up">
                    <h3>Boas práticas para resposta mais rápida</h3>
                    <ul class="contact-mini-list">
                        <li>
                            <span class="contact-mini-icon" aria-hidden="true">@include('partials.nav-icon', ['name' => 'services'])</span>
                            Informe qual serviço ou produto você deseja.
                        </li>
                        <li>
                            <span class="contact-mini-icon" aria-hidden="true">@include('partials.nav-icon', ['name' => 'quote'])</span>
                            Se tiver prazo, descreva a data desejada.
                        </li>
                        <li>
                            <span class="contact-mini-icon" aria-hidden="true">@include('partials.nav-icon', ['name' => 'contact'])</span>
                            Deixe um canal de retorno preferencial.
                        </li>
                    </ul>
                </article>
            </aside>

            <article class="contact-form-card reveal-up">
                <header class="contact-form-head">
                    <h2>Envie sua mensagem</h2>
                    <p>Campos marcados com <span class="contact-required">*</span> são obrigatórios.</p>
                </header>

                <form method="POST" action="{{ route('pages.contact.store') }}" novalidate>
                    @csrf

                    <div class="contact-honeypot" aria-hidden="true">
                        <label for="company_website">Site da empresa</label>
                        <input
                            id="company_website"
                            type="text"
                            name="company_website"
                            autocomplete="off"
                            tabindex="-1"
                            value="{{ old('company_website') }}"
                        >
                    </div>
                    <input type="hidden" name="form_started_at" value="{{ old('form_started_at', $formStartedAt) }}">

                    <div class="contact-form-grid">
                        <div class="contact-field full {{ $errors->has('name') ? 'has-error' : '' }}">
                            <label class="contact-label-row" for="contact_name">
                                Nome completo <span class="contact-required">*</span>
                                @include('partials.help-hint', ['text' => 'Use o nome que você deseja no atendimento.'])
                            </label>
                            <input
                                id="contact_name"
                                class="input"
                                name="name"
                                value="{{ old('name', $user?->name) }}"
                                autocomplete="name"
                                aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                                aria-describedby="{{ $errors->has('name') ? 'contact_name_error' : 'contact_name_help' }}"
                            >
                            @if ($errors->has('name'))
                                <span id="contact_name_error" class="contact-error">{{ $errors->first('name') }}</span>
                            @else
                                <span id="contact_name_help" class="contact-help">Como devemos te chamar no retorno.</span>
                            @endif
                        </div>

                        <div class="contact-field {{ $errors->has('email') ? 'has-error' : '' }}">
                            <label class="contact-label-row" for="contact_email">
                                E-mail <span class="contact-required">*</span>
                                @include('partials.help-hint', ['text' => 'Enviaremos a resposta principal para este endereço.'])
                            </label>
                            <input
                                id="contact_email"
                                class="input"
                                type="email"
                                name="email"
                                value="{{ old('email', $user?->email) }}"
                                autocomplete="email"
                                aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                                aria-describedby="{{ $errors->has('email') ? 'contact_email_error' : 'contact_email_help' }}"
                            >
                            @if ($errors->has('email'))
                                <span id="contact_email_error" class="contact-error">{{ $errors->first('email') }}</span>
                            @else
                                <span id="contact_email_help" class="contact-help">Exemplo: nome@empresa.com.br.</span>
                            @endif
                        </div>

                        <div class="contact-field {{ $errors->has('phone') ? 'has-error' : '' }}">
                            <label class="contact-label-row" for="contact_phone">
                                Telefone / WhatsApp
                                @include('partials.help-hint', ['text' => 'Opcional, mas acelera contato em caso de urgência.'])
                            </label>
                            <input
                                id="contact_phone"
                                class="input"
                                name="phone"
                                value="{{ old('phone') }}"
                                autocomplete="tel"
                                inputmode="tel"
                                aria-invalid="{{ $errors->has('phone') ? 'true' : 'false' }}"
                                aria-describedby="{{ $errors->has('phone') ? 'contact_phone_error' : 'contact_phone_help' }}"
                            >
                            @if ($errors->has('phone'))
                                <span id="contact_phone_error" class="contact-error">{{ $errors->first('phone') }}</span>
                            @else
                                <span id="contact_phone_help" class="contact-help">Inclua DDD para facilitar retorno.</span>
                            @endif
                        </div>

                        <div class="contact-field {{ $errors->has('preferred_contact') ? 'has-error' : '' }}">
                            <label class="contact-label-row" for="contact_preferred_contact">
                                Canal de retorno preferido
                                @include('partials.help-hint', ['text' => 'Canal mais prático para você receber nossa resposta.'])
                            </label>
                            <select
                                id="contact_preferred_contact"
                                class="select"
                                name="preferred_contact"
                                aria-invalid="{{ $errors->has('preferred_contact') ? 'true' : 'false' }}"
                                aria-describedby="{{ $errors->has('preferred_contact') ? 'contact_preferred_contact_error' : 'contact_preferred_contact_help' }}"
                            >
                                <option value="">Selecione</option>
                                @foreach ($preferredContactOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('preferred_contact') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('preferred_contact'))
                                <span id="contact_preferred_contact_error" class="contact-error">{{ $errors->first('preferred_contact') }}</span>
                            @else
                                <span id="contact_preferred_contact_help" class="contact-help">Opcional, usamos quando possível.</span>
                            @endif
                        </div>

                        <div class="contact-field {{ $errors->has('subject') ? 'has-error' : '' }}">
                            <label class="contact-label-row" for="contact_subject">
                                Assunto <span class="contact-required">*</span>
                                @include('partials.help-hint', ['text' => 'Resumo curto do motivo do contato.'])
                            </label>
                            <input
                                id="contact_subject"
                                class="input"
                                name="subject"
                                value="{{ $selectedSubject }}"
                                aria-invalid="{{ $errors->has('subject') ? 'true' : 'false' }}"
                                aria-describedby="{{ $errors->has('subject') ? 'contact_subject_error' : 'contact_subject_help' }}"
                            >
                            @if ($errors->has('subject'))
                                <span id="contact_subject_error" class="contact-error">{{ $errors->first('subject') }}</span>
                            @else
                                <span id="contact_subject_help" class="contact-help">Exemplo: Orçamento de cartões em couché 300g.</span>
                            @endif
                        </div>

                        <div class="contact-field {{ $errors->has('service_interest') ? 'has-error' : '' }}">
                            <label class="contact-label-row" for="contact_service_interest">
                                Tipo de demanda
                                @include('partials.help-hint', ['text' => 'Ajuda nossa triagem interna e reduz tempo de resposta.'])
                            </label>
                            <select
                                id="contact_service_interest"
                                class="select"
                                name="service_interest"
                                aria-invalid="{{ $errors->has('service_interest') ? 'true' : 'false' }}"
                                aria-describedby="{{ $errors->has('service_interest') ? 'contact_service_interest_error' : 'contact_service_interest_help' }}"
                            >
                                <option value="">Selecione</option>
                                @foreach ($serviceOptions as $value => $label)
                                    <option value="{{ $value }}" @selected($selectedServiceInterest === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('service_interest'))
                                <span id="contact_service_interest_error" class="contact-error">{{ $errors->first('service_interest') }}</span>
                            @else
                                <span id="contact_service_interest_help" class="contact-help">Escolha a categoria que melhor representa sua necessidade.</span>
                            @endif
                        </div>

                        <div class="contact-field full {{ $errors->has('order_reference') ? 'has-error' : '' }}">
                            <label class="contact-label-row" for="contact_order_reference">
                                Referência do pedido (opcional)
                                @include('partials.help-hint', ['text' => 'Se já existe pedido, informe número ou identificação para agilizar suporte.'])
                            </label>
                            <input
                                id="contact_order_reference"
                                class="input"
                                name="order_reference"
                                value="{{ old('order_reference') }}"
                                aria-invalid="{{ $errors->has('order_reference') ? 'true' : 'false' }}"
                                aria-describedby="{{ $errors->has('order_reference') ? 'contact_order_reference_error' : 'contact_order_reference_help' }}"
                            >
                            @if ($errors->has('order_reference'))
                                <span id="contact_order_reference_error" class="contact-error">{{ $errors->first('order_reference') }}</span>
                            @else
                                <span id="contact_order_reference_help" class="contact-help">Exemplo: #1024, Pedido marketplace, orçamento interno, etc.</span>
                            @endif
                        </div>

                        <div class="contact-field full {{ $errors->has('message') ? 'has-error' : '' }}">
                            <label class="contact-label-row" for="contact_message">
                                Mensagem <span class="contact-required">*</span>
                                @include('partials.help-hint', ['text' => 'Inclua volume, formato, acabamento e prazo para resposta mais precisa.'])
                            </label>
                            <textarea
                                id="contact_message"
                                class="textarea"
                                name="message"
                                aria-invalid="{{ $errors->has('message') ? 'true' : 'false' }}"
                                aria-describedby="{{ $errors->has('message') ? 'contact_message_error' : 'contact_message_help' }}"
                            >{{ $selectedMessage }}</textarea>
                            @if ($errors->has('message'))
                                <span id="contact_message_error" class="contact-error">{{ $errors->first('message') }}</span>
                            @else
                                <span id="contact_message_help" class="contact-help">Descreva objetivo, quantidade, material e prazo desejado.</span>
                            @endif
                        </div>
                    </div>

                    <div class="contact-consent">
                        <label for="contact_lgpd_consent">
                            <input
                                id="contact_lgpd_consent"
                                type="checkbox"
                                name="lgpd_consent"
                                value="1"
                                @checked(old('lgpd_consent'))
                            >
                            <span>Autorizo o uso dos dados deste formulário para retorno do atendimento comercial e suporte.</span>
                        </label>
                        @if ($errors->has('lgpd_consent'))
                            <span class="contact-error">{{ $errors->first('lgpd_consent') }}</span>
                        @endif
                    </div>

                    <div class="contact-submit">
                        <p class="contact-submit-note">
                            Após enviar, mostramos uma confirmação e sua mensagem fica registrada para acompanhamento interno.
                        </p>
                        <button type="submit" class="btn btn-primary">
                            @include('partials.nav-icon', ['name' => 'contact', 'class' => 'nav-icon'])
                            Enviar mensagem
                        </button>
                    </div>
                </form>
            </article>
        </section>
    </div>
@endsection
