@extends('layouts.store')

@section('title', 'Mensagem de contato | Painel da Gráfica')
@section('meta_description', 'Visualização detalhada de mensagem de contato recebida no site.')

@php
    $serviceLabels = [
        'orcamento' => 'Orçamento',
        'servicos-graficos' => 'Serviços gráficos',
        'trafego-pago' => 'Tráfego pago e mídia de performance',
        'redes-sociais' => 'Gestão de redes sociais',
        'marketing-integrado' => 'Plano de marketing integrado',
        'tecnologia-octhopus' => 'Soluções de tecnologia (Octhopus Labs)',
        'suporte-pedido' => 'Suporte de pedido',
        'parceria' => 'Parceria comercial',
        'outros' => 'Outros assuntos',
    ];

    $statusTone = [
        'new' => 'tone-new',
        'in_progress' => 'tone-progress',
        'responded' => 'tone-responded',
        'archived' => 'tone-archived',
        'spam' => 'tone-spam',
    ];

    $serviceKey = (string) ($contactMessage->service_interest ?? '');
    $serviceLabel = $serviceKey !== ''
        ? ($serviceLabels[$serviceKey] ?? \Illuminate\Support\Str::headline(str_replace('-', ' ', $serviceKey)))
        : 'Não informado';
    $statusLabel = $statusLabels[$contactMessage->status] ?? \Illuminate\Support\Str::headline(str_replace('_', ' ', $contactMessage->status));

    $phoneDigits = preg_replace('/\D+/', '', (string) ($contactMessage->phone ?? ''));
    $whatsappLink = $phoneDigits !== '' ? 'https://wa.me/'.$phoneDigits : null;
@endphp

@push('head')
    <style>
        .contact-status-chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .04em;
            text-transform: uppercase;
            border: 1px solid transparent;
        }

        .contact-status-chip::before {
            content: "";
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: currentColor;
        }

        .contact-status-chip.tone-new {
            color: #8a3b16;
            border-color: rgba(195,58,29,.26);
            background: rgba(195,58,29,.10);
        }

        .contact-status-chip.tone-progress {
            color: #1f5eff;
            border-color: rgba(31,94,255,.24);
            background: rgba(31,94,255,.10);
        }

        .contact-status-chip.tone-responded {
            color: #0d7a53;
            border-color: rgba(15,138,95,.24);
            background: rgba(15,138,95,.10);
        }

        .contact-status-chip.tone-archived {
            color: #6a6258;
            border-color: rgba(106,98,88,.22);
            background: rgba(106,98,88,.10);
        }

        .contact-status-chip.tone-spam {
            color: #8f221c;
            border-color: rgba(179,38,30,.26);
            background: rgba(179,38,30,.10);
        }

        .contact-message-body {
            margin: 0;
            border-radius: 14px;
            border: 1px solid rgba(112,92,70,.16);
            background: rgba(255,255,255,.72);
            padding: 14px;
            white-space: pre-wrap;
            line-height: 1.58;
        }

        .contact-meta {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            gap: 10px;
        }

        .contact-meta li {
            display: grid;
            gap: 2px;
            border-bottom: 1px dashed rgba(112,92,70,.16);
            padding-bottom: 8px;
        }

        .contact-meta li:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }
    </style>
@endpush

