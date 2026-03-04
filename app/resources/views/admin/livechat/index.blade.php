@extends('layouts.store')

@section('title', 'Atendimento Online | Painel da Grafica')
@section('meta_description', 'Monitor de visitantes ativos e chat em tempo real para atendimento comercial.')

@php
    $bootstrapPayload = [
        'visitors' => $initialVisitors,
        'sessions' => $initialSessions,
        'stats' => $stats,
        'consultants_online' => $consultantsOnline,
    ];
@endphp

@push('head')
    <style>
        .chat-admin-grid {
            display: grid;
            grid-template-columns: 380px minmax(0, 1fr);
            gap: 12px;
            margin: 8px 0 30px;
        }

        .chat-admin-pane {
            border-radius: 20px;
            border: 1px solid rgba(22,20,19,.08);
            background:
                radial-gradient(circle at 94% 0%, rgba(198,161,74,.12), transparent 45%),
                linear-gradient(180deg, rgba(255,255,255,.95), rgba(255,255,255,.88));
            box-shadow:
                0 14px 24px rgba(12,10,8,.06),
                inset 0 1px 0 rgba(255,255,255,.74);
            overflow: hidden;
            min-height: 520px;
            display: grid;
            grid-template-rows: auto minmax(0, 1fr);
        }

        .chat-admin-head {
            padding: 12px 14px;
            border-bottom: 1px solid rgba(22,20,19,.07);
            display: grid;
            gap: 8px;
        }

        .chat-admin-head h2 {
            margin: 0;
            font-size: 1.03rem;
            line-height: 1.15;
        }

        .chat-admin-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .chat-admin-stat {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            border: 1px solid rgba(22,20,19,.08);
            padding: 6px 9px;
            background: rgba(255,255,255,.84);
            font-size: .74rem;
            color: #5f564c;
            font-weight: 700;
        }

        .chat-admin-stat strong {
            color: #1e1a16;
            font-size: .8rem;
        }

        .chat-admin-stat.online {
            border-color: rgba(15,138,95,.28);
            color: #0c7a52;
            background: rgba(15,138,95,.10);
        }

        .chat-admin-list {
            overflow: auto;
            padding: 10px;
            display: grid;
            gap: 8px;
            align-content: start;
        }

        .chat-admin-row {
            border-radius: 13px;
            border: 1px solid rgba(22,20,19,.08);
            background: rgba(255,255,255,.86);
            padding: 10px;
            display: grid;
            gap: 6px;
        }

        .chat-admin-row .head {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            align-items: center;
        }

        .chat-admin-row .name {
            font-size: .86rem;
            font-weight: 800;
            color: #1f1b17;
        }

        .chat-admin-row .meta {
            font-size: .72rem;
            color: #665d53;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .chat-admin-row .url {
            font-size: .73rem;
            color: #42506a;
            line-height: 1.4;
            word-break: break-word;
        }

        .chat-admin-journey {
            margin-top: 3px;
            padding-top: 7px;
            border-top: 1px dashed rgba(22,20,19,.12);
            display: grid;
            gap: 4px;
        }

        .chat-admin-journey-item {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            align-items: baseline;
            font-size: .68rem;
            color: #5f564c;
            line-height: 1.35;
        }

        .chat-admin-journey-item.current {
            color: #0f5df5;
            font-weight: 700;
        }

        .chat-admin-journey-path {
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .chat-admin-journey-meta {
            white-space: nowrap;
            color: #6d6358;
            font-variant-numeric: tabular-nums;
        }

        .chat-admin-pill {
            border-radius: 999px;
            border: 1px solid rgba(22,20,19,.08);
            background: rgba(255,255,255,.88);
            padding: 4px 8px;
            font-size: .67rem;
            font-weight: 700;
        }

        .chat-admin-pill.alert {
            border-color: rgba(179,38,30,.28);
            background: rgba(179,38,30,.10);
            color: #8d2c1f;
        }

        .chat-admin-main {
            display: grid;
            grid-template-columns: 320px minmax(0, 1fr);
            min-height: 520px;
        }

        .chat-admin-sessions {
            border-right: 1px solid rgba(22,20,19,.07);
            overflow: auto;
            padding: 10px;
            display: grid;
            gap: 8px;
            align-content: start;
        }

        .chat-session-item {
            border-radius: 13px;
            border: 1px solid rgba(22,20,19,.08);
            background: rgba(255,255,255,.86);
            padding: 10px;
            display: grid;
            gap: 6px;
            cursor: pointer;
        }

        .chat-session-item.active {
            border-color: rgba(198,161,74,.28);
            box-shadow: 0 10px 16px rgba(198,161,74,.16);
            background: linear-gradient(180deg, rgba(255,255,255,.96), rgba(249,243,232,.95));
        }

        .chat-session-item .name {
            font-size: .86rem;
            font-weight: 800;
            color: #1f1b17;
            line-height: 1.22;
        }

        .chat-session-item .tiny {
            font-size: .71rem;
            color: #675e54;
            line-height: 1.35;
        }

        .chat-conversation {
            display: grid;
            grid-template-rows: auto minmax(0, 1fr) auto;
            min-height: 520px;
        }

        .chat-conversation-head {
            padding: 12px 14px;
            border-bottom: 1px solid rgba(22,20,19,.07);
            display: grid;
            gap: 7px;
        }

        .chat-conversation-title {
            margin: 0;
            font-size: 1rem;
            line-height: 1.2;
        }

        .chat-conversation-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            font-size: .72rem;
            color: #665d53;
        }

        .chat-conversation-stream {
            overflow: auto;
            padding: 12px;
            display: grid;
            gap: 8px;
            align-content: start;
            background:
                radial-gradient(circle at 96% 0%, rgba(198,161,74,.08), transparent 46%),
                linear-gradient(180deg, rgba(255,255,255,.82), rgba(255,255,255,.86));
        }

        .chat-msg {
            max-width: min(560px, 92%);
            border-radius: 14px;
            padding: 9px 11px;
            display: grid;
            gap: 4px;
            font-size: .83rem;
            line-height: 1.44;
            border: 1px solid rgba(22,20,19,.08);
            background: rgba(255,255,255,.88);
        }

        .chat-msg.admin {
            margin-left: auto;
            border-color: rgba(31,94,255,.20);
            background: linear-gradient(180deg, rgba(31,94,255,.16), rgba(31,94,255,.08));
        }

        .chat-msg .meta {
            font-size: .67rem;
            color: #6a6258;
            display: flex;
            gap: 6px;
            align-items: center;
        }

        .chat-conversation-form {
            border-top: 1px solid rgba(22,20,19,.07);
            padding: 12px;
            display: grid;
            gap: 8px;
            background: rgba(255,255,255,.9);
        }

        .chat-empty {
            padding: 18px;
            color: #6a6157;
            font-size: .88rem;
        }

        @media (max-width: 1100px) {
            .chat-admin-grid {
                grid-template-columns: 1fr;
            }

            .chat-admin-main {
                grid-template-columns: 1fr;
                grid-template-rows: 230px minmax(0, 1fr);
            }

            .chat-admin-sessions {
                border-right: 0;
                border-bottom: 1px solid rgba(22,20,19,.07);
            }
        }
    </style>
