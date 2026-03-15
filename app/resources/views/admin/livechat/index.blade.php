@extends('layouts.store')

@section('title', 'Atendimento Online | Painel da Grafica')
@section('meta_description', 'Monitor de visitantes ativos e chat em tempo real para atendimento comercial.')

@php
    $bootstrapPayload = [
        'visitors' => $initialVisitors,
        'sessions' => $initialSessions,
        'tracking' => $initialTracking,
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

        .tracking-admin-wrap {
            margin: 8px 0 14px;
            display: grid;
            gap: 12px;
        }

        .tracking-admin-head {
            border-radius: 18px;
            border: 1px solid rgba(22,20,19,.08);
            background:
                radial-gradient(circle at 90% 0%, rgba(31,94,255,.11), transparent 44%),
                linear-gradient(180deg, rgba(255,255,255,.96), rgba(255,255,255,.9));
            box-shadow: 0 12px 20px rgba(12,10,8,.06);
            padding: 12px 14px;
            display: grid;
            gap: 4px;
        }

        .tracking-admin-head h2 {
            margin: 0;
            font-size: 1.04rem;
            color: #1f1b17;
            line-height: 1.2;
        }

        .tracking-admin-head p {
            margin: 0;
            color: #645b51;
            font-size: .79rem;
            line-height: 1.45;
        }

        .tracking-summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
        }

        .tracking-summary-card {
            border-radius: 14px;
            border: 1px solid rgba(22,20,19,.08);
            background: rgba(255,255,255,.9);
            padding: 10px 12px;
            display: grid;
            gap: 3px;
        }

        .tracking-summary-card strong {
            font-size: 1.28rem;
            color: #1f1b17;
            line-height: 1;
        }

        .tracking-summary-card span {
            font-size: .71rem;
            color: #6d6358;
            text-transform: uppercase;
            letter-spacing: .04em;
            font-weight: 700;
        }

        .tracking-charts-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .tracking-card {
            border-radius: 18px;
            border: 1px solid rgba(22,20,19,.08);
            background:
                radial-gradient(circle at 95% 0%, rgba(198,161,74,.12), transparent 47%),
                linear-gradient(180deg, rgba(255,255,255,.95), rgba(255,255,255,.88));
            box-shadow: 0 12px 20px rgba(12,10,8,.06);
            padding: 12px;
            display: grid;
            gap: 10px;
        }

        .tracking-card-wide {
            grid-column: 1 / -1;
        }

        .tracking-card h3 {
            margin: 0;
            font-size: .93rem;
            color: #1f1b17;
            line-height: 1.2;
        }

        .tracking-canvas-wrap {
            position: relative;
            min-height: 248px;
        }

        .tracking-active-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            gap: 7px;
        }

        .tracking-active-list li {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            align-items: center;
            border-radius: 10px;
            border: 1px solid rgba(22,20,19,.08);
            background: rgba(255,255,255,.88);
            padding: 7px 9px;
            font-size: .75rem;
            color: #5f564c;
        }

        .tracking-active-list .path {
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #1f1b17;
            font-weight: 700;
        }

        .tracking-active-list .count {
            border-radius: 999px;
            border: 1px solid rgba(22,20,19,.09);
            background: rgba(255,255,255,.86);
            padding: 3px 7px;
            font-size: .68rem;
            color: #5f564c;
            font-weight: 800;
            white-space: nowrap;
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

            .tracking-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .tracking-charts-grid {
                grid-template-columns: 1fr;
            }

            .tracking-card-wide {
                grid-column: auto;
            }

            .tracking-canvas-wrap {
                min-height: 220px;
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

    <section class="tracking-admin-wrap">
        <header class="tracking-admin-head">
            <h2>Tracking comportamental</h2>
            <p>Leitura em tempo real do fluxo de navegacao: entradas, saidas, paginas com mais interesse e pontos de abandono.</p>
        </header>

        <div class="tracking-summary-grid">
            <article class="tracking-summary-card">
                <strong id="livechat_tracking_active_now">0</strong>
                <span>Ativos agora</span>
            </article>
            <article class="tracking-summary-card">
                <strong id="livechat_tracking_views_24h">0</strong>
                <span>Pageviews 24h</span>
            </article>
            <article class="tracking-summary-card">
                <strong id="livechat_tracking_avg_duration">00:00</strong>
                <span>Tempo medio 24h</span>
            </article>
            <article class="tracking-summary-card">
                <strong id="livechat_tracking_exits_24h">0</strong>
                <span>Saidas 24h</span>
            </article>
        </div>

        <div class="tracking-charts-grid">
            <article class="tracking-card tracking-card-wide">
                <h3>Entradas e saidas por minuto (ultima hora)</h3>
                <div class="tracking-canvas-wrap">
                    <canvas id="livechat_tracking_timeline_chart" aria-label="Grafico de entradas e saidas"></canvas>
                </div>
            </article>

            <article class="tracking-card">
                <h3>Paginas mais vistas (24h)</h3>
                <div class="tracking-canvas-wrap">
                    <canvas id="livechat_tracking_top_paths_chart" aria-label="Grafico de paginas mais vistas"></canvas>
                </div>
            </article>

            <article class="tracking-card">
                <h3>Tipos de saida (24h)</h3>
                <div class="tracking-canvas-wrap">
                    <canvas id="livechat_tracking_exit_types_chart" aria-label="Grafico de tipos de saida"></canvas>
                </div>
            </article>

            <article class="tracking-card tracking-card-wide">
                <h3>Paginas ativas agora</h3>
                <ul id="livechat_tracking_active_paths" class="tracking-active-list">
                    <li><span class="path">Sem dados ainda</span><span class="count">0</span></li>
                </ul>
            </article>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
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

        const trackingActiveNow = document.getElementById('livechat_tracking_active_now');
        const trackingViews24h = document.getElementById('livechat_tracking_views_24h');
        const trackingAvgDuration = document.getElementById('livechat_tracking_avg_duration');
        const trackingExits24h = document.getElementById('livechat_tracking_exits_24h');
        const trackingTimelineCanvas = document.getElementById('livechat_tracking_timeline_chart');
        const trackingTopPathsCanvas = document.getElementById('livechat_tracking_top_paths_chart');
        const trackingExitTypesCanvas = document.getElementById('livechat_tracking_exit_types_chart');
        const trackingActivePathsList = document.getElementById('livechat_tracking_active_paths');

        const normalizeTracking = (payload) => {
            const summary = payload && typeof payload.summary === 'object' && payload.summary !== null ? payload.summary : {};
            const timeline = payload && typeof payload.timeline === 'object' && payload.timeline !== null ? payload.timeline : {};

            return {
                summary,
                timeline: {
                    labels: Array.isArray(timeline.labels) ? timeline.labels : [],
                    entries: Array.isArray(timeline.entries) ? timeline.entries : [],
                    exits: Array.isArray(timeline.exits) ? timeline.exits : [],
                },
                top_paths: Array.isArray(payload?.top_paths) ? payload.top_paths : [],
                active_paths: Array.isArray(payload?.active_paths) ? payload.active_paths : [],
                exit_types: Array.isArray(payload?.exit_types) ? payload.exit_types : [],
            };
        };

        const state = {
            visitors: Array.isArray(bootstrap.visitors) ? bootstrap.visitors : [],
            sessions: Array.isArray(bootstrap.sessions) ? bootstrap.sessions : [],
            tracking: normalizeTracking(bootstrap.tracking),
            selectedSessionId: null,
            messages: [],
            loadingSession: false,
            charts: {
                timeline: null,
                topPaths: null,
                exitTypes: null,
            },
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

        const numberFormatter = new Intl.NumberFormat('pt-BR');
        const formatNumber = (value) => numberFormatter.format(Math.max(0, Number(value) || 0));

        const shortPath = (value, max = 42) => {
            const path = String(value || '/');
            if (path.length <= max) return path;
            return `${path.slice(0, max - 1)}...`;
        };

        const normalizeSeries = (source, size) => {
            return Array.from({ length: size }, (_, index) => Math.max(0, Number(source[index] ?? 0) || 0));
        };

        const canRenderCharts = () => typeof window.Chart !== 'undefined';

        const renderTrackingSummary = (tracking) => {
            const summary = tracking?.summary || {};
            const activeNow = Number(summary.active_visitors_now || 0);
            const views24h = Number(summary.page_views_24h || 0);
            const avgDuration = Number(summary.avg_duration_seconds_24h || 0);
            const exits24h = Number(summary.exits_24h || 0);

            if (trackingActiveNow) trackingActiveNow.textContent = formatNumber(activeNow);
            if (trackingViews24h) trackingViews24h.textContent = formatNumber(views24h);
            if (trackingAvgDuration) trackingAvgDuration.textContent = formatDuration(avgDuration);
            if (trackingExits24h) trackingExits24h.textContent = formatNumber(exits24h);
        };

        const renderActivePaths = (tracking) => {
            if (!trackingActivePathsList) return;
            trackingActivePathsList.innerHTML = '';

            const rows = Array.isArray(tracking?.active_paths) ? tracking.active_paths.slice(0, 10) : [];
            if (!rows.length) {
                const empty = document.createElement('li');
                const path = document.createElement('span');
                path.className = 'path';
                path.textContent = 'Sem dados ainda';
                const count = document.createElement('span');
                count.className = 'count';
                count.textContent = '0';
                empty.appendChild(path);
                empty.appendChild(count);
                trackingActivePathsList.appendChild(empty);
                return;
            }

            rows.forEach((row) => {
                const item = document.createElement('li');
                const path = document.createElement('span');
                path.className = 'path';
                path.textContent = row.path || '/';
                path.title = row.path || '/';

                const count = document.createElement('span');
                count.className = 'count';
                count.textContent = `${formatNumber(row.active || 0)} ativo(s)`;

                item.appendChild(path);
                item.appendChild(count);
                trackingActivePathsList.appendChild(item);
            });
        };

        const upsertTimelineChart = (tracking) => {
            if (!trackingTimelineCanvas || !canRenderCharts()) return;

            const labels = Array.isArray(tracking?.timeline?.labels) ? tracking.timeline.labels : [];
            const entries = normalizeSeries(tracking?.timeline?.entries || [], labels.length);
            const exits = normalizeSeries(tracking?.timeline?.exits || [], labels.length);
            const data = {
                labels,
                datasets: [
                    {
                        label: 'Entradas',
                        data: entries,
                        borderColor: '#178160',
                        backgroundColor: 'rgba(23,129,96,.22)',
                        pointRadius: 0,
                        tension: .32,
                        fill: true,
                    },
                    {
                        label: 'Saidas',
                        data: exits,
                        borderColor: '#b33a2e',
                        backgroundColor: 'rgba(179,58,46,.16)',
                        pointRadius: 0,
                        tension: .32,
                        fill: true,
                    },
                ],
            };

            if (!state.charts.timeline) {
                state.charts.timeline = new window.Chart(trackingTimelineCanvas, {
                    type: 'line',
                    data,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: {
                                labels: {
                                    boxWidth: 14,
                                    color: '#4f463d',
                                },
                            },
                        },
                        scales: {
                            x: {
                                ticks: { color: '#70665b', maxTicksLimit: 12 },
                                grid: { color: 'rgba(22,20,19,.07)' },
                            },
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0, color: '#70665b' },
                                grid: { color: 'rgba(22,20,19,.07)' },
                            },
                        },
                    },
                });
                return;
            }

            state.charts.timeline.data = data;
            state.charts.timeline.update('none');
        };

        const upsertTopPathsChart = (tracking) => {
            if (!trackingTopPathsCanvas || !canRenderCharts()) return;

            const topPaths = Array.isArray(tracking?.top_paths) ? tracking.top_paths.slice(0, 8) : [];
            const labels = topPaths.map((row) => shortPath(row.path || '/'));
            const dataPoints = topPaths.map((row) => Math.max(0, Number(row.views || 0)));
            const data = {
                labels,
                datasets: [
                    {
                        label: 'Visualizacoes',
                        data: dataPoints,
                        borderRadius: 8,
                        backgroundColor: '#3c63f3',
                    },
                ],
            };

            if (!state.charts.topPaths) {
                state.charts.topPaths = new window.Chart(trackingTopPathsCanvas, {
                    type: 'bar',
                    data,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: {
                            legend: { display: false },
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: { precision: 0, color: '#70665b' },
                                grid: { color: 'rgba(22,20,19,.07)' },
                            },
                            y: {
                                ticks: { color: '#70665b' },
                                grid: { display: false },
                            },
                        },
                    },
                });
                return;
            }

            state.charts.topPaths.data = data;
            state.charts.topPaths.update('none');
        };

        const upsertExitTypesChart = (tracking) => {
            if (!trackingExitTypesCanvas || !canRenderCharts()) return;

            const rows = Array.isArray(tracking?.exit_types) ? tracking.exit_types : [];
            const labels = rows.map((row) => exitTypeLabel(row.type));
            const dataPoints = rows.map((row) => Math.max(0, Number(row.count || 0)));
            const data = {
                labels,
                datasets: [
                    {
                        data: dataPoints,
                        backgroundColor: [
                            '#3c63f3',
                            '#178160',
                            '#f18a1a',
                            '#b33a2e',
                            '#7a4df2',
                            '#1e9bb2',
                        ],
                        borderWidth: 1,
                        borderColor: 'rgba(255,255,255,.9)',
                    },
                ],
            };

            if (!state.charts.exitTypes) {
                state.charts.exitTypes = new window.Chart(trackingExitTypesCanvas, {
                    type: 'doughnut',
                    data,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    color: '#4f463d',
                                },
                            },
                        },
                    },
                });
                return;
            }

            state.charts.exitTypes.data = data;
            state.charts.exitTypes.update('none');
        };

        const renderTracking = (payload) => {
            const tracking = normalizeTracking(payload);
            state.tracking = tracking;

            renderTrackingSummary(tracking);
            renderActivePaths(tracking);
            upsertTimelineChart(tracking);
            upsertTopPathsChart(tracking);
            upsertExitTypesChart(tracking);
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
                renderTracking(payload.tracking);

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
        renderTracking(state.tracking);

        if (state.sessions.length) {
            openSession(state.sessions[0].id);
        }

        window.setInterval(refreshSnapshot, 5000);
        window.setInterval(refreshSelectedSession, 4000);
    })();
</script>
@endpush
