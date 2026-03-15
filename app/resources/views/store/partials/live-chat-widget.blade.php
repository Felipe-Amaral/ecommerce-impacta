@php
    $chatUser = auth()->user();
    $trackOnly = (bool) ($chatUser?->is_admin);
@endphp

<div
    class="livechat-floating{{ $trackOnly ? ' is-track-only' : '' }}"
    data-livechat-widget
    data-track-only="{{ $trackOnly ? '1' : '0' }}"
    data-heartbeat-url="{{ route('livechat.heartbeat') }}"
    data-poll-url="{{ route('livechat.poll') }}"
    data-open-url="{{ route('livechat.session.open') }}"
    data-send-url="{{ route('livechat.message.store') }}"
    data-offline-url="{{ route('livechat.message.offline') }}"
    data-contact-url="{{ route('pages.contact') }}"
    data-visitor-name="{{ $chatUser?->name ?? '' }}"
    data-visitor-email="{{ $chatUser?->email ?? '' }}"
    data-visitor-phone="{{ $chatUser?->phone ?? '' }}"
    data-open="0"
>
    @unless($trackOnly)
        <aside class="livechat-panel" data-livechat-panel aria-label="Atendimento online">
            <header class="livechat-head">
                <div class="livechat-head-copy">
                    <span class="livechat-kicker">Atendimento online</span>
                    <strong data-livechat-title>Fale com um consultor agora</strong>
                    <p data-livechat-status-text>Verificando consultores disponiveis...</p>
                </div>
                <button class="livechat-close" type="button" data-livechat-close aria-label="Fechar chat">
                    &times;
                </button>
            </header>

            <div class="livechat-thread-wrap">
                <div class="livechat-thread" data-livechat-thread aria-live="polite"></div>
                <div class="livechat-empty" data-livechat-empty>
                    Abra o chat para conversar em tempo real com nosso atendimento.
                </div>
            </div>

            <p class="livechat-feedback" data-livechat-feedback hidden></p>

            <form class="livechat-form" data-livechat-chat-form>
                <div class="livechat-identity-grid">
                    <label class="sr-only" for="livechat_name">Nome</label>
                    <input id="livechat_name" class="input" type="text" maxlength="120" placeholder="Nome (opcional)" data-livechat-name>
                    <label class="sr-only" for="livechat_email">E-mail</label>
                    <input id="livechat_email" class="input" type="email" maxlength="190" placeholder="E-mail (opcional)" data-livechat-email>
                </div>
                <label class="sr-only" for="livechat_message">Mensagem</label>
                <textarea
                    id="livechat_message"
                    class="textarea livechat-textarea"
                    rows="3"
                    maxlength="3000"
                    placeholder="Oi! Preciso de ajuda para escolher o produto ideal."
                    data-livechat-message
                    required
                ></textarea>
                <div class="livechat-actions">
                    <span class="tiny muted">Tempo real com consultor online.</span>
                    <button class="btn btn-primary btn-sm" type="submit">Enviar</button>
                </div>
            </form>

            <form class="livechat-form livechat-offline-form" data-livechat-offline-form hidden>
                <div class="livechat-identity-grid">
                    <label class="sr-only" for="livechat_offline_name">Nome</label>
                    <input id="livechat_offline_name" class="input" type="text" maxlength="120" placeholder="Seu nome" data-livechat-offline-name required>
                    <label class="sr-only" for="livechat_offline_email">E-mail</label>
                    <input id="livechat_offline_email" class="input" type="email" maxlength="190" placeholder="Seu e-mail" data-livechat-offline-email required>
                </div>
                <label class="sr-only" for="livechat_offline_phone">Telefone</label>
                <input id="livechat_offline_phone" class="input" type="text" maxlength="40" placeholder="Telefone / WhatsApp (opcional)" data-livechat-offline-phone>
                <label class="sr-only" for="livechat_offline_message">Mensagem</label>
                <textarea
                    id="livechat_offline_message"
                    class="textarea livechat-textarea"
                    rows="4"
                    maxlength="3000"
                    placeholder="No momento estamos offline. Deixe sua mensagem e retornamos."
                    data-livechat-offline-message
                    required
                ></textarea>
                <label class="livechat-consent">
                    <input type="checkbox" data-livechat-offline-consent required>
                    <span>Autorizo o uso dos dados para retorno comercial.</span>
                </label>
                <div class="livechat-actions">
                    <a class="btn btn-secondary btn-sm" href="{{ route('pages.contact') }}" data-livechat-contact-link>
                        Ir para contato
                    </a>
                    <button class="btn btn-primary btn-sm" type="submit">Deixar mensagem</button>
                </div>
            </form>
        </aside>

        <button
            type="button"
            class="livechat-launcher"
            data-livechat-launcher
            aria-expanded="false"
            aria-label="Abrir atendimento online"
        >
            <span class="livechat-launcher-icon" aria-hidden="true">
                @include('partials.nav-icon', ['name' => 'contact', 'class' => 'nav-icon'])
            </span>
            <span class="livechat-launcher-copy">
                <strong data-livechat-launcher-title>Fale com consultor agora</strong>
                <small data-livechat-launcher-subtitle>Carregando status...</small>
            </span>
            <span class="livechat-unread" data-livechat-unread hidden>0</span>
        </button>
    @endunless