@section('content')
    <section class="hero hero-studio reveal-up" style="margin-bottom: 18px;">
        <div class="hero-studio-grid">
            <div class="hero-pane">
                <div class="stack-xl">
                    <div class="pill-list">
                        <span class="badge badge-brand">Contato</span>
                        <span class="pill">Detalhe da mensagem</span>
                    </div>
                    <div class="stack">
                        <h1 class="text-gradient" style="font-size: clamp(1.6rem, 2.8vw, 2.4rem);">{{ $contactMessage->subject }}</h1>
                        <p class="lead">Mensagem enviada por <strong>{{ $contactMessage->name }}</strong> em {{ $contactMessage->created_at->format('d/m/Y H:i') }}.</p>
                    </div>
                    <div class="hero-actions">
                        <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary">Voltar para inbox</a>
                        <a href="mailto:{{ $contactMessage->email }}" class="btn btn-secondary">Responder por e-mail</a>
                        @if($whatsappLink)
                            <a href="{{ $whatsappLink }}" target="_blank" rel="noreferrer" class="btn btn-secondary">Abrir WhatsApp</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="hero-pane">
                <div class="stack">
                    <span class="contact-status-chip {{ $statusTone[$contactMessage->status] ?? 'tone-archived' }}">{{ $statusLabel }}</span>
                    <span class="tiny muted">{{ $contactMessage->read_at ? 'Lida em '.$contactMessage->read_at->format('d/m/Y H:i') : 'Ainda não lida' }}</span>
                    <span class="tiny muted">{{ $contactMessage->responded_at ? 'Respondida em '.$contactMessage->responded_at->format('d/m/Y H:i') : 'Sem registro de resposta' }}</span>
                </div>
                <section class="card card-pad stack" style="margin-top: 12px;">
                    <h3 style="margin:0;">Atualizar status</h3>
                    <form method="POST" action="{{ route('admin.contacts.status.update', $contactMessage) }}" class="stack">
                        @csrf
                        @method('PATCH')
                        <div class="field">
                            <label for="admin_contact_status">Status</label>
                            <select id="admin_contact_status" name="status" class="select">
                                @foreach($statusLabels as $value => $label)
                                    <option value="{{ $value }}" @selected($contactMessage->status === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <label class="radio-card" for="admin_contact_mark_as_read">
                            <input id="admin_contact_mark_as_read" type="checkbox" name="mark_as_read" value="1" @checked(! is_null($contactMessage->read_at))>
                            <span>Marcar como lida ao salvar</span>
                        </label>
                        <button type="submit" class="btn btn-primary">Salvar status</button>
                    </form>
                </section>
            </div>
        </div>
    </section>

    <section class="split" style="margin-bottom: 28px;">
        <article class="card card-pad stack-lg">
            <div class="section-head">
                <div class="copy">
                    <span class="section-kicker">Mensagem</span>
                    <h2>Conteúdo enviado</h2>
                </div>
            </div>
            <p class="contact-message-body">{{ $contactMessage->message }}</p>
        </article>

        <aside class="stack-lg">
            <section class="card card-pad stack">
                <div class="section-head">
                    <div class="copy">
                        <span class="section-kicker">Dados de contato</span>
                        <h3>Informações do cliente</h3>
                    </div>
                </div>
                <ul class="contact-meta small">
                    <li><span class="tiny muted">Nome</span><strong>{{ $contactMessage->name }}</strong></li>
                    <li><span class="tiny muted">E-mail</span><strong>{{ $contactMessage->email }}</strong></li>
                    <li><span class="tiny muted">Telefone</span><strong>{{ $contactMessage->phone ?: 'Não informado' }}</strong></li>
                    <li><span class="tiny muted">Canal preferido</span><strong>{{ $contactMessage->preferred_contact ?: 'Não informado' }}</strong></li>
                    <li><span class="tiny muted">Tipo de demanda</span><strong>{{ $serviceLabel }}</strong></li>
                    <li><span class="tiny muted">Referência do pedido</span><strong>{{ $contactMessage->order_reference ?: 'Não informada' }}</strong></li>
                </ul>
            </section>

            <section class="card card-pad stack">
                <div class="section-head">
                    <div class="copy">
                        <span class="section-kicker">Metadados</span>
                        <h3>Registro técnico</h3>
                    </div>
                </div>
                <ul class="contact-meta small">
                    <li><span class="tiny muted">Origem</span><strong>{{ $contactMessage->source_url ?: route('pages.contact') }}</strong></li>
                    <li><span class="tiny muted">IP</span><strong>{{ $contactMessage->ip_address ?: 'Não disponível' }}</strong></li>
                    <li><span class="tiny muted">Navegador</span><strong>{{ $contactMessage->user_agent ?: 'Não disponível' }}</strong></li>
                    <li><span class="tiny muted">Aceite LGPD</span><strong>{{ $contactMessage->lgpd_consent ? 'Sim' : 'Não' }}</strong></li>
                    <li><span class="tiny muted">Usuário autenticado</span><strong>{{ $contactMessage->user?->email ?: 'Visitante' }}</strong></li>
                </ul>
            </section>
        </aside>
    </section>
@endsection
