@extends('layouts.store')

@php
    $graphicServices = [
        [
            'icon' => 'catalog',
            'title' => 'Impressos Comerciais',
            'description' => 'Cartoes, folders, cardapios, papelaria e materiais promocionais com producao orientada por prazo.',
        ],
        [
            'icon' => 'portfolio',
            'title' => 'Comunicacao Visual e PDV',
            'description' => 'Banners, adesivos, sinalizacao e kits de ponto de venda para reforcar marca e gerar fluxo local.',
        ],
        [
            'icon' => 'store',
            'title' => 'Embalagens e Rotulos',
            'description' => 'Projetos para varejo e alimentos com foco em presenca de gondola e consistencia visual.',
        ],
        [
            'icon' => 'status-approved',
            'title' => 'Acabamentos Especiais',
            'description' => 'Laminacao, verniz, cortes e padroes premium para aumentar percepcao de valor da sua entrega.',
        ],
    ];

    $agencyServices = [
        [
            'icon' => 'traffic',
            'title' => 'Gestao de Trafego Pago',
            'description' => 'Campanhas de performance em Google Ads e Meta Ads com foco em custo por lead e retorno comercial.',
            'tag' => 'Novo',
        ],
        [
            'icon' => 'social',
            'title' => 'Gestao de Redes Sociais',
            'description' => 'Planejamento editorial, criacao de pecas, roteiros curtos e acompanhamento de resultados por objetivo.',
            'tag' => 'Novo',
        ],
        [
            'icon' => 'seo',
            'title' => 'SEO e Conteudo de Busca',
            'description' => 'Estrutura tecnica, titulos estrategicos e conteudo orientado por intencao para ampliar descoberta organica.',
        ],
        [
            'icon' => 'blog',
            'title' => 'Landing Pages e Copy',
            'description' => 'Paginas de conversao com proposta de valor clara, argumento comercial e CTA direto para contato.',
        ],
    ];

    $techServices = [
        [
            'icon' => 'app',
            'title' => 'Sistemas Web e Mobile Sob Medida',
            'description' => 'Projetos personalizados para operacao comercial, atendimento, vendas e gestao interna.',
        ],
        [
            'icon' => 'integration',
            'title' => 'Integracoes, APIs e Automacoes',
            'description' => 'Conexao entre plataformas, ERPs, CRMs e fluxos operacionais para reduzir retrabalho manual.',
        ],
        [
            'icon' => 'server',
            'title' => 'Cloud, Servidores e Sustentacao',
            'description' => 'Infraestrutura, monitoracao, seguranca e evolucao continua para manter aplicacoes estaveis.',
        ],
    ];

    $workflow = [
        [
            'icon' => 'status-review',
            'step' => 'Diagnostico',
            'title' => 'Entendimento de objetivo e gargalo',
            'description' => 'Mapeamos momento atual, metas e prioridade de execucao para nao dispersar investimento.',
        ],
        [
            'icon' => 'services',
            'step' => 'Plano',
            'title' => 'Estrutura integrada de servicos',
            'description' => 'Definimos o mix ideal entre grafica, agencia e tecnologia conforme fase de crescimento.',
        ],
        [
            'icon' => 'status-production',
            'step' => 'Execucao',
            'title' => 'Implementacao por sprints',
            'description' => 'Entregas com cronograma curto, checkpoints e ajustes rapidos conforme desempenho real.',
        ],
        [
            'icon' => 'status-approved',
            'step' => 'Evolucao',
            'title' => 'Medicao e melhoria continua',
            'description' => 'Acompanhamos indicadores de conversao e refinamos campanhas, conteudo e experiencia.',
        ],
    ];

    $marketReferences = [
        [
            'title' => 'Telefonica + AMP',
            'description' => 'Case publicado no Think with Google mostra aumento de 33% em leads apos acelerar experiencia mobile.',
            'url' => 'https://www.thinkwithgoogle.com/intl/en-emea/marketing-strategies/app-and-mobile/case-study-telefonica-uses-amp-to-increase-lead-generation-by-33/',
        ],
        [
            'title' => 'Paisabazaar + Performance',
            'description' => 'Relato de mercado com melhoria de velocidade mobile e crescimento de conversoes apos ajuste tecnico.',
            'url' => 'https://www.thinkwithgoogle.com/intl/en-apac/marketing-strategies/app-and-mobile/paisabazaar-improves-mobile-page-speeds-by-63-and-increases-conversions-by-20/',
        ],
        [
            'title' => 'BannerBuzz + Search',
            'description' => 'Case do Think with Google com ganho em termos de busca e aumento de valor de conversao.',
            'url' => 'https://www.thinkwithgoogle.com/intl/en-149/marketing-strategies/search/broad-match-smart-bidding/',
        ],
    ];

    $integratedPlanUrl = route('pages.contact', [
        'service_interest' => 'marketing-integrado',
        'subject' => 'Plano integrado de grafica e marketing',
        'message' => 'Quero um plano para combinar materiais graficos, trafego pago e redes sociais.',
    ]);

    $trafficUrl = route('pages.contact', [
        'service_interest' => 'trafego-pago',
        'subject' => 'Quero gestao de trafego pago',
        'message' => 'Preciso estruturar campanhas de performance para gerar leads e vendas.',
    ]);

    $socialUrl = route('pages.contact', [
        'service_interest' => 'redes-sociais',
        'subject' => 'Quero gestao de redes sociais',
        'message' => 'Preciso de planejamento de conteudo e rotina de publicacao para minha marca.',
    ]);

    $techUrl = route('pages.contact', [
        'service_interest' => 'tecnologia-octhopus',
        'subject' => 'Projeto com a Octhopus Labs',
        'message' => 'Quero conversar sobre desenvolvimento web/mobile e solucoes de tecnologia.',
    ]);

    $serviceSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => 'Servicos Integrados da Uriah Criativa',
        'serviceType' => 'Grafica, Marketing Digital e Solucoes de Tecnologia',
        'provider' => [
            '@type' => 'Organization',
            'name' => 'Uriah Criativa',
            'url' => route('home'),
        ],
        'areaServed' => 'BR',
        'url' => route('pages.services'),
        'description' => 'Servicos de grafica, trafego pago, redes sociais, SEO e tecnologia em parceria com a Octhopus Labs.',
        'offers' => [
            [
                '@type' => 'Offer',
                'name' => 'Gestao de Trafego Pago',
            ],
            [
                '@type' => 'Offer',
                'name' => 'Gestao de Redes Sociais',
            ],
            [
                '@type' => 'Offer',
                'name' => 'Solucoes de Tecnologia com Octhopus Labs',
            ],
        ],
    ];
