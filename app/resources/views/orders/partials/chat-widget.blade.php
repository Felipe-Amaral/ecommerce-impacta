@props([
    'order',
    'viewer' => 'client', // client|admin
    'title' => 'Atendimento do pedido',
    'subtitle' => 'Use este chat para alinhar arte, prazo, acabamento e entrega.',
    'widgetId' => null,
])

@php
    $initialMessages = $order->messages
        ->sortBy('created_at')
        ->values()
        ->map(fn ($message) => [
            'id' => $message->id,
            'sender_role' => $message->sender_role,
            'sender_name' => $message->user?->name ?: match ($message->sender_role) {
                'admin' => 'Atendimento da gráfica',
                'client' => 'Cliente',
                default => 'Sistema',
            },
            'body' => $message->body,
            'created_at' => optional($message->created_at)->toIso8601String(),
            'created_at_human' => optional($message->created_at)->format('d/m/Y H:i'),
        ])
        ->all();

    $reverbBroadcast = (string) config('broadcasting.default', 'null') === 'reverb';
    $reverbConfig = (array) config('broadcasting.connections.reverb', []);
    $reverbOptions = (array) ($reverbConfig['options'] ?? []);
    $reverbHost = (string) ($reverbOptions['host'] ?? env('REVERB_HOST', 'localhost'));
    $reverbPort = (int) ($reverbOptions['port'] ?? env('REVERB_PORT', 8081));
    $reverbScheme = (string) ($reverbOptions['scheme'] ?? env('REVERB_SCHEME', 'http'));
@endphp

<section
    @if($widgetId) id="{{ $widgetId }}" @endif
    class="card card-pad stack chat-widget"
    data-order-chat
    data-order-id="{{ $order->id }}"
    data-messages-endpoint="{{ route('orders.chat.messages.index', $order) }}"
    data-send-endpoint="{{ route('orders.chat.messages.store', $order) }}"
    data-csrf-token="{{ csrf_token() }}"
    data-viewer-role="{{ $viewer }}"
    data-realtime="{{ $reverbBroadcast ? '1' : '0' }}"
    data-reverb-key="{{ (string) ($reverbConfig['key'] ?? '') }}"
    data-reverb-host="{{ $reverbHost }}"
    data-reverb-port="{{ $reverbPort }}"
    data-reverb-scheme="{{ $reverbScheme }}"
>
    <div class="section-head">
        <div class="copy">
            <span class="section-kicker">Chat do pedido</span>
            <h3 style="margin:0;">{{ $title }}</h3>
            <p class="small muted">{{ $subtitle }}</p>
        </div>
        <span class="chat-connection" data-chat-connection>Sincronizando...</span>
    </div>

    <div class="chat-thread" data-chat-thread aria-live="polite"></div>

    <div class="chat-empty muted small" data-chat-empty @if(!empty($initialMessages)) hidden @endif>
        Nenhuma mensagem ainda. Envie uma mensagem para alinhar detalhes deste pedido.
    </div>

    <form class="chat-form" data-chat-form>
        <label class="sr-only" for="chat_body_{{ $order->id }}">Mensagem</label>
        <textarea
            id="chat_body_{{ $order->id }}"
            class="textarea chat-input"
            data-chat-input
            rows="3"
            maxlength="4000"
            placeholder="{{ $viewer === 'admin' ? 'Ex.: confirme a versão final da arte, prazo de aprovação e observações de acabamento...' : 'Ex.: preciso ajustar a arte, confirmar acabamento ou prazo. Pode me orientar?' }}"
            required
        ></textarea>
        <div class="link-row">
            <span class="tiny muted">Mensagens ficam registradas no pedido para cliente e gráfica. Use <strong>Ctrl + Enter</strong> para enviar.</span>
            <button type="submit" class="btn btn-primary btn-sm" data-chat-submit>Enviar mensagem</button>
        </div>
    </form>

    <script type="application/json" data-chat-initial>@json($initialMessages)</script>
</section>