@endpush

@section('content')
    <section class="hero hero-studio reveal-up" style="margin-bottom: 14px;">
        <div class="hero-studio-grid">
            <div class="hero-pane">
                <div class="stack-xl">
                    <div class="pill-list">
                        <span class="badge badge-brand">Atendimento em tempo real</span>
                        <span class="pill">Visitantes ativos</span>
                        <span class="pill">Chat consultivo</span>
                    </div>
                    <div class="stack">
                        <h1 class="text-gradient" style="font-size: clamp(1.8rem, 3vw, 2.8rem);">Central de atendimento online</h1>
                        <p class="lead">Acompanhe usuarios navegando no site, inicie conversas e responda visitantes em tempo real.</p>
                    </div>
                    <div class="hero-actions">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Voltar ao painel</a>
                        <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary">Mensagens de contato</a>
                    </div>
                </div>
            </div>
            <div class="hero-pane">
                <div class="metric-grid">
                    <div class="metric-card"><strong id="livechat_metric_visitors">{{ $stats['active_visitors'] }}</strong><span>Visitantes ativos</span></div>
                    <div class="metric-card"><strong id="livechat_metric_sessions">{{ $stats['open_sessions'] }}</strong><span>Sessoes abertas</span></div>
                    <div class="metric-card"><strong id="livechat_metric_waiting">{{ $stats['waiting_messages'] }}</strong><span>Aguardando retorno</span></div>
                </div>
                <div class="board-card stack" style="margin-top:12px;">
                    <div class="link-row">
                        <strong>Consultores online agora</strong>
                        <span id="livechat_consultants_online" class="badge">{{ $consultantsOnline }}</span>
                    </div>
                    <p class="small muted">Status calculado por atividade recente dos administradores no painel.</p>
                </div>
            </div>
        </div>
    </section>

    <section
        class="chat-admin-grid"
        data-livechat-admin
        data-bootstrap='@json($bootstrapPayload, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)'
        data-snapshot-url="{{ route('admin.livechat.data') }}"
        data-session-url-template="{{ route('admin.livechat.sessions.show', ['liveChatSession' => '__ID__']) }}"
        data-send-url-template="{{ route('admin.livechat.sessions.messages.store', ['liveChatSession' => '__ID__']) }}"
        data-close-url-template="{{ route('admin.livechat.sessions.close', ['liveChatSession' => '__ID__']) }}"
    >
        <aside class="chat-admin-pane">
            <header class="chat-admin-head">
                <h2>Visitantes ativos no site</h2>
                <div class="chat-admin-stats">
                    <span class="chat-admin-stat"><strong id="livechat_visitor_count">{{ $stats['active_visitors'] }}</strong> na navegacao</span>
                    <span class="chat-admin-stat online"><strong id="livechat_online_count">{{ $consultantsOnline }}</strong> consultor(es) online</span>
                </div>
            </header>
            <div id="livechat_visitors_list" class="chat-admin-list"></div>
        </aside>

        <section class="chat-admin-pane">
            <div class="chat-admin-main">
                <div id="livechat_sessions_list" class="chat-admin-sessions"></div>

                <div class="chat-conversation">
                    <header class="chat-conversation-head">
                        <h2 id="livechat_session_title" class="chat-conversation-title">Selecione uma sessao</h2>
                        <div id="livechat_session_meta" class="chat-conversation-meta"></div>
                    </header>

                    <div id="livechat_stream" class="chat-conversation-stream">
                        <div class="chat-empty">Clique em uma sessao para abrir a conversa.</div>
                    </div>

                    <form id="livechat_reply_form" class="chat-conversation-form" style="display:none;">
                        <textarea id="livechat_reply_body" class="textarea" placeholder="Digite sua resposta..." style="min-height:84px;" required></textarea>
                        <div style="display:flex; gap:8px; justify-content:space-between; flex-wrap:wrap;">
                            <button class="btn btn-primary" type="submit">Enviar resposta</button>
                            <button class="btn btn-secondary" type="button" id="livechat_close_session_btn">Encerrar sessao</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </section>