@endphp

@section('title', 'Servicos Integrados | Grafica, Agencia e Tecnologia | Uriah Criativa')
@section('meta_description', 'A Uriah Criativa agora tambem atua com trafego pago, redes sociais e servicos de agencia, alem de parceria com a Octhopus Labs para web, mobile, sistemas e infraestrutura.')
@section('canonical_url', route('pages.services'))
@section('og_type', 'website')

@section('seo_json_ld')
    <script type="application/ld+json">{!! json_encode($serviceSchema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
@endsection

@push('head')
    <style>
        .services-shell {
            margin: 10px 0 32px;
            display: grid;
            gap: 16px;
        }

        .services-card {
            border-radius: 24px;
            border: 1px solid rgba(198, 161, 74, .18);
            background:
                radial-gradient(circle at 8% 8%, rgba(198, 161, 74, .16), transparent 42%),
                radial-gradient(circle at 92% 14%, rgba(31, 94, 255, .10), transparent 46%),
                linear-gradient(165deg, rgba(255,255,255,.96), rgba(248,243,234,.95));
            box-shadow:
                0 24px 44px rgba(14, 11, 9, .09),
                inset 0 1px 0 rgba(255,255,255,.76);
            overflow: hidden;
            position: relative;
        }

        .services-hero {
            padding: clamp(18px, 2.8vw, 34px);
            display: grid;
            gap: 18px;
        }

        .services-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: fit-content;
            border-radius: 999px;
            padding: 7px 12px;
            border: 1px solid rgba(198, 161, 74, .24);
            background: rgba(255,255,255,.78);
            color: #604d22;
            font-size: .74rem;
            font-weight: 800;
            letter-spacing: .07em;
            text-transform: uppercase;
        }

        .services-kicker::before {
            content: "";
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: linear-gradient(135deg, #b98a30, #d8b35f);
            box-shadow: 0 0 0 3px rgba(198,161,74,.15);
        }

        .services-hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(0, .95fr);
            gap: 18px;
            align-items: stretch;
        }

        .services-title {
            margin: 8px 0 0;
            font-size: clamp(1.68rem, 3.2vw, 2.9rem);
            line-height: 1.02;
            max-width: 17ch;
            letter-spacing: -.01em;
        }

        .services-subtitle {
            margin: 10px 0 0;
            color: #5e554b;
            font-size: 1rem;
            line-height: 1.6;
            max-width: 58ch;
        }

        .services-chip-row {
            margin-top: 14px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .services-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 7px 11px;
            border: 1px solid rgba(22,20,19,.10);
            background: rgba(255,255,255,.82);
            color: #5d544a;
            font-size: .76rem;
            font-weight: 700;
        }

        .services-chip strong {
            color: #1f1a16;
            font-weight: 800;
        }

        .services-chip-dot {
            width: 6px;
            height: 6px;
            border-radius: 999px;
            background: linear-gradient(135deg, #b98a30, #d8b35f);
        }

        .services-actions {
            margin-top: 16px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .services-summary-card {
            border-radius: 18px;
            border: 1px solid rgba(22,20,19,.08);
            background:
                linear-gradient(180deg, rgba(26,22,18,.96), rgba(17,15,13,.98)),
                radial-gradient(circle at 85% 0%, rgba(198,161,74,.18), transparent 42%);
            color: #fff;
            padding: clamp(14px, 2vw, 20px);
            display: grid;
            gap: 12px;
        }

        .services-summary-card h2 {
            margin: 0;
            font-size: 1.2rem;
        }

        .services-summary-points {
            margin: 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 8px;
        }

        .services-summary-points li {
            display: grid;
            grid-template-columns: 26px minmax(0, 1fr);
            gap: 8px;
            align-items: start;
        }

        .services-summary-icon {
            display: inline-grid;
            place-items: center;
            width: 26px;
            height: 26px;
            border-radius: 9px;
            border: 1px solid rgba(255,255,255,.18);
            background: rgba(255,255,255,.09);
            color: rgba(255,255,255,.92);
        }

        .services-summary-icon .nav-icon-svg {
            width: 15px;
            height: 15px;
            stroke-width: 1.85;
        }

        .services-columns {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .services-column {
            border-radius: 20px;
            border: 1px solid rgba(198,161,74,.16);
            background:
                radial-gradient(circle at 90% -5%, rgba(198,161,74,.13), transparent 40%),
                linear-gradient(180deg, rgba(255,255,255,.95), rgba(255,255,255,.87));
            box-shadow:
                0 16px 32px rgba(12,10,8,.07),
                inset 0 1px 0 rgba(255,255,255,.74);
            padding: clamp(14px, 2vw, 22px);
            display: grid;
            gap: 14px;
        }

        .services-column h2 {
            margin: 0;
            font-size: clamp(1.15rem, 2.1vw, 1.55rem);
            line-height: 1.1;
        }

        .services-column p {
            margin: 0;
            color: #61594f;
            font-size: .9rem;
            line-height: 1.54;
        }

        .services-list {
            display: grid;
            gap: 10px;
        }

        .services-item {
            border-radius: 14px;
            border: 1px solid rgba(22,20,19,.08);
            background:
                linear-gradient(180deg, rgba(255,255,255,.92), rgba(255,255,255,.84));
            padding: 11px;
            display: grid;
            grid-template-columns: 34px minmax(0, 1fr) auto;
            gap: 10px;
            align-items: start;
        }

        .services-item-icon {
            display: inline-grid;
            place-items: center;
            width: 34px;
            height: 34px;
            border-radius: 11px;
            border: 1px solid rgba(198,161,74,.20);
            background: rgba(255,255,255,.82);
            color: #6a5422;
        }

        .services-item-icon .nav-icon-svg {
            width: 17px;
            height: 17px;
            stroke-width: 1.9;
        }

        .services-item h3 {
            margin: 0;
            font-size: .92rem;
            line-height: 1.2;
        }

        .services-item p {
            margin: 4px 0 0;
            font-size: .82rem;
            line-height: 1.47;
            color: #645b52;
        }

        .services-item-tag {
            border-radius: 999px;
            padding: 5px 9px;
            font-size: .67rem;
            font-weight: 800;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: #8a3d14;
            border: 1px solid rgba(179,38,30,.25);
            background: rgba(179,38,30,.11);
            align-self: center;
        }

        .services-tech {
            padding: clamp(16px, 2.4vw, 24px);
            display: grid;
            grid-template-columns: minmax(0, 1.06fr) minmax(0, .94fr);
            gap: 14px;
            align-items: stretch;
        }

        .services-tech-copy {
            display: grid;
            gap: 10px;
        }

        .services-tech-copy h2 {
            margin: 0;
            font-size: clamp(1.3rem, 2.4vw, 1.95rem);
            line-height: 1.12;
        }

        .services-tech-copy p {
            margin: 0;
            color: #61594f;
            line-height: 1.6;
            max-width: 58ch;
        }

        .services-tech-brand {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            width: fit-content;
            border-radius: 999px;
            padding: 7px 12px;
            border: 1px solid rgba(31,94,255,.24);
            color: #234ca8;
            background: rgba(31,94,255,.08);
            font-size: .75rem;
            font-weight: 800;
            letter-spacing: .05em;
            text-transform: uppercase;
        }

        .services-tech-brand .dot {
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: linear-gradient(135deg, #2a61d2, #78a0ff);
            box-shadow: 0 0 0 3px rgba(31,94,255,.14);
        }

        .services-tech-list {
            display: grid;
            gap: 8px;
        }

        .services-tech-item {
            border-radius: 14px;
            border: 1px solid rgba(22,20,19,.08);
            background: rgba(255,255,255,.86);
            padding: 10px;
            display: grid;
            grid-template-columns: 34px minmax(0, 1fr);
            gap: 10px;
            align-items: start;
        }

        .services-tech-item h3 {
            margin: 0;
            font-size: .92rem;
            line-height: 1.22;
        }

        .services-tech-item p {
            margin: 4px 0 0;
            font-size: .82rem;
            line-height: 1.48;
            color: #655c52;
        }

        .services-tech-actions {
            margin-top: 12px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .services-flow {
            border-radius: 22px;
            border: 1px solid rgba(198,161,74,.16);
            background:
                radial-gradient(circle at 9% 0%, rgba(198,161,74,.14), transparent 42%),
                linear-gradient(180deg, rgba(255,255,255,.94), rgba(255,255,255,.88));
            box-shadow:
                0 18px 34px rgba(12,10,8,.07),
                inset 0 1px 0 rgba(255,255,255,.74);
            padding: clamp(14px, 2.1vw, 22px);
            display: grid;
            gap: 14px;
        }

        .services-flow-head h2 {
            margin: 0;
            font-size: clamp(1.2rem, 2.2vw, 1.75rem);
        }

        .services-flow-head p {
            margin: 6px 0 0;
            color: #61584f;
            line-height: 1.56;
            max-width: 66ch;
        }

        .services-flow-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
        }

        .services-flow-card {
            border-radius: 15px;
            border: 1px solid rgba(22,20,19,.08);
            background: rgba(255,255,255,.84);
            padding: 12px;
            display: grid;
            gap: 8px;
        }

        .services-flow-step {
            font-size: .68rem;
            letter-spacing: .06em;
            text-transform: uppercase;
            font-weight: 800;
            color: #6a4f1c;
        }

        .services-flow-card h3 {
            margin: 0;
            font-size: .93rem;
            line-height: 1.2;
        }

        .services-flow-card p {
            margin: 0;
            color: #655c53;
            font-size: .82rem;
            line-height: 1.48;
        }

        .services-flow-icon {
            width: 33px;
            height: 33px;
            display: inline-grid;
            place-items: center;
            border-radius: 11px;
            border: 1px solid rgba(198,161,74,.2);
            background: rgba(255,255,255,.84);
            color: #674f1d;
        }

        .services-flow-icon .nav-icon-svg {
            width: 17px;
            height: 17px;
        }

        .services-proof {
            border-radius: 22px;
            border: 1px solid rgba(198,161,74,.15);
            background:
                linear-gradient(180deg, rgba(255,255,255,.95), rgba(255,255,255,.88));
            box-shadow: 0 16px 30px rgba(12,10,8,.06);
            padding: clamp(14px, 2vw, 20px);
            display: grid;
            gap: 12px;
        }

        .services-proof h2 {
            margin: 0;
            font-size: clamp(1.18rem, 2vw, 1.6rem);
        }

        .services-proof-subtitle {
            color: #625a50;
            line-height: 1.55;
            margin: 0;
        }

        .services-proof-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .services-proof-card {
            border-radius: 14px;
            border: 1px solid rgba(22,20,19,.08);
            background: rgba(255,255,255,.84);
            padding: 11px;
            display: grid;
            gap: 8px;
        }

        .services-proof-card h3 {
            margin: 0;
            font-size: .9rem;
            line-height: 1.2;
        }

        .services-proof-card p {
            margin: 0;
            color: #655c53;
            font-size: .82rem;
            line-height: 1.5;
        }

        .services-proof-card a {
            font-size: .74rem;
            font-weight: 700;
            color: #1f5eff;
            width: fit-content;
        }

        .services-cta {
            padding: clamp(15px, 2.4vw, 26px);
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 12px;
            align-items: stretch;
        }

        .services-cta-card {
            border-radius: 16px;
            border: 1px solid rgba(22,20,19,.09);
            background:
                radial-gradient(circle at 92% -8%, rgba(198,161,74,.16), transparent 42%),
                linear-gradient(180deg, rgba(255,255,255,.95), rgba(255,255,255,.88));
            box-shadow:
                0 12px 22px rgba(12,10,8,.06),
                inset 0 1px 0 rgba(255,255,255,.74);
            padding: 14px;
            display: grid;
            gap: 10px;
        }

        .services-cta-card h2 {
            margin: 0;
            font-size: 1.1rem;
            line-height: 1.2;
        }

        .services-cta-card p {
            margin: 0;
            color: #61584f;
            font-size: .88rem;
            line-height: 1.54;
        }

        .services-cta-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .services-note {
            font-size: .75rem;
            color: #685f55;
        }

        @media (max-width: 1024px) {
            .services-hero-grid,
            .services-tech {
                grid-template-columns: 1fr;
            }

            .services-columns {
                grid-template-columns: 1fr;
            }

            .services-flow-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .services-proof-grid,
            .services-cta {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .services-flow-grid {
                grid-template-columns: 1fr;
            }

            .services-item {
                grid-template-columns: 30px minmax(0, 1fr);
            }

            .services-item-tag {
                grid-column: 2;
                justify-self: start;
                margin-top: 3px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="services-shell">
        <section class="services-card services-hero reveal-up">
            <div class="services-kicker">Servicos Integrados Uriah</div>
            <div class="services-hero-grid">
                <div>
                    <h1 class="services-title">Grafica, agencia e tecnologia para acelerar resultado comercial</h1>
                    <p class="services-subtitle">
                        Alem da operacao grafica, a Uriah Criativa agora entrega <strong>trafego pago</strong>, <strong>gestao de redes sociais</strong>
                        e demais servicos de agencia de publicidade para marcas que precisam vender com consistencia.
                    </p>
                    <div class="services-chip-row">
                        <span class="services-chip"><span class="services-chip-dot"></span><strong>Novo:</strong> Trafego pago</span>
                        <span class="services-chip"><span class="services-chip-dot"></span><strong>Novo:</strong> Redes sociais</span>
                        <span class="services-chip"><span class="services-chip-dot"></span>SEO e conteudo</span>
                        <span class="services-chip"><span class="services-chip-dot"></span>Landing pages e copy</span>
                    </div>
                    <div class="services-actions">
                        <a href="{{ $integratedPlanUrl }}" class="btn btn-primary">Quero um plano integrado</a>
                        <a href="#solicitar-contato" class="btn btn-secondary">Solicitar atendimento</a>
                    </div>
                </div>
                <aside class="services-summary-card">
                    <h2>Como isso ajuda seu negocio</h2>
                    <ul class="services-summary-points">
                        <li>
                            <span class="services-summary-icon" aria-hidden="true">@include('partials.nav-icon', ['name' => 'services'])</span>
                            <span>Mesma equipe cuidando da mensagem da marca no impresso, no digital e no ponto de conversao.</span>
                        </li>
                        <li>
                            <span class="services-summary-icon" aria-hidden="true">@include('partials.nav-icon', ['name' => 'traffic'])</span>
                            <span>Aquisicao de demanda com campanhas de performance orientadas por indicadores comerciais.</span>
                        </li>
                        <li>
                            <span class="services-summary-icon" aria-hidden="true">@include('partials.nav-icon', ['name' => 'social'])</span>
                            <span>Presenca recorrente em rede social para manter marca ativa e gerar lembranca de compra.</span>
                        </li>
                        <li>
                            <span class="services-summary-icon" aria-hidden="true">@include('partials.nav-icon', ['name' => 'app'])</span>
                            <span>Projetos de tecnologia via parceria com a Octhopus Labs quando a operacao exige sistemas.</span>
                        </li>
                    </ul>
                </aside>
            </div>
        </section>

        <section class="services-columns">
            <article class="services-column reveal-up">
                <div>
                    <h2>Frente 01: Servicos da Grafica</h2>
                    <p>Base solida de impressao para apoiar vendas, padronizacao de marca e materiais de alto impacto.</p>
                </div>
                <div class="services-list">
                    @foreach ($graphicServices as $service)
                        <article class="services-item">
                            <span class="services-item-icon" aria-hidden="true">@include('partials.nav-icon', ['name' => $service['icon']])</span>
                            <div>
                                <h3>{{ $service['title'] }}</h3>
                                <p>{{ $service['description'] }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </article>

            <article class="services-column reveal-up">
                <div>
                    <h2>Frente 02: Servicos de Agencia</h2>
                    <p>A Uriah tambem opera como agencia de publicidade para gerar demanda e fortalecer posicionamento.</p>
                </div>
                <div class="services-list">
                    @foreach ($agencyServices as $service)
                        <article class="services-item">
                            <span class="services-item-icon" aria-hidden="true">@include('partials.nav-icon', ['name' => $service['icon']])</span>
                            <div>
                                <h3>{{ $service['title'] }}</h3>
                                <p>{{ $service['description'] }}</p>
                            </div>
                            @if (!empty($service['tag']))
                                <span class="services-item-tag">{{ $service['tag'] }}</span>
                            @endif
                        </article>
                    @endforeach
                </div>
                <div class="services-actions" style="margin-top: 4px;">
                    <a href="{{ $trafficUrl }}" class="btn btn-secondary btn-sm">Falar sobre trafego pago</a>
                    <a href="{{ $socialUrl }}" class="btn btn-secondary btn-sm">Falar sobre redes sociais</a>
                </div>
            </article>
        </section>

        <section class="services-card services-tech reveal-up">
            <div class="services-tech-copy">
                <span class="services-tech-brand"><span class="dot"></span>Parceria oficial: Octhopus Labs</span>
                <h2>Quando o projeto exige software, entramos com nossa software house parceira</h2>
                <p>
                    Para demandas alem da comunicacao e marketing, atuamos junto da <strong>Octhopus Labs</strong>, empresa especializada
                    em desenvolvimento de sistemas, solucoes web e mobile, infraestrutura de servidores, cloud e integracoes de tecnologia.
                </p>
                <p>
                    Isso permite ao cliente resolver branding, aquisicao de demanda e tecnologia em um fluxo coordenado, sem ruido entre fornecedores.
                </p>
                <div class="services-tech-actions">
                    <a href="{{ $techUrl }}" class="btn btn-primary">Solicitar projeto com Octhopus Labs</a>
                    <a href="{{ route('pages.contact') }}" class="btn btn-secondary">Falar com consultor</a>
                </div>
            </div>
            <div class="services-tech-list">
                @foreach ($techServices as $service)
                    <article class="services-tech-item">
                        <span class="services-item-icon" aria-hidden="true">@include('partials.nav-icon', ['name' => $service['icon']])</span>
                        <div>
                            <h3>{{ $service['title'] }}</h3>
                            <p>{{ $service['description'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="services-flow reveal-up">
            <div class="services-flow-head">
                <h2>Metodo orientado por UX para facilitar decisao do cliente</h2>
                <p>Fluxo desenhado com linguagem objetiva, etapas claras e CTA em pontos de maior intencao de contato.</p>
            </div>
            <div class="services-flow-grid">
                @foreach ($workflow as $item)
                    <article class="services-flow-card">
                        <span class="services-flow-icon" aria-hidden="true">@include('partials.nav-icon', ['name' => $item['icon']])</span>
                        <span class="services-flow-step">{{ $item['step'] }}</span>
                        <h3>{{ $item['title'] }}</h3>
                        <p>{{ $item['description'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="services-proof reveal-up">
            <h2>Referencias de mercado usadas como inspiracao</h2>
            <p class="services-proof-subtitle">
                O desenho desta experiencia considera relatos publicos de performance digital e conversao apresentados por fontes de mercado.
            </p>
            <div class="services-proof-grid">
                @foreach ($marketReferences as $reference)
                    <article class="services-proof-card">
                        <h3>{{ $reference['title'] }}</h3>
                        <p>{{ $reference['description'] }}</p>
                        <a href="{{ $reference['url'] }}" target="_blank" rel="noopener noreferrer">Ver referencia</a>
                    </article>
                @endforeach
            </div>
        </section>

        <section id="solicitar-contato" class="services-card services-cta reveal-up">
            <article class="services-cta-card">
                <h2>Quero contratar servicos de agencia</h2>
                <p>Indicado para quem quer gerar demanda com campanhas, redes sociais, conteudo e funil de conversao.</p>
                <div class="services-cta-row">
                    <a href="{{ $integratedPlanUrl }}" class="btn btn-primary btn-sm">Solicitar plano integrado</a>
                    <a href="{{ $trafficUrl }}" class="btn btn-secondary btn-sm">Falar de trafego</a>
                    <a href="{{ $socialUrl }}" class="btn btn-secondary btn-sm">Falar de redes sociais</a>
                </div>
                <span class="services-note">Atendimento consultivo com retorno inicial em ate 1 dia util.</span>
            </article>

            <article class="services-cta-card">
                <h2>Quero um projeto de tecnologia</h2>
                <p>Indicado para sistemas, app, automacao, integracao entre plataformas, cloud e sustentacao tecnica.</p>
                <div class="services-cta-row">
                    <a href="{{ $techUrl }}" class="btn btn-primary btn-sm">Acionar Octhopus Labs</a>
                    <a href="{{ route('pages.contact', ['service_interest' => 'parceria']) }}" class="btn btn-secondary btn-sm">Propor parceria</a>
                </div>
                <span class="services-note">Escopo tecnico validado em conjunto entre equipe comercial e time de desenvolvimento.</span>
            </article>
        </section>
    </div>
@endsection