</div>

@once
    <style>
        .livechat-floating {
            --livechat-right: 24px;
            --livechat-bottom: 28px;
            position: fixed;
            inset: auto var(--livechat-right) var(--livechat-bottom) auto;
            top: auto;
            left: auto;
            right: var(--livechat-right);
            bottom: var(--livechat-bottom);
            z-index: 110;
            display: grid;
            gap: 10px;
            justify-items: end;
            align-content: end;
            height: auto;
            width: min(390px, calc(100vw - 20px));
            pointer-events: none;
        }

        .livechat-floating.is-track-only {
            display: none;
        }

        @supports (right: clamp(10px, 2.2vw, 24px)) {
            .livechat-floating {
                --livechat-right: clamp(10px, 2.2vw, 24px);
                --livechat-bottom: clamp(14px, 2.5vw, 28px);
            }
        }

        .livechat-launcher {
            width: min(360px, calc(100vw - 20px));
            border: 1px solid rgba(255,255,255,.8);
            border-radius: 18px;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            color: #171310;
            background:
                radial-gradient(circle at 15% 0%, rgba(195,58,29,.17), transparent 58%),
                linear-gradient(180deg, rgba(255,255,255,.96), rgba(255,255,255,.9));
            box-shadow:
                0 16px 32px rgba(18,14,10,.18),
                inset 0 1px 0 rgba(255,255,255,.9);
            transition: transform .18s ease, box-shadow .18s ease;
            text-align: left;
            pointer-events: auto;
        }

        .livechat-launcher:hover {
            transform: translateY(-1px);
            box-shadow:
                0 18px 34px rgba(18,14,10,.22),
                inset 0 1px 0 rgba(255,255,255,.94);
        }

        .livechat-launcher-icon {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            color: #fff;
            background:
                linear-gradient(140deg, #b52b17 4%, #df5f25 58%, #ef9146 115%);
            box-shadow: 0 10px 18px rgba(181,43,23,.32);
            display: grid;
            place-items: center;
            flex: 0 0 38px;
        }

        .livechat-launcher-icon .nav-icon-svg {
            width: 21px;
            height: 21px;
        }

        .livechat-launcher-copy {
            min-width: 0;
            display: grid;
            gap: 1px;
            flex: 1;
        }

        .livechat-launcher-copy strong {
            font-size: .84rem;
            line-height: 1.2;
            color: #1f1a16;
        }

        .livechat-launcher-copy small {
            font-size: .73rem;
            color: #5f564b;
            line-height: 1.25;
        }

        .livechat-unread {
            flex: 0 0 auto;
            min-width: 22px;
            height: 22px;
            border-radius: 999px;
            display: inline-grid;
            place-items: center;
            background: #b3261e;
            color: #fff;
            font-size: .74rem;
            font-weight: 800;
            padding-inline: 6px;
            box-shadow: 0 8px 16px rgba(179,38,30,.32);
        }

        .livechat-floating[data-open="1"] .livechat-launcher {
            transform: translateY(0);
            box-shadow:
                0 14px 28px rgba(18,14,10,.15),
                inset 0 1px 0 rgba(255,255,255,.92);
        }

        .livechat-panel {
            position: absolute;
            right: 0;
            bottom: calc(100% + 10px);
            width: min(390px, calc(100vw - 20px));
            max-height: min(78vh, 640px);
            border-radius: 22px;
            border: 1px solid rgba(255,255,255,.84);
            overflow: hidden;
            display: grid;
            grid-template-rows: auto minmax(120px, 1fr) auto auto auto;
            background:
                radial-gradient(circle at 100% 0%, rgba(15,93,245,.16), transparent 48%),
                radial-gradient(circle at 0% 100%, rgba(195,58,29,.16), transparent 52%),
                linear-gradient(180deg, rgba(255,255,255,.95), rgba(255,255,255,.9));
            box-shadow: 0 22px 40px rgba(14,11,8,.24);
            transform: translateY(12px) scale(.985);
            transform-origin: bottom right;
            opacity: 0;
            pointer-events: none;
            transition: transform .2s ease, opacity .2s ease;
        }

        .livechat-floating[data-open="1"] .livechat-panel {
            transform: translateY(0) scale(1);
            opacity: 1;
            pointer-events: auto;
        }

        .livechat-head {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: flex-start;
            padding: 14px 14px 10px;
            border-bottom: 1px solid rgba(22,20,19,.08);
            background: rgba(255,255,255,.7);
        }

        .livechat-head-copy {
            display: grid;
            gap: 2px;
        }

        .livechat-kicker {
            text-transform: uppercase;
            letter-spacing: .08em;
            font-size: .66rem;
            color: #8d2c1f;
            font-weight: 800;
        }

        .livechat-head-copy strong {
            font-size: .94rem;
            line-height: 1.2;
            color: #1f1a16;
        }

        .livechat-head-copy p {
            margin: 0;
            font-size: .75rem;
            color: #645a4f;
            line-height: 1.32;
        }

        .livechat-close {
            width: 30px;
            height: 30px;
            border-radius: 10px;
            border: 1px solid rgba(22,20,19,.12);
            background: rgba(255,255,255,.84);
            cursor: pointer;
            font-size: 1.15rem;
            line-height: 1;
            color: #51463b;
        }

        .livechat-thread-wrap {
            position: relative;
            min-height: 140px;
        }

        .livechat-thread {
            overflow: auto;
            max-height: min(48vh, 360px);
            padding: 12px;
            display: grid;
            align-content: start;
            gap: 8px;
            background:
                radial-gradient(circle at 96% 0%, rgba(15,93,245,.08), transparent 46%),
                linear-gradient(180deg, rgba(255,255,255,.84), rgba(255,255,255,.88));
        }

        .livechat-empty {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            text-align: center;
            padding: 18px;
            color: #665c52;
            font-size: .82rem;
            line-height: 1.4;
            pointer-events: none;
        }

        .livechat-message {
            max-width: min(270px, 86%);
            padding: 8px 10px;
            border-radius: 14px;
            border: 1px solid rgba(22,20,19,.08);
            background: rgba(255,255,255,.9);
            box-shadow: 0 8px 14px rgba(12,10,8,.05);
            display: grid;
            gap: 4px;
        }

        .livechat-message.mine {
            margin-left: auto;
            border-color: rgba(31,94,255,.24);
            background: linear-gradient(180deg, rgba(31,94,255,.16), rgba(31,94,255,.08));
        }

        .livechat-message.system {
            margin-inline: auto;
            max-width: 92%;
            text-align: center;
            color: #5f564c;
            font-size: .73rem;
            background: rgba(241,233,220,.84);
        }

        .livechat-message-meta {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            font-size: .65rem;
            color: #675e54;
        }

        .livechat-message-meta strong {
            font-size: .67rem;
            color: #1f1a16;
        }

        .livechat-message-body {
            font-size: .8rem;
            line-height: 1.42;
            color: #201a16;
            word-break: break-word;
        }

        .livechat-feedback {
            margin: 0 12px;
            border-radius: 10px;
            border: 1px solid rgba(22,20,19,.09);
            background: rgba(255,255,255,.82);
            color: #5f564c;
            font-size: .73rem;
            line-height: 1.35;
            padding: 8px 9px;
        }

        .livechat-feedback[data-tone="error"] {
            border-color: rgba(179,38,30,.25);
            color: #8d2c1f;
            background: rgba(179,38,30,.10);
        }

        .livechat-feedback[data-tone="success"] {
            border-color: rgba(15,138,95,.24);
            color: #0e6f4c;
            background: rgba(15,138,95,.10);
        }

        .livechat-form {
            border-top: 1px solid rgba(22,20,19,.08);
            padding: 10px 12px 12px;
            display: grid;
            gap: 8px;
            background: rgba(255,255,255,.92);
        }

        .livechat-form[hidden],
        .livechat-offline-form[hidden],
        .livechat-invite[hidden],
        .livechat-feedback[hidden],
        .livechat-unread[hidden],
        .livechat-empty[hidden] {
            display: none !important;
        }

        .livechat-identity-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .livechat-textarea {
            min-height: 82px;
            max-height: 150px;
        }

        .livechat-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .livechat-consent {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            font-size: .72rem;
            color: #645a4f;
            line-height: 1.35;
        }

        .livechat-consent input {
            margin-top: 2px;
        }

        .livechat-invite {
            width: min(330px, calc(100vw - 20px));
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,.88);
            padding: 11px 12px 12px;
            display: grid;
            gap: 8px;
            background:
                radial-gradient(circle at 4% 0%, rgba(195,58,29,.16), transparent 52%),
                linear-gradient(180deg, rgba(255,255,255,.96), rgba(255,255,255,.9));
            box-shadow: 0 16px 28px rgba(14,11,8,.18);
            position: relative;
            animation: livechatInviteIn .32s ease;
        }

        .livechat-invite strong {
            font-size: .86rem;
            color: #1f1a16;
        }

        .livechat-invite p {
            margin: 0;
            font-size: .75rem;
            line-height: 1.35;
            color: #62584e;
        }

        .livechat-invite-close {
            position: absolute;
            top: 6px;
            right: 7px;
            width: 24px;
            height: 24px;
            border-radius: 8px;
            border: 1px solid rgba(22,20,19,.11);
            background: rgba(255,255,255,.84);
            cursor: pointer;
            color: #51463b;
            font-size: 1rem;
            line-height: 1;
        }

        @keyframes livechatInviteIn {
            from {
                opacity: 0;
                transform: translateY(6px) scale(.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @media (max-width: 760px) {
            .livechat-floating {
                --livechat-right: 8px;
                --livechat-bottom: calc(env(safe-area-inset-bottom, 0px) + 82px);
                width: calc(100vw - 16px);
            }

            .livechat-panel,
            .livechat-launcher,
            .livechat-invite {
                width: calc(100vw - 16px);
            }

            .livechat-thread {
                max-height: min(46vh, 320px);
            }
        }
    </style>
@endonce

@push('scripts')
    <script>
        (function () {
            const root = document.querySelector('[data-livechat-widget]');
            if (!root) return;

            const trackOnly = root.getAttribute('data-track-only') === '1';
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            const heartbeatUrl = root.getAttribute('data-heartbeat-url') || '';
            const pollUrl = root.getAttribute('data-poll-url') || '';
            const openUrl = root.getAttribute('data-open-url') || '';
            const sendUrl = root.getAttribute('data-send-url') || '';
            const offlineUrl = root.getAttribute('data-offline-url') || '';
            const contactUrl = root.getAttribute('data-contact-url') || '';

            const storage = {
                get(scope, key) {
                    try {
                        return scope.getItem(key) || '';
                    } catch (error) {
                        return '';
                    }
                },
                set(scope, key, value) {
                    try {
                        scope.setItem(key, value);
                    } catch (error) {
                        // ignore storage quota/privacy errors
                    }
                },
            };

            const randomString = (length) => {
                const alphabet = 'abcdefghijklmnopqrstuvwxyz0123456789';
                const bytes = new Uint8Array(length);
                if (window.crypto?.getRandomValues) {
                    window.crypto.getRandomValues(bytes);
                } else {
                    for (let index = 0; index < length; index += 1) {
                        bytes[index] = Math.floor(Math.random() * 255);
                    }
                }

                let output = '';
                for (let index = 0; index < bytes.length; index += 1) {
                    output += alphabet[bytes[index] % alphabet.length];
                }

                return output;
            };

            const tokenKey = 'uriah_livechat_token_v1';
            const inviteDismissKey = 'uriah_livechat_invite_dismissed';
            const browserSessionKey = 'uriah_livechat_browser_session';
            const landingUrlKey = 'uriah_livechat_landing_url';
            const pageViewsKey = 'uriah_livechat_pageviews_session';
            const visitsCountKey = 'uriah_livechat_visits_count';

            let visitorToken = storage.get(window.localStorage, tokenKey);
            if (!visitorToken || visitorToken.length < 18) {
                visitorToken = 'vst_' + randomString(28);
                storage.set(window.localStorage, tokenKey, visitorToken);
            }

            let browserSessionId = storage.get(window.sessionStorage, browserSessionKey);
            const isNewBrowserSession = !browserSessionId;
            if (!browserSessionId) {
                browserSessionId = 'sid_' + randomString(22);
                storage.set(window.sessionStorage, browserSessionKey, browserSessionId);
            }

            let landingUrl = storage.get(window.sessionStorage, landingUrlKey);
            if (!landingUrl) {
                landingUrl = window.location.href;
                storage.set(window.sessionStorage, landingUrlKey, landingUrl);
            }

            const pageViews = Math.max(1, Number(storage.get(window.sessionStorage, pageViewsKey) || '0') + 1);
            storage.set(window.sessionStorage, pageViewsKey, String(pageViews));

            let visitsCount = Math.max(0, Number(storage.get(window.localStorage, visitsCountKey) || '0'));
            if (isNewBrowserSession) {
                visitsCount += 1;
                storage.set(window.localStorage, visitsCountKey, String(visitsCount));
            }

            let referrerHost = '';
            try {
                referrerHost = document.referrer ? new URL(document.referrer).hostname : '';
            } catch (error) {
                referrerHost = '';
            }

            const query = new URLSearchParams(window.location.search);

            const userAgent = navigator.userAgent || '';
            const browserName = /edg/i.test(userAgent)
                ? 'Edge'
                : /chrome|crios/i.test(userAgent)
                    ? 'Chrome'
                    : /safari/i.test(userAgent) && !/chrome|crios/i.test(userAgent)
                        ? 'Safari'
                        : /firefox|fxios/i.test(userAgent)
                            ? 'Firefox'
                            : /opr|opera/i.test(userAgent)
                                ? 'Opera'
                                : 'Outro';

            const deviceType = /mobile|iphone|ipod|android.+mobile|windows phone/i.test(userAgent)
                ? 'mobile'
                : /ipad|tablet|android(?!.*mobile)/i.test(userAgent)
                    ? 'tablet'
                    : 'desktop';

            const state = {
                visitorToken,
                browserSessionId,
                landingUrl,
                sessionId: null,
                pageStartedAt: Math.floor(Date.now() / 1000),
                panelOpen: false,
                consultantsOnline: 0,
                isConsultantOnline: false,
                lastMessageId: 0,
                messages: [],
                unreadCount: 0,
                visitorName: root.getAttribute('data-visitor-name') || '',
                visitorEmail: root.getAttribute('data-visitor-email') || '',
                visitorPhone: root.getAttribute('data-visitor-phone') || '',
                pageViewsInSession: pageViews,
                visitsCount,
                browserName,
                deviceType,
                platform: navigator.platform || '',
                referrerHost,
                utmSource: query.get('utm_source') || '',
                utmMedium: query.get('utm_medium') || '',
                utmCampaign: query.get('utm_campaign') || '',
                utmTerm: query.get('utm_term') || '',
                utmContent: query.get('utm_content') || '',
                inviteDismissed: storage.get(window.sessionStorage, inviteDismissKey) === '1',
                inviteTimer: null,
                loadingOpen: false,
            };

            const panel = root.querySelector('[data-livechat-panel]');
            const launcher = root.querySelector('[data-livechat-launcher]');
            const launcherTitle = root.querySelector('[data-livechat-launcher-title]');
            const launcherSubtitle = root.querySelector('[data-livechat-launcher-subtitle]');
            const unreadBadge = root.querySelector('[data-livechat-unread]');
            const closeButton = root.querySelector('[data-livechat-close]');
            const statusText = root.querySelector('[data-livechat-status-text]');
            const thread = root.querySelector('[data-livechat-thread]');
            const empty = root.querySelector('[data-livechat-empty]');
            const feedback = root.querySelector('[data-livechat-feedback]');
            const chatForm = root.querySelector('[data-livechat-chat-form]');
            const nameInput = root.querySelector('[data-livechat-name]');
            const emailInput = root.querySelector('[data-livechat-email]');
            const messageInput = root.querySelector('[data-livechat-message]');
            const offlineForm = root.querySelector('[data-livechat-offline-form]');
            const offlineNameInput = root.querySelector('[data-livechat-offline-name]');
            const offlineEmailInput = root.querySelector('[data-livechat-offline-email]');
            const offlinePhoneInput = root.querySelector('[data-livechat-offline-phone]');
            const offlineMessageInput = root.querySelector('[data-livechat-offline-message]');
            const offlineConsentInput = root.querySelector('[data-livechat-offline-consent]');
            const contactLink = root.querySelector('[data-livechat-contact-link]');
            const invite = root.querySelector('[data-livechat-invite]');
            const inviteDismiss = root.querySelector('[data-livechat-invite-dismiss]');
            const inviteOpen = root.querySelector('[data-livechat-invite-open]');

            const nowIsoTime = (value) => {
                if (!value) return '--:--';
                const date = new Date(value);
                if (Number.isNaN(date.getTime())) return '--:--';
                return date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
            };

            const escapeHtml = (value) => String(value || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');

            const nl2br = (value) => escapeHtml(value).replace(/\n/g, '<br>');

            const requestJson = async (url, payload) => {
                const response = await fetch(url, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(payload || {}),
                });

                const text = await response.text();
                let data = {};
                if (text && text.trim() !== '') {
                    try {
                        data = JSON.parse(text);
                    } catch (error) {
                        data = {};
                    }
                }

                if (!response.ok) {
                    const message = typeof data?.message === 'string' && data.message !== ''
                        ? data.message
                        : 'Nao foi possivel concluir esta acao agora.';
                    throw new Error(message);
                }

                return data;
            };

            const syncIdentityInputs = () => {
                if (nameInput && !nameInput.value) nameInput.value = state.visitorName || '';
                if (emailInput && !emailInput.value) emailInput.value = state.visitorEmail || '';
                if (offlineNameInput && !offlineNameInput.value) offlineNameInput.value = state.visitorName || '';
                if (offlineEmailInput && !offlineEmailInput.value) offlineEmailInput.value = state.visitorEmail || '';
                if (offlinePhoneInput && !offlinePhoneInput.value) offlinePhoneInput.value = state.visitorPhone || '';
            };

            const setFeedback = (text, tone = 'muted') => {
                if (!feedback) return;
                const safeText = String(text || '').trim();
                if (safeText === '') {
                    feedback.hidden = true;
                    feedback.textContent = '';
                    feedback.removeAttribute('data-tone');
                    return;
                }

                feedback.hidden = false;
                feedback.textContent = safeText;
                feedback.setAttribute('data-tone', tone);
            };

            const setStatus = (consultantsOnline, isConsultantOnline) => {
                const onlineCount = Number(consultantsOnline || 0);
                state.consultantsOnline = onlineCount;
                state.isConsultantOnline = Boolean(isConsultantOnline || onlineCount > 0);

                if (trackOnly) return;

                if (statusText) {
                    statusText.textContent = state.isConsultantOnline
                        ? `${onlineCount} consultor(es) online agora. Fale com a equipe em tempo real.`
                        : 'No momento estamos offline. Deixe uma mensagem e retornamos pelo contato informado.';
                }

                if (launcherTitle) {
                    launcherTitle.textContent = state.isConsultantOnline
                        ? 'Fale com consultor agora'
                        : 'Deixe sua mensagem';
                }

                if (launcherSubtitle) {
                    launcherSubtitle.textContent = state.isConsultantOnline
                        ? `Online agora (${onlineCount})`
                        : 'Offline no momento';
                }

                if (chatForm) {
                    chatForm.hidden = !state.isConsultantOnline;
                }

                if (offlineForm) {
                    offlineForm.hidden = state.isConsultantOnline;
                }

                if (contactLink && !contactLink.getAttribute('href')) {
                    contactLink.setAttribute('href', contactUrl);
                }

                if (state.isConsultantOnline && !state.panelOpen) {
                    scheduleInvite();
                }
            };

            const messageAuthor = (message) => {
                if (message?.author_name) return String(message.author_name);
                if (message?.sender_role === 'admin') return 'Consultor';
                if (message?.sender_role === 'visitor') return 'Voce';
                return 'Sistema';
            };

            const renderMessages = () => {
                if (!thread || !empty) return;
                thread.innerHTML = '';

                if (!state.messages.length) {
                    empty.hidden = false;
                    return;
                }

                empty.hidden = true;

                state.messages.forEach((message) => {
                    const item = document.createElement('article');
                    item.className = [
                        'livechat-message',
                        message.sender_role === 'visitor' ? 'mine' : '',
                        message.sender_role === 'system' ? 'system' : '',
                    ].filter(Boolean).join(' ');

                    const author = escapeHtml(messageAuthor(message));
                    const body = nl2br(message.body || '');
                    const time = escapeHtml(nowIsoTime(message.created_at));

                    item.innerHTML = `
                        <div class="livechat-message-meta">
                            <strong>${author}</strong>
                            <span>${time}</span>
                        </div>
                        <div class="livechat-message-body">${body}</div>
                    `;

                    thread.appendChild(item);
                });

                thread.scrollTop = thread.scrollHeight;
            };

            const updateUnreadBadge = () => {
                if (!unreadBadge) return;
                if (state.unreadCount <= 0) {
                    unreadBadge.hidden = true;
                    unreadBadge.textContent = '0';
                    return;
                }

                unreadBadge.hidden = false;
                unreadBadge.textContent = state.unreadCount > 9 ? '9+' : String(state.unreadCount);
            };

            const absorbMessages = (messages, markAsUnreadWhenClosed = true) => {
                if (!Array.isArray(messages) || messages.length === 0) return;

                let changed = false;
                messages.forEach((entry) => {
                    if (!entry || !entry.id) return;
                    const id = Number(entry.id);
                    if (state.messages.some((message) => Number(message.id) === id)) return;
                    state.messages.push(entry);
                    state.lastMessageId = Math.max(state.lastMessageId, id);
                    changed = true;

                    if (
                        markAsUnreadWhenClosed &&
                        !state.panelOpen &&
                        entry.sender_role === 'admin'
                    ) {
                        state.unreadCount += 1;
                    }
                });

                if (!changed) return;
                state.messages.sort((left, right) => Number(left.id) - Number(right.id));
                renderMessages();
                updateUnreadBadge();
            };

            const basePayload = () => ({
                visitor_token: state.visitorToken,
                current_url: window.location.href,
                current_path: window.location.pathname + window.location.search,
                metadata: {
                    page_started_at: state.pageStartedAt,
                    visitor_name: state.visitorName || undefined,
                    visitor_email: state.visitorEmail || undefined,
                    browser: state.browserName || undefined,
                    device_type: state.deviceType || undefined,
                    platform: state.platform || undefined,
                    page_views_in_session: state.pageViewsInSession,
                    visits_count: state.visitsCount,
                    referrer_host: state.referrerHost || undefined,
                    utm_source: state.utmSource || undefined,
                    utm_medium: state.utmMedium || undefined,
                    utm_campaign: state.utmCampaign || undefined,
                    utm_term: state.utmTerm || undefined,
                    utm_content: state.utmContent || undefined,
                },
            });

            const heartbeat = async () => {
                const payload = {
                    ...basePayload(),
                    landing_url: state.landingUrl,
                    referrer_url: document.referrer || null,
                    page_title: document.title || null,
                    timezone: Intl.DateTimeFormat().resolvedOptions().timeZone || null,
                    language: navigator.language || null,
                    screen_size: `${window.innerWidth}x${window.innerHeight}`,
                    session_id: state.browserSessionId,
                    panel_open: state.panelOpen,
                };

                const data = await requestJson(heartbeatUrl, payload);
                if (data?.session?.id) {
                    state.sessionId = Number(data.session.id);
                }

                setStatus(data?.consultants_online, data?.is_consultant_online);
            };

            const poll = async () => {
                if (!state.sessionId && !state.panelOpen) return;

                const payload = {
                    ...basePayload(),
                    session_id: state.sessionId || undefined,
                    last_message_id: state.lastMessageId || 0,
                    panel_open: state.panelOpen,
                };

                const data = await requestJson(pollUrl, payload);
                if (data?.session?.id) {
                    state.sessionId = Number(data.session.id);
                }

                setStatus(data?.consultants_online, data?.is_consultant_online);
                absorbMessages(Array.isArray(data?.messages) ? data.messages : []);
            };

            const hideInvite = () => {
                if (!invite) return;
                if (state.inviteTimer) {
                    window.clearTimeout(state.inviteTimer);
                    state.inviteTimer = null;
                }
                invite.hidden = true;
            };

            const scheduleInvite = () => {
                if (!invite || state.inviteDismissed || state.panelOpen || !state.isConsultantOnline) return;
                if (!invite.hidden || state.inviteTimer) return;
                state.inviteTimer = window.setTimeout(() => {
                    state.inviteTimer = null;
                    if (state.inviteDismissed || state.panelOpen || !state.isConsultantOnline) return;
                    invite.hidden = false;
                }, 7000);
            };

            const setPanelOpen = (open) => {
                state.panelOpen = Boolean(open);
                root.setAttribute('data-open', state.panelOpen ? '1' : '0');
                if (launcher) {
                    launcher.setAttribute('aria-expanded', state.panelOpen ? 'true' : 'false');
                }
                if (state.panelOpen) {
                    hideInvite();
                    state.unreadCount = 0;
                    updateUnreadBadge();
                }
            };

            const ensureSessionLoaded = async () => {
                if (state.loadingOpen) return;
                state.loadingOpen = true;

                try {
                    const payload = {
                        ...basePayload(),
                        visitor_name: state.visitorName || undefined,
                        visitor_email: state.visitorEmail || undefined,
                        visitor_phone: state.visitorPhone || undefined,
                    };
                    const data = await requestJson(openUrl, payload);
                    if (data?.session?.id) {
                        state.sessionId = Number(data.session.id);
                    }
                    if (Array.isArray(data?.messages)) {
                        absorbMessages(data.messages, false);
                    }
                    if (data?.message) {
                        absorbMessages([data.message], false);
                    }
                } finally {
                    state.loadingOpen = false;
                }
            };

            const sendMessage = async () => {
                if (!messageInput) return;
                const message = String(messageInput.value || '').trim();
                if (message === '') return;

                const payload = {
                    ...basePayload(),
                    session_id: state.sessionId || undefined,
                    message,
                    visitor_name: state.visitorName || undefined,
                    visitor_email: state.visitorEmail || undefined,
                    visitor_phone: state.visitorPhone || undefined,
                };

                const data = await requestJson(sendUrl, payload);
                if (data?.session?.id) {
                    state.sessionId = Number(data.session.id);
                }
                if (data?.message) {
                    absorbMessages([data.message], false);
                }

                messageInput.value = '';
                setFeedback('', 'muted');
            };

            const sendOfflineMessage = async () => {
                if (!offlineNameInput || !offlineEmailInput || !offlineMessageInput || !offlineConsentInput) return;

                const payload = {
                    ...basePayload(),
                    name: String(offlineNameInput.value || '').trim(),
                    email: String(offlineEmailInput.value || '').trim(),
                    phone: String(offlinePhoneInput?.value || '').trim(),
                    message: String(offlineMessageInput.value || '').trim(),
                    lgpd_consent: Boolean(offlineConsentInput.checked),
                };

                const data = await requestJson(offlineUrl, payload);
                if (data?.session?.id) {
                    state.sessionId = Number(data.session.id);
                }
                if (data?.message) {
                    absorbMessages([data.message], false);
                }

                if (contactLink && data?.contact_url) {
                    contactLink.setAttribute('href', data.contact_url);
                }

                if (offlineMessageInput) offlineMessageInput.value = '';
                setFeedback('Mensagem recebida. Nossa equipe retornara pelo contato informado.', 'success');
            };

            const bindIdentitySync = (input, field) => {
                if (!input) return;
                input.addEventListener('input', () => {
                    const value = String(input.value || '').trim();
                    if (field === 'name') state.visitorName = value;
                    if (field === 'email') state.visitorEmail = value;
                    if (field === 'phone') state.visitorPhone = value;

                    if (field === 'name') {
                        if (nameInput && nameInput !== input) nameInput.value = value;
                        if (offlineNameInput && offlineNameInput !== input) offlineNameInput.value = value;
                    }
                    if (field === 'email') {
                        if (emailInput && emailInput !== input) emailInput.value = value;
                        if (offlineEmailInput && offlineEmailInput !== input) offlineEmailInput.value = value;
                    }
                    if (field === 'phone') {
                        if (offlinePhoneInput && offlinePhoneInput !== input) offlinePhoneInput.value = value;
                    }
                });
            };

            const runHeartbeatSafely = async () => {
                try {
                    await heartbeat();
                } catch (error) {
                    if (!trackOnly) {
                        setFeedback('Conexao instavel. Tentando reconectar...', 'error');
                    }
                }
            };

            const runPollSafely = async () => {
                try {
                    await poll();
                } catch (error) {
                    // keep silent to avoid noisy UX on temporary failures
                }
            };

            let exitSignalSent = false;
            let exitSignalAtMs = 0;

            const sendExitSignal = (reason = 'pagehide') => {
                const nowMs = Date.now();
                if (exitSignalSent || (nowMs - exitSignalAtMs) < 900) return;
                exitSignalSent = true;
                exitSignalAtMs = nowMs;

                const payload = {
                    ...basePayload(),
                    current_url: window.location.href,
                    current_path: window.location.pathname + window.location.search,
                    session_id: state.browserSessionId,
                    panel_open: false,
                    metadata: {
                        ...basePayload().metadata,
                        exit_event: true,
                        exit_reason: reason,
                    },
                };

                fetch(heartbeatUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    keepalive: true,
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(payload),
                }).catch(() => {
                    // ignore unload failures
                });
            };

            window.addEventListener('pagehide', () => sendExitSignal('pagehide'));
            window.addEventListener('beforeunload', () => sendExitSignal('beforeunload'));

            if (trackOnly) {
                runHeartbeatSafely();
                window.setInterval(runHeartbeatSafely, 15000);
                document.addEventListener('visibilitychange', () => {
                    if (!document.hidden) runHeartbeatSafely();
                });
                return;
            }

            syncIdentityInputs();
            bindIdentitySync(nameInput, 'name');
            bindIdentitySync(emailInput, 'email');
            bindIdentitySync(offlineNameInput, 'name');
            bindIdentitySync(offlineEmailInput, 'email');
            bindIdentitySync(offlinePhoneInput, 'phone');

            if (launcher) {
                launcher.addEventListener('click', async () => {
                    const next = !state.panelOpen;
                    setPanelOpen(next);
                    if (!next) return;

                    try {
                        await ensureSessionLoaded();
                        await runPollSafely();
                    } catch (error) {
                        setFeedback('Nao foi possivel abrir o chat agora.', 'error');
                    }
                });
            }

            if (closeButton) {
                closeButton.addEventListener('click', () => setPanelOpen(false));
            }

            if (inviteDismiss) {
                inviteDismiss.addEventListener('click', () => {
                    state.inviteDismissed = true;
                    storage.set(window.sessionStorage, inviteDismissKey, '1');
                    hideInvite();
                });
            }

            if (inviteOpen) {
                inviteOpen.addEventListener('click', async () => {
                    setPanelOpen(true);
                    hideInvite();
                    await ensureSessionLoaded();
                    await runPollSafely();
                });
            }

            if (chatForm) {
                chatForm.addEventListener('submit', async (event) => {
                    event.preventDefault();
                    try {
                        await sendMessage();
                    } catch (error) {
                        setFeedback(error instanceof Error ? error.message : 'Erro ao enviar mensagem.', 'error');
                    }
                });
            }

            if (messageInput) {
                messageInput.addEventListener('keydown', async (event) => {
                    if ((event.ctrlKey || event.metaKey) && event.key === 'Enter') {
                        event.preventDefault();
                        if (!chatForm) return;
                        chatForm.requestSubmit();
                    }
                });
            }

            if (offlineForm) {
                offlineForm.addEventListener('submit', async (event) => {
                    event.preventDefault();
                    try {
                        await sendOfflineMessage();
                    } catch (error) {
                        setFeedback(error instanceof Error ? error.message : 'Erro ao enviar mensagem offline.', 'error');
                    }
                });
            }

            runHeartbeatSafely();
            window.setTimeout(runPollSafely, 1200);
            window.setInterval(runHeartbeatSafely, 12000);
            window.setInterval(runPollSafely, 4000);

            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) {
                    runHeartbeatSafely();
                    runPollSafely();
                }
            });
        })();
    </script>
@endpush
