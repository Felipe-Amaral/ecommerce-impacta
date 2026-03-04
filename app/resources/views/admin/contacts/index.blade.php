@extends('layouts.store')

@section('title', 'Contatos | Painel da Gráfica')
@section('meta_description', 'Caixa de entrada com mensagens de contato enviadas pelo site.')

@php
    $serviceLabels = [
        'orcamento' => 'Orçamento',
        'suporte-pedido' => 'Suporte de pedido',
        'servicos-graficos' => 'Serviços gráficos',
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

        .contact-row-unread td {
            background: rgba(31,94,255,.03);
        }

        .contact-unread-dot {
            display: inline-flex;
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: #1f5eff;
            box-shadow: 0 0 0 4px rgba(31,94,255,.14);
            margin-right: 8px;
            vertical-align: middle;
        }
    </style>
@endpush

@section('content')
    <section class="hero hero-studio reveal-up" style="margin-bottom: 18px;">
        <div class="hero-studio-grid">
            <div class="hero-pane">
                <div class="stack-xl">
                    <div class="pill-list">
                        <span class="badge badge-brand">Painel da gráfica</span>
                        <span class="pill">Contato</span>
                        <span class="pill">Inbox comercial</span>
                    </div>
                    <div class="stack">
                        <h1 class="text-gradient" style="font-size: clamp(1.8rem, 3vw, 2.7rem);">Caixa de entrada de contatos do site</h1>
                        <p class="lead">Centralize as mensagens enviadas pelo formulário, priorize atendimento e acompanhe o status de cada solicitação.</p>
                    </div>
                    <div class="hero-actions">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Voltar ao painel</a>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Pedidos</a>
                    </div>
                </div>
            </div>
            <div class="hero-pane">
                <div class="metric-grid">
                    <div class="metric-card"><strong>{{ $stats['total'] }}</strong><span>Total</span></div>
                    <div class="metric-card"><strong>{{ $stats['unread'] }}</strong><span>Não lidas</span></div>
                    <div class="metric-card"><strong>{{ $stats['today'] }}</strong><span>Hoje</span></div>
                </div>
                <div class="board-card stack" style="margin-top: 12px;">
                    <div class="board-title"><strong>Pipeline</strong><span class="tiny muted">atendimento</span></div>
                    <div class="process-rail">
                        <div class="process-step">
                            <span class="num">{{ $stats['new'] }}</span>
                            <span class="label">Novas</span>
                            <span class="eta">entrada</span>
                        </div>
                        <div class="process-step">
                            <span class="num">{{ $stats['responded'] }}</span>
                            <span class="label">Respondidas</span>
                            <span class="eta">concluídas</span>
                        </div>
                        <div class="process-step">
                            <span class="num">{{ $stats['unread'] }}</span>
                            <span class="label">Pendentes</span>
                            <span class="eta">triagem</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="card card-pad stack-lg" style="margin-bottom: 18px;">
        <form method="GET" action="{{ route('admin.contacts.index') }}" class="form-grid">
            <div class="field full">
                <label for="admin_contacts_q">Buscar</label>
                <input
                    id="admin_contacts_q"
                    type="search"
                    name="q"
                    class="input"
                    value="{{ $filters['q'] }}"
                    placeholder="Nome, e-mail, assunto, referência..."
                >
            </div>

            <div class="field">
                <label for="admin_contacts_status">Status</label>
                <select id="admin_contacts_status" name="status" class="select">
                    <option value="">Todos</option>
                    @foreach($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="admin_contacts_service">Tipo de demanda</label>
                <select id="admin_contacts_service" name="service_interest" class="select">
                    <option value="">Todos</option>
                    @foreach($serviceOptions as $serviceOption)
                        @php
                            $serviceKey = (string) $serviceOption;
                            $serviceLabel = $serviceLabels[$serviceKey] ?? \Illuminate\Support\Str::headline(str_replace('-', ' ', $serviceKey));
                        @endphp
                        <option value="{{ $serviceKey }}" @selected($filters['service_interest'] === $serviceKey)>{{ $serviceLabel }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field" style="align-content:end;">
                <label class="radio-card" for="admin_contacts_unread">
                    <input id="admin_contacts_unread" type="checkbox" name="unread" value="1" @checked($filters['unread'])>
                    <span>Somente não lidas</span>
                </label>
            </div>

            <div class="field full" style="justify-content:end;">
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <button type="submit" class="btn btn-secondary">Aplicar filtros</button>
                    <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary">Limpar</a>
                </div>
            </div>
        </form>
    </section>

    <section class="card card-pad stack" style="margin-bottom: 28px;">
        <div class="stack" style="gap: 4px;">
            <h2>Mensagens recebidas</h2>
            <p class="small muted">{{ $messages->total() }} mensagem(ns) encontrada(s)</p>
        </div>

        @if($messages->isEmpty())
            <p class="small muted">Nenhuma mensagem encontrada para os filtros informados.</p>
        @else
            <div class="table-wrap">
                <table class="table-compact">
                    <thead>
                        <tr>
                            <th>Contato</th>
                            <th>Assunto</th>
                            <th>Demanda</th>
                            <th>Status</th>
                            <th>Recebido</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($messages as $message)
                        @php
                            $serviceKey = (string) ($message->service_interest ?? '');
                            $serviceLabel = $serviceKey !== ''
                                ? ($serviceLabels[$serviceKey] ?? \Illuminate\Support\Str::headline(str_replace('-', ' ', $serviceKey)))
                                : 'Não informado';
                        @endphp
                        <tr class="{{ $message->read_at ? '' : 'contact-row-unread' }}">
                            <td>
                                <strong>
                                    @if(! $message->read_at)
                                        <span class="contact-unread-dot" aria-hidden="true"></span>
                                    @endif
                                    {{ $message->name }}
                                </strong>
                                <div class="tiny muted">{{ $message->email }}</div>
                                <div class="tiny muted">{{ $message->phone ?: 'Sem telefone' }}</div>
                            </td>
                            <td>
                                <strong>{{ $message->subject }}</strong>
                                <div class="tiny muted">{{ \Illuminate\Support\Str::limit($message->message, 120) }}</div>
                            </td>
                            <td>
                                <div class="tiny">{{ $serviceLabel }}</div>
                                <div class="tiny muted">{{ $message->preferred_contact ? 'Canal: '.$message->preferred_contact : 'Canal não informado' }}</div>
                            </td>
                            <td>
                                <span class="contact-status-chip {{ $statusTone[$message->status] ?? 'tone-archived' }}">
                                    {{ $statusLabels[$message->status] ?? \Illuminate\Support\Str::headline(str_replace('_', ' ', $message->status)) }}
                                </span>
                                @if($message->responded_at)
                                    <div class="tiny muted" style="margin-top:6px;">Respondida em {{ $message->responded_at->format('d/m/Y H:i') }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="tiny">{{ $message->created_at->format('d/m/Y H:i') }}</div>
                                <div class="tiny muted">{{ $message->read_at ? 'Lida' : 'Não lida' }}</div>
                            </td>
                            <td>
                                <div style="display:flex; gap:6px; flex-wrap:wrap;">
                                    <a href="{{ route('admin.contacts.show', $message) }}" class="btn btn-primary btn-sm">Abrir</a>
                                    @if(! $message->read_at)
                                        <form method="POST" action="{{ route('admin.contacts.read', $message) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-secondary btn-sm">Marcar lida</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{ $messages->links() }}
        @endif
    </section>
@endsection