@push('scripts')
    @once
        <script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/laravel-echo@2.2.4/dist/echo.iife.js"></script>
    @endonce
    @once
        <script>
            (function () {
                const widgets = Array.from(document.querySelectorAll('[data-order-chat]'));
                if (!widgets.length) return;

                const nl2br = (text) => String(text || '').replace(/\n/g, '<br>');

                const initWidget = (root) => {
                    if (root.dataset.chatReady === '1') return;
                    root.dataset.chatReady = '1';

                    const thread = root.querySelector('[data-chat-thread]');
                    const empty = root.querySelector('[data-chat-empty]');
                    const form = root.querySelector('[data-chat-form]');
                    const input = root.querySelector('[data-chat-input]');
                    const submit = root.querySelector('[data-chat-submit]');
                    const connectionLabel = root.querySelector('[data-chat-connection]');
                    const initialJson = root.querySelector('[data-chat-initial]');

                    if (!thread || !form || !input || !submit) return;

                    const state = {
                        orderId: Number(root.dataset.orderId || 0),
                        messagesEndpoint: root.dataset.messagesEndpoint || '',
                        sendEndpoint: root.dataset.sendEndpoint || '',
                        csrfToken: root.dataset.csrfToken || '',
                        viewerRole: root.dataset.viewerRole || 'client',
                        lastId: 0,
                        messages: [],
                        pollingHandle: null,
                        echoChannel: null,
                        sending: false,
                        submitByShortcut: false,
                    };

                    const setConnection = (text, tone = 'muted') => {
                        if (!connectionLabel) return;
                        connectionLabel.textContent = text;
                        connectionLabel.dataset.tone = tone;
                    };

                    const render = () => {
                        thread.innerHTML = '';

                        if (!state.messages.length) {
                            if (empty) empty.hidden = false;
                            return;
                        }

                        if (empty) empty.hidden = true;

                        state.messages.forEach((message) => {
                            const item = document.createElement('article');
                            item.className = 'chat-message ' + [
                                message.sender_role === state.viewerRole ? 'mine' : '',
                                message.sender_role === 'system' ? 'system' : '',
                                message.sender_role === 'admin' ? 'from-admin' : '',
                                message.sender_role === 'client' ? 'from-client' : '',
                            ].filter(Boolean).join(' ');
                            item.dataset.messageId = String(message.id);

                            item.innerHTML = `
                                <div class="chat-message-meta">
                                    <strong>${message.sender_name || 'Mensagem'}</strong>
                                    <span>${message.created_at_human || ''}</span>
                                </div>
                                <div class="chat-message-body">${nl2br(message.body || '')}</div>
                            `;

                            thread.appendChild(item);
                        });

                        thread.scrollTop = thread.scrollHeight;
                    };

                    const pushMessages = (incoming) => {
                        let changed = false;
                        (incoming || []).forEach((message) => {
                            if (!message || !message.id) return;
                            if (state.messages.some((m) => Number(m.id) === Number(message.id))) return;
                            state.messages.push(message);
                            state.lastId = Math.max(state.lastId, Number(message.id) || 0);
                            changed = true;
                        });

                        if (changed) {
                            state.messages.sort((a, b) => Number(a.id) - Number(b.id));
                            render();
                        }
                    };

                    try {
                        const initial = JSON.parse(initialJson?.textContent || '[]');
                        if (Array.isArray(initial)) {
                            state.messages = initial;
                            state.lastId = initial.reduce((carry, item) => Math.max(carry, Number(item?.id) || 0), 0);
                            render();
                        }
                    } catch (error) {
                        setConnection('Erro ao carregar mensagens', 'error');
                    }

                    const fetchNewMessages = async () => {
                        if (!state.messagesEndpoint) return;

                        try {
                            const url = new URL(state.messagesEndpoint, window.location.origin);
                            if (state.lastId > 0) {
                                url.searchParams.set('after_id', String(state.lastId));
                            }

                            const response = await fetch(url.toString(), {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                credentials: 'same-origin'
                            });

                            if (!response.ok) throw new Error('chat_poll_failed');
                            const data = await response.json();
                            pushMessages(Array.isArray(data.messages) ? data.messages : []);
                            setConnection(state.echoChannel ? 'Online em tempo real' : 'Atualizando automaticamente', state.echoChannel ? 'success' : 'muted');
                        } catch (error) {
                            setConnection('Sem conexão em tempo real (polling local)', 'warning');
                        }
                    };

                    const startPolling = () => {
                        if (state.pollingHandle) return;
                        state.pollingHandle = window.setInterval(fetchNewMessages, 5000);
                    };

                    const isCoarsePointer = () => window.matchMedia('(pointer: coarse)').matches;

                    const parseJsonResponse = async (response) => {
                        const raw = await response.text().catch(() => '');
                        if (!raw || !raw.trim()) return null;
                        try {
                            return JSON.parse(raw);
                        } catch (error) {
                            return null;
                        }
                    };

                    const submitMessage = async () => {
                        const body = String(input.value || '').trim();
                        if (!body || state.sending) return;

                        state.sending = true;
                        submit.disabled = true;
                        setConnection('Enviando mensagem...', 'muted');

                        try {
                            const response = await fetch(state.sendEndpoint, {
                                method: 'POST',
                                credentials: 'same-origin',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': state.csrfToken,
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                                body: JSON.stringify({ body }),
                            });

                            const data = await parseJsonResponse(response);

                            if (!response.ok) {
                                const message = typeof data?.message === 'string' ? data.message : 'chat_send_failed';
                                throw new Error(message);
                            }

                            // Treat any 2xx as success. If JSON parsing fails, polling/realtime will sync the message.
                            if (data && data.message) {
                                pushMessages([data.message]);
                            } else {
                                void fetchNewMessages();
                            }

                            input.value = '';
                            setConnection(state.echoChannel ? 'Online em tempo real' : 'Mensagem enviada', state.echoChannel ? 'success' : 'muted');
                        } catch (error) {
                            setConnection('Falha ao enviar. Tente novamente.', 'error');
                        } finally {
                            state.sending = false;
                            submit.disabled = false;
                            state.submitByShortcut = false;
                        }
                    };

                    form.addEventListener('submit', async (event) => {
                        event.preventDefault();

                        // Desktop: only Ctrl+Enter sends. Mobile/touch can still use the button.
                        if (!state.submitByShortcut && !isCoarsePointer()) {
                            setConnection('Use Ctrl + Enter para enviar.', 'warning');
                            return;
                        }

                        await submitMessage();
                    });

                    submit.addEventListener('click', (event) => {
                        if (isCoarsePointer()) return;
                        if (state.submitByShortcut) return;
                        // Prevent desktop mouse click submit; enforce Ctrl+Enter.
                        event.preventDefault();
                        setConnection('Use Ctrl + Enter para enviar.', 'warning');
                    });

                    input.addEventListener('keydown', (event) => {
                        if (event.key !== 'Enter') return;

                        if (event.ctrlKey || event.metaKey) {
                            event.preventDefault();
                            if (state.sending) return;
                            state.submitByShortcut = true;
                            if (typeof form.requestSubmit === 'function') {
                                form.requestSubmit();
                            } else {
                                void submitMessage();
                            }
                        }
                    });

                    const setupRealtime = () => {
                        const wantsRealtime = root.dataset.realtime === '1';
                        const hasEcho = !!window.Echo;
                        const hasPusher = !!window.Pusher;
                        const key = root.dataset.reverbKey || '';
                        if (!wantsRealtime || !hasEcho || !hasPusher || !key) {
                            startPolling();
                            void fetchNewMessages();
                            return;
                        }

                        try {
                            const wsHost = root.dataset.reverbHost || window.location.hostname;
                            const wsPort = Number(root.dataset.reverbPort || 8081);
                            const scheme = root.dataset.reverbScheme || 'http';
                            const forceTLS = scheme === 'https';

                            if (!window.__verticeEcho) {
                                window.__verticeEcho = new window.Echo({
                                    broadcaster: 'reverb',
                                    key,
                                    wsHost,
                                    wsPort,
                                    wssPort: wsPort,
                                    forceTLS,
                                    enabledTransports: ['ws', 'wss'],
                                    authEndpoint: '/broadcasting/auth',
                                    withCredentials: true,
                                });
                            }

                            const channelName = 'orders.' + state.orderId + '.chat';
                            state.echoChannel = window.__verticeEcho.private(channelName)
                                .listen('.order.message.sent', (payload) => {
                                    if (payload && payload.message) {
                                        pushMessages([payload.message]);
                                        setConnection('Online em tempo real', 'success');
                                    }
                                });

                            setConnection('Online em tempo real', 'success');
                        } catch (error) {
                            setConnection('Sem conexão em tempo real (polling local)', 'warning');
                        } finally {
                            startPolling();
                            void fetchNewMessages();
                        }
                    };

                    setupRealtime();
                };

                widgets.forEach(initWidget);
            })();
        </script>
    @endonce
@endpush