@endsection

@push('scripts')
<script>
    (function () {
        const root = document.querySelector('[data-livechat-admin]');
        if (!root) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const bootstrap = JSON.parse(root.getAttribute('data-bootstrap') || '{}');
        const snapshotUrl = root.getAttribute('data-snapshot-url') || '';
        const sessionUrlTemplate = root.getAttribute('data-session-url-template') || '';
        const sendUrlTemplate = root.getAttribute('data-send-url-template') || '';
        const closeUrlTemplate = root.getAttribute('data-close-url-template') || '';

        const visitorsList = document.getElementById('livechat_visitors_list');
        const sessionsList = document.getElementById('livechat_sessions_list');
        const stream = document.getElementById('livechat_stream');
        const titleEl = document.getElementById('livechat_session_title');
        const metaEl = document.getElementById('livechat_session_meta');
        const form = document.getElementById('livechat_reply_form');
        const bodyInput = document.getElementById('livechat_reply_body');
        const closeButton = document.getElementById('livechat_close_session_btn');

        const metricVisitors = document.getElementById('livechat_metric_visitors');
        const metricSessions = document.getElementById('livechat_metric_sessions');
        const metricWaiting = document.getElementById('livechat_metric_waiting');
        const consultantsOnlineBadge = document.getElementById('livechat_consultants_online');
        const visitorsCountBadge = document.getElementById('livechat_visitor_count');
        const consultantsInlineBadge = document.getElementById('livechat_online_count');

        const state = {
            visitors: Array.isArray(bootstrap.visitors) ? bootstrap.visitors : [],
            sessions: Array.isArray(bootstrap.sessions) ? bootstrap.sessions : [],
            selectedSessionId: null,
            messages: [],
            loadingSession: false,
        };

        const request = async (url, options = {}) => {
            const response = await fetch(url, {
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    ...(options.headers || {}),
                },
                ...options,
            });

            if (!response.ok) {
                const text = await response.text();
                throw new Error(text || 'request_failed');
            }

            return response.json();
        };

        const formatDuration = (seconds) => {
            if (seconds === null || seconds === undefined || Number.isNaN(Number(seconds))) return '--';
            const safe = Math.max(0, Number(seconds));
            const h = Math.floor(safe / 3600);
            const m = Math.floor((safe % 3600) / 60);
            const s = Math.floor(safe % 60);
            if (h > 0) return `${h}h ${String(m).padStart(2, '0')}m`;
            return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
        };

        const formatTime = (iso) => {
            if (!iso) return '--';
            const date = new Date(iso);
            if (Number.isNaN(date.getTime())) return '--';
            return date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
        };

        const formatDateTime = (iso) => {
            if (!iso) return '--';
            const date = new Date(iso);
            if (Number.isNaN(date.getTime())) return '--';
            return date.toLocaleString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
            });
        };

        const exitTypeLabel = (exitType) => {
            const map = {
                navigation: 'navegou',
                pagehide: 'saiu da aba',
                beforeunload: 'fechou/recarregou',
                inactivity: 'inatividade',
                manual: 'manual',
            };

            return map[exitType] || exitType || 'navegacao';
        };

        const createMetaPill = (label) => {
            const span = document.createElement('span');
            span.className = 'chat-admin-pill';
            span.textContent = label;
            return span;
        };

        const renderVisitors = () => {
            if (!visitorsList) return;
            visitorsList.innerHTML = '';

            if (!state.visitors.length) {
                const empty = document.createElement('div');
                empty.className = 'chat-empty';
                empty.textContent = 'Nenhum visitante ativo no momento.';
                visitorsList.appendChild(empty);
                return;
            }

            state.visitors.forEach((visitor) => {
                const row = document.createElement('article');
                row.className = 'chat-admin-row';

                const head = document.createElement('div');
                head.className = 'head';

                const name = document.createElement('strong');
                name.className = 'name';
                name.textContent = visitor.display_name || 'Visitante';
                head.appendChild(name);

                if (visitor.is_logged) {
                    const pill = createMetaPill('Logado');
                    head.appendChild(pill);
                }

                const meta = document.createElement('div');
                meta.className = 'meta';
                const ip = visitor.ip_address ? `IP ${visitor.ip_address}` : 'IP --';
                const pageTime = `Pagina ${formatDuration(visitor.page_seconds)}`;
                const sessionTime = `Sessao ${formatDuration(visitor.session_seconds)}`;
                const pageViews = visitor.page_views_in_session ? `PV ${visitor.page_views_in_session}` : null;
                const visits = visitor.visits_count ? `Visitas ${visitor.visits_count}` : null;
                const chats = visitor.chat_sessions_count ? `Chats ${visitor.chat_sessions_count}` : null;
                [ip, pageTime, sessionTime, pageViews, visits, chats].filter(Boolean).forEach((chunk) => {
                    const span = document.createElement('span');
                    span.textContent = chunk;
                    meta.appendChild(span);
                });

                if (visitor.email) {
                    const email = document.createElement('span');
                    email.textContent = visitor.email;
                    meta.appendChild(email);
                }

                const url = document.createElement('div');
                url.className = 'url';
                url.textContent = visitor.current_path || visitor.current_url || '/';

                const ref = document.createElement('div');
                ref.className = 'tiny';
                const source = visitor.referrer_url ? `Origem: ${visitor.referrer_url}` : 'Origem: direta';
                const utm = visitor.utm_source
                    ? ` • UTM ${visitor.utm_source}${visitor.utm_medium ? `/${visitor.utm_medium}` : ''}`
                    : '';
                ref.textContent = `${source}${utm}`;

                const device = document.createElement('div');
                device.className = 'tiny';
                device.textContent = [
                    visitor.device_type ? `Dispositivo: ${visitor.device_type}` : null,
                    visitor.browser ? `Navegador: ${visitor.browser}` : null,
                    visitor.platform ? `Plataforma: ${visitor.platform}` : null,
                ].filter(Boolean).join(' • ') || 'Dispositivo: --';

                const journey = Array.isArray(visitor.journey) ? visitor.journey : [];
                const journeyWrap = document.createElement('div');
                journeyWrap.className = 'chat-admin-journey';

                if (!journey.length) {
                    const emptyJourney = document.createElement('div');
                    emptyJourney.className = 'tiny';
                    emptyJourney.textContent = 'Fluxo ainda em coleta.';
                    journeyWrap.appendChild(emptyJourney);
                } else {
                    journey.forEach((step) => {
                        const rowStep = document.createElement('div');
                        rowStep.className = `chat-admin-journey-item ${step.is_current ? 'current' : ''}`;

                        const path = document.createElement('span');
                        path.className = 'chat-admin-journey-path';
                        path.textContent = step.path || '/';
                        path.title = step.url || step.path || '/';

                        const metaStep = document.createElement('span');
                        metaStep.className = 'chat-admin-journey-meta';
                        metaStep.textContent = step.is_current
                            ? `agora • ${formatDuration(step.duration_seconds)}`
                            : `${formatTime(step.entered_at)}-${formatTime(step.left_at)} • ${formatDuration(step.duration_seconds)} • ${exitTypeLabel(step.exit_type)}`;

                        rowStep.appendChild(path);
                        rowStep.appendChild(metaStep);
                        journeyWrap.appendChild(rowStep);
                    });
                }

                if (visitor.last_exit_path && visitor.last_exit_at) {
                    const exitSummary = document.createElement('div');
                    exitSummary.className = 'tiny';
                    exitSummary.textContent = `Ultima saida do site: ${visitor.last_exit_path} em ${formatDateTime(visitor.last_exit_at)} (${exitTypeLabel(visitor.last_exit_type)})`;
                    journeyWrap.appendChild(exitSummary);
                }

                row.appendChild(head);
                row.appendChild(meta);
                row.appendChild(url);
                row.appendChild(ref);
                row.appendChild(device);
                row.appendChild(journeyWrap);
                visitorsList.appendChild(row);
            });
        };

        const renderSessions = () => {
            if (!sessionsList) return;
            sessionsList.innerHTML = '';

            if (!state.sessions.length) {
                const empty = document.createElement('div');
                empty.className = 'chat-empty';
                empty.textContent = 'Sem sessoes abertas.';
                sessionsList.appendChild(empty);
                return;
            }

            state.sessions.forEach((session) => {
                const item = document.createElement('button');
                item.type = 'button';
                item.className = `chat-session-item ${Number(session.id) === Number(state.selectedSessionId) ? 'active' : ''}`;

                const name = document.createElement('strong');
                name.className = 'name';
                name.textContent = session.display_name || `Sessao #${session.id}`;

                const row1 = document.createElement('div');
                row1.className = 'tiny';
                row1.textContent = session.current_path || session.current_url || '/';

                const row2 = document.createElement('div');
                row2.className = 'tiny';
                row2.textContent = session.last_message_preview || 'Sem mensagens ainda.';

                const row3 = document.createElement('div');
                row3.className = 'tiny';
                row3.textContent = `Ultima atividade: ${formatTime(session.last_message_at)}`;

                item.appendChild(name);
                item.appendChild(row1);
                item.appendChild(row2);
                item.appendChild(row3);

                if (Number(session.unread_from_visitor || 0) > 0) {
                    const alert = createMetaPill(`${session.unread_from_visitor} nova(s)`);
                    alert.classList.add('alert');
                    item.appendChild(alert);
                }

                item.addEventListener('click', () => {
                    openSession(session.id);
                });

                sessionsList.appendChild(item);
            });
        };

        const renderConversation = () => {
            if (!stream) return;
            stream.innerHTML = '';

            if (!state.selectedSessionId) {
                const empty = document.createElement('div');
                empty.className = 'chat-empty';
                empty.textContent = 'Clique em uma sessao para abrir a conversa.';
                stream.appendChild(empty);
                if (form) form.style.display = 'none';
                return;
            }

            if (!state.messages.length) {
                const empty = document.createElement('div');
                empty.className = 'chat-empty';
                empty.textContent = 'Sem mensagens nesta sessao.';
                stream.appendChild(empty);
            } else {
                state.messages.forEach((message) => {
                    const article = document.createElement('article');
                    article.className = `chat-msg ${message.sender_role === 'admin' ? 'admin' : 'visitor'}`;

                    const body = document.createElement('div');
                    body.textContent = message.body || '';

                    const meta = document.createElement('div');
                    meta.className = 'meta';
                    meta.textContent = `${message.sender_role === 'admin' ? 'Consultor' : 'Visitante'} • ${formatTime(message.created_at)}`;
                    if (message.author_name) {
                        meta.textContent = `${message.author_name} • ${formatTime(message.created_at)}`;
                    }

                    article.appendChild(body);
                    article.appendChild(meta);
                    stream.appendChild(article);
                });
                stream.scrollTop = stream.scrollHeight;
            }

            if (form) form.style.display = 'grid';
        };

        const renderStats = (payload) => {
            const stats = payload?.stats || {};
            const consultantsOnline = Number(payload?.consultants_online || 0);

            if (metricVisitors) metricVisitors.textContent = String(stats.active_visitors ?? state.visitors.length);
            if (metricSessions) metricSessions.textContent = String(stats.open_sessions ?? state.sessions.length);
            if (metricWaiting) metricWaiting.textContent = String(stats.waiting_messages ?? 0);
            if (consultantsOnlineBadge) consultantsOnlineBadge.textContent = String(consultantsOnline);
            if (visitorsCountBadge) visitorsCountBadge.textContent = String(stats.active_visitors ?? state.visitors.length);
            if (consultantsInlineBadge) consultantsInlineBadge.textContent = String(consultantsOnline);
        };

        const fillSessionHeader = (session) => {
            if (!titleEl || !metaEl) return;
            if (!session) {
                titleEl.textContent = 'Selecione uma sessao';
                metaEl.innerHTML = '';
                return;
            }

            titleEl.textContent = session.display_name || `Sessao #${session.id}`;
            metaEl.innerHTML = '';

            [
                session.email ? `Email: ${session.email}` : null,
                session.phone ? `Telefone: ${session.phone}` : null,
                session.visitor_ip ? `IP: ${session.visitor_ip}` : null,
                session.current_path ? `Pagina: ${session.current_path}` : null,
                session.visitor_browser ? `Navegador: ${session.visitor_browser}` : null,
                session.visitor_device_type ? `Dispositivo: ${session.visitor_device_type}` : null,
                session.visitor_platform ? `Plataforma: ${session.visitor_platform}` : null,
                session.visitor_page_views_in_session ? `Pageviews: ${session.visitor_page_views_in_session}` : null,
                session.visitor_visits_count ? `Visitas: ${session.visitor_visits_count}` : null,
                session.visitor_utm_source ? `UTM: ${session.visitor_utm_source}${session.visitor_utm_medium ? `/${session.visitor_utm_medium}` : ''}` : null,
                session.last_message_at ? `Ultima: ${formatTime(session.last_message_at)}` : null,
            ].filter(Boolean).forEach((text) => {
                const span = document.createElement('span');
                span.textContent = text;
                metaEl.appendChild(span);
            });
        };

        const sessionUrl = (id) => sessionUrlTemplate.replace('__ID__', String(id));
        const sendUrl = (id) => sendUrlTemplate.replace('__ID__', String(id));
        const closeUrl = (id) => closeUrlTemplate.replace('__ID__', String(id));

        const openSession = async (sessionId) => {
            if (!sessionId || state.loadingSession) return;
            state.loadingSession = true;
            state.selectedSessionId = Number(sessionId);
            renderSessions();

            try {
                const payload = await request(sessionUrl(sessionId), { method: 'GET' });
                if (!payload?.ok) return;

                const session = payload.session || null;
                state.messages = Array.isArray(payload.messages) ? payload.messages : [];
                fillSessionHeader(session);
                renderConversation();
            } catch (error) {
                console.error(error);
            } finally {
                state.loadingSession = false;
            }
        };

        const refreshSnapshot = async () => {
            try {
                const payload = await request(snapshotUrl, { method: 'GET' });
                if (!payload?.ok) return;

                state.visitors = Array.isArray(payload.active_visitors) ? payload.active_visitors : [];
                state.sessions = Array.isArray(payload.sessions) ? payload.sessions : [];

                renderVisitors();
                renderSessions();
                renderStats(payload);

                if (state.selectedSessionId && !state.sessions.some((session) => Number(session.id) === Number(state.selectedSessionId))) {
                    state.selectedSessionId = null;
                    state.messages = [];
                    fillSessionHeader(null);
                    renderConversation();
                }
            } catch (error) {
                console.error(error);
            }
        };

        const refreshSelectedSession = async () => {
            if (!state.selectedSessionId) return;
            await openSession(state.selectedSessionId);
        };

        if (form && bodyInput) {
            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                if (!state.selectedSessionId) return;

                const body = (bodyInput.value || '').trim();
                if (!body) return;

                try {
                    const payload = await request(sendUrl(state.selectedSessionId), {
                        method: 'POST',
                        body: JSON.stringify({ body }),
                    });
                    if (!payload?.ok) return;

                    bodyInput.value = '';
                    await openSession(state.selectedSessionId);
                } catch (error) {
                    console.error(error);
                }
            });
        }

        if (closeButton) {
            closeButton.addEventListener('click', async () => {
                if (!state.selectedSessionId) return;
                try {
                    const payload = await request(closeUrl(state.selectedSessionId), {
                        method: 'PATCH',
                        body: JSON.stringify({}),
                    });
                    if (!payload?.ok) return;

                    state.selectedSessionId = null;
                    state.messages = [];
                    fillSessionHeader(null);
                    renderConversation();
                    await refreshSnapshot();
                } catch (error) {
                    console.error(error);
                }
            });
        }

        renderVisitors();
        renderSessions();
        renderConversation();
        renderStats(bootstrap);

        if (state.sessions.length) {
            openSession(state.sessions[0].id);
        }

        window.setInterval(refreshSnapshot, 5000);
        window.setInterval(refreshSelectedSession, 4000);
    })();
</script>
@endpush
