<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\LiveChatMessage;
use App\Models\LiveChatSession;
use App\Models\LiveVisitor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class LiveChatController extends Controller
{
    public function heartbeat(Request $request): JsonResponse
    {
        $data = $request->validate([
            'visitor_token' => ['required', 'string', 'min:18', 'max:64'],
            'current_url' => ['nullable', 'string', 'max:1200'],
            'current_path' => ['nullable', 'string', 'max:600'],
            'landing_url' => ['nullable', 'string', 'max:1200'],
            'referrer_url' => ['nullable', 'string', 'max:1200'],
            'page_title' => ['nullable', 'string', 'max:255'],
            'timezone' => ['nullable', 'string', 'max:80'],
            'language' => ['nullable', 'string', 'max:40'],
            'screen_size' => ['nullable', 'string', 'max:40'],
            'session_id' => ['nullable', 'string', 'max:120'],
            'panel_open' => ['nullable', 'boolean'],
            'metadata' => ['nullable', 'array'],
        ]);

        $token = $this->normalizeToken((string) $data['visitor_token']);
        $panelOpen = (bool) ($data['panel_open'] ?? false);

        $visitor = $this->touchVisitor($request, $token, $data);
        $session = $this->resolveSessionForVisitor($token, null);

        if ($session) {
            $session->forceFill([
                'current_url' => $this->stringOrNull($data['current_url'] ?? $visitor->current_url, 1200),
                'current_path' => $this->stringOrNull($data['current_path'] ?? $visitor->current_path, 600),
                'user_id' => $session->user_id ?: $visitor->user_id,
            ])->save();

            if ($panelOpen) {
                $session->messages()
                    ->where('sender_role', 'admin')
                    ->where('is_read_by_visitor', false)
                    ->update(['is_read_by_visitor' => true]);
            }
        }

        $consultantsOnline = $this->activeConsultantsCount();

        return response()->json([
            'ok' => true,
            'server_time' => now()->toIso8601String(),
            'consultants_online' => $consultantsOnline,
            'is_consultant_online' => $consultantsOnline > 0,
            'session' => $session ? $this->sessionPayload($session, $token) : null,
            'visitor' => [
                'token' => $token,
                'is_logged' => (bool) $visitor->user_id,
                'name' => $visitor->user?->name,
            ],
        ]);
    }

    public function poll(Request $request): JsonResponse
    {
        $data = $request->validate([
            'visitor_token' => ['required', 'string', 'min:18', 'max:64'],
            'session_id' => ['nullable', 'integer', 'min:1'],
            'last_message_id' => ['nullable', 'integer', 'min:0'],
            'panel_open' => ['nullable', 'boolean'],
            'current_url' => ['nullable', 'string', 'max:1200'],
            'current_path' => ['nullable', 'string', 'max:600'],
            'metadata' => ['nullable', 'array'],
        ]);

        $token = $this->normalizeToken((string) $data['visitor_token']);
        $panelOpen = (bool) ($data['panel_open'] ?? false);
        $lastMessageId = (int) ($data['last_message_id'] ?? 0);

        $this->touchVisitor($request, $token, $data, false);
        $session = $this->resolveSessionForVisitor($token, isset($data['session_id']) ? (int) $data['session_id'] : null);

        $consultantsOnline = $this->activeConsultantsCount();

        if (! $session) {
            return response()->json([
                'ok' => true,
                'session' => null,
                'messages' => [],
                'consultants_online' => $consultantsOnline,
                'is_consultant_online' => $consultantsOnline > 0,
            ]);
        }

        if ($panelOpen) {
            $session->messages()
                ->where('sender_role', 'admin')
                ->where('is_read_by_visitor', false)
                ->update(['is_read_by_visitor' => true]);
        }

        $messages = $session->messages()
            ->where('id', '>', $lastMessageId)
            ->with('user:id,name')
            ->orderBy('id')
            ->take(80)
            ->get();

        return response()->json([
            'ok' => true,
            'session' => $this->sessionPayload($session, $token),
            'messages' => $this->messagePayloadCollection($messages),
            'consultants_online' => $consultantsOnline,
            'is_consultant_online' => $consultantsOnline > 0,
        ]);
    }

    public function open(Request $request): JsonResponse
    {
        $data = $request->validate([
            'visitor_token' => ['required', 'string', 'min:18', 'max:64'],
            'message' => ['nullable', 'string', 'min:2', 'max:3000'],
            'visitor_name' => ['nullable', 'string', 'max:120'],
            'visitor_email' => ['nullable', 'email', 'max:190'],
            'visitor_phone' => ['nullable', 'string', 'max:40'],
            'current_url' => ['nullable', 'string', 'max:1200'],
            'current_path' => ['nullable', 'string', 'max:600'],
            'metadata' => ['nullable', 'array'],
        ]);

        $token = $this->normalizeToken((string) $data['visitor_token']);
        $visitor = $this->touchVisitor($request, $token, $data);
        $session = $this->findOrCreateOpenSession($visitor, $data);

        $message = trim((string) ($data['message'] ?? ''));
        if ($message !== '') {
            $chatMessage = $this->createVisitorMessage($session, $message, $visitor->user_id);

            return response()->json([
                'ok' => true,
                'session' => $this->sessionPayload($session->fresh(), $token),
                'message' => $this->messagePayload($chatMessage),
            ]);
        }

        $messages = $session->messages()
            ->with('user:id,name')
            ->orderByDesc('id')
            ->take(60)
            ->get()
            ->reverse()
            ->values();

        return response()->json([
            'ok' => true,
            'session' => $this->sessionPayload($session->fresh(), $token),
            'messages' => $this->messagePayloadCollection($messages),
        ]);
    }

    public function sendMessage(Request $request): JsonResponse
    {
        $data = $request->validate([
            'visitor_token' => ['required', 'string', 'min:18', 'max:64'],
            'session_id' => ['nullable', 'integer', 'min:1'],
            'message' => ['required', 'string', 'min:1', 'max:3000'],
            'visitor_name' => ['nullable', 'string', 'max:120'],
            'visitor_email' => ['nullable', 'email', 'max:190'],
            'visitor_phone' => ['nullable', 'string', 'max:40'],
            'current_url' => ['nullable', 'string', 'max:1200'],
            'current_path' => ['nullable', 'string', 'max:600'],
            'metadata' => ['nullable', 'array'],
        ]);

        $token = $this->normalizeToken((string) $data['visitor_token']);
        $visitor = $this->touchVisitor($request, $token, $data);

        $session = $this->resolveSessionForVisitor($token, isset($data['session_id']) ? (int) $data['session_id'] : null);
        if (! $session) {
            $session = $this->findOrCreateOpenSession($visitor, $data);
        } else {
            $session->forceFill([
                'visitor_name' => $this->stringOrNull($data['visitor_name'] ?? $session->visitor_name, 120),
                'visitor_email' => $this->stringOrNull($data['visitor_email'] ?? $session->visitor_email, 190),
                'visitor_phone' => $this->stringOrNull($data['visitor_phone'] ?? $session->visitor_phone, 40),
                'current_url' => $this->stringOrNull($data['current_url'] ?? $session->current_url, 1200),
                'current_path' => $this->stringOrNull($data['current_path'] ?? $session->current_path, 600),
                'status' => 'open',
                'closed_at' => null,
            ])->save();
        }

        $chatMessage = $this->createVisitorMessage(
            $session,
            trim((string) $data['message']),
            $visitor->user_id,
        );

        return response()->json([
            'ok' => true,
            'session' => $this->sessionPayload($session->fresh(), $token),
            'message' => $this->messagePayload($chatMessage),
        ]);
    }

    public function leaveOfflineMessage(Request $request): JsonResponse
    {
        $data = $request->validate([
            'visitor_token' => ['required', 'string', 'min:18', 'max:64'],
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'message' => ['required', 'string', 'min:8', 'max:3000'],
            'current_url' => ['nullable', 'string', 'max:1200'],
            'current_path' => ['nullable', 'string', 'max:600'],
            'lgpd_consent' => ['accepted'],
        ]);

        $token = $this->normalizeToken((string) $data['visitor_token']);
        $visitor = $this->touchVisitor($request, $token, [
            'current_url' => $data['current_url'] ?? null,
            'current_path' => $data['current_path'] ?? null,
            'metadata' => [
                'visitor_name' => $data['name'],
                'visitor_email' => $data['email'],
            ],
        ]);

        $session = $this->findOrCreateOpenSession($visitor, [
            'visitor_name' => $data['name'],
            'visitor_email' => $data['email'],
            'visitor_phone' => $data['phone'] ?? null,
            'current_url' => $data['current_url'] ?? null,
            'current_path' => $data['current_path'] ?? null,
        ]);

        $session->forceFill([
            'status' => 'offline_message',
        ])->save();

        $chatMessage = $this->createVisitorMessage($session, trim((string) $data['message']), $visitor->user_id);

        ContactMessage::query()->create([
            'user_id' => $visitor->user_id,
            'name' => trim((string) $data['name']),
            'email' => trim((string) $data['email']),
            'phone' => $this->stringOrNull($data['phone'] ?? null, 40),
            'subject' => 'Mensagem offline do chat',
            'service_interest' => 'outros',
            'preferred_contact' => 'email',
            'order_reference' => null,
            'message' => trim((string) $data['message']),
            'lgpd_consent' => true,
            'status' => 'new',
            'source_url' => $this->stringOrNull($data['current_url'] ?? route('pages.contact'), 1200),
            'ip_address' => $this->stringOrNull($request->ip(), 45),
            'user_agent' => $this->stringOrNull((string) $request->userAgent(), 500),
        ]);

        return response()->json([
            'ok' => true,
            'session' => $this->sessionPayload($session->fresh(), $token),
            'message' => $this->messagePayload($chatMessage),
            'contact_url' => route('pages.contact', [
                'service_interest' => 'outros',
                'subject' => 'Mensagem offline do chat',
                'message' => trim((string) $data['message']),
            ]),
        ]);
    }

    private function findOrCreateOpenSession(LiveVisitor $visitor, array $payload): LiveChatSession
    {
        $session = $this->resolveSessionForVisitor($visitor->visitor_token, null);
        if ($session) {
            return $session;
        }

        return LiveChatSession::query()->create([
            'visitor_token' => $visitor->visitor_token,
            'user_id' => $visitor->user_id,
            'assigned_admin_id' => null,
            'status' => 'open',
            'visitor_name' => $this->stringOrNull($payload['visitor_name'] ?? data_get($visitor->metadata, 'visitor_name'), 120),
            'visitor_email' => $this->stringOrNull($payload['visitor_email'] ?? data_get($visitor->metadata, 'visitor_email'), 190),
            'visitor_phone' => $this->stringOrNull($payload['visitor_phone'] ?? null, 40),
            'current_url' => $this->stringOrNull($payload['current_url'] ?? $visitor->current_url, 1200),
            'current_path' => $this->stringOrNull($payload['current_path'] ?? $visitor->current_path, 600),
            'metadata' => [
                'created_from' => 'widget',
            ],
        ]);
    }

    private function resolveSessionForVisitor(string $visitorToken, ?int $sessionId): ?LiveChatSession
    {
        if ($sessionId) {
            return LiveChatSession::query()
                ->where('id', $sessionId)
                ->where('visitor_token', $visitorToken)
                ->first();
        }

        return LiveChatSession::query()
            ->openOrWaiting()
            ->where('visitor_token', $visitorToken)
            ->latest('id')
            ->first();
    }

    private function createVisitorMessage(LiveChatSession $session, string $body, ?int $userId): LiveChatMessage
    {
        $message = $session->messages()->create([
            'sender_role' => 'visitor',
            'user_id' => $userId,
            'body' => trim($body),
            'is_read_by_admin' => false,
            'is_read_by_visitor' => true,
            'metadata' => [
                'channel' => 'live_chat',
            ],
        ]);

        $firstMessageAt = $session->first_message_at ?: $message->created_at;
        $session->forceFill([
            'first_message_at' => $firstMessageAt,
            'last_message_at' => $message->created_at,
            'status' => 'open',
            'closed_at' => null,
        ])->save();

        return $message->loadMissing('user:id,name');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function touchVisitor(Request $request, string $token, array $payload, bool $updatePageData = true): LiveVisitor
    {
        $visitor = LiveVisitor::query()->firstOrNew(['visitor_token' => $token]);
        $now = now();

        if (! $visitor->exists || ! $visitor->first_seen_at) {
            $visitor->first_seen_at = $now;
        }

        $currentUrl = $this->stringOrNull($payload['current_url'] ?? null, 1200);
        $currentPath = $this->stringOrNull($payload['current_path'] ?? null, 600);

        if ($updatePageData) {
            $visitor->current_url = $currentUrl ?: $visitor->current_url;
            $visitor->current_path = $currentPath ?: $visitor->current_path;
            $visitor->landing_url = $visitor->landing_url ?: ($this->stringOrNull($payload['landing_url'] ?? null, 1200) ?: $currentUrl);
            $visitor->referrer_url = $this->stringOrNull($payload['referrer_url'] ?? null, 1200) ?: $visitor->referrer_url;
            $visitor->page_title = $this->stringOrNull($payload['page_title'] ?? null, 255) ?: $visitor->page_title;
            $visitor->timezone = $this->stringOrNull($payload['timezone'] ?? null, 80) ?: $visitor->timezone;
            $visitor->language = $this->stringOrNull($payload['language'] ?? null, 40) ?: $visitor->language;
            $visitor->screen_size = $this->stringOrNull($payload['screen_size'] ?? null, 40) ?: $visitor->screen_size;
        } else {
            $visitor->current_url = $currentUrl ?: $visitor->current_url;
            $visitor->current_path = $currentPath ?: $visitor->current_path;
        }

        $visitor->session_id = $this->stringOrNull($payload['session_id'] ?? session()->getId(), 120);
        $visitor->ip_address = $this->stringOrNull($request->ip(), 45);
        $visitor->user_agent = $this->stringOrNull((string) $request->userAgent(), 500);
        $visitor->country_code = $this->stringOrNull((string) ($request->header('CF-IPCountry') ?? $request->header('X-Appengine-Country') ?? ''), 8);
        $visitor->last_seen_at = $now;

        if ($request->user()) {
            $visitor->user_id = $request->user()->id;
        }

        $metadata = is_array($visitor->metadata) ? $visitor->metadata : [];
        $incomingMetadata = (array) ($payload['metadata'] ?? []);
        $visitor->metadata = array_filter(array_merge($metadata, [
            'page_started_at' => isset($incomingMetadata['page_started_at']) ? (int) $incomingMetadata['page_started_at'] : data_get($metadata, 'page_started_at'),
            'visitor_name' => $this->stringOrNull($incomingMetadata['visitor_name'] ?? null, 120) ?: data_get($metadata, 'visitor_name'),
            'visitor_email' => $this->stringOrNull($incomingMetadata['visitor_email'] ?? null, 190) ?: data_get($metadata, 'visitor_email'),
        ], $incomingMetadata), fn ($value) => $value !== null && $value !== '');

        $visitor->save();

        return $visitor->loadMissing('user:id,name,email,is_admin');
    }

    private function activeConsultantsCount(): int
    {
        return LiveVisitor::query()
            ->active(75)
            ->with('user:id,is_admin')
            ->get()
            ->filter(fn (LiveVisitor $visitor): bool => (bool) $visitor->user?->is_admin)
            ->pluck('user_id')
            ->filter()
            ->unique()
            ->count();
    }

    private function normalizeToken(string $token): string
    {
        $token = trim($token);
        if ($token === '') {
            return Str::random(32);
        }

        return Str::lower(substr(preg_replace('/[^a-zA-Z0-9\-_]/', '', $token) ?: Str::random(32), 0, 64));
    }

    private function stringOrNull(mixed $value, int $maxLength): ?string
    {
        $text = trim((string) $value);
        if ($text === '') {
            return null;
        }

        return mb_substr($text, 0, $maxLength);
    }

    private function sessionPayload(LiveChatSession $session, string $token): array
    {
        return [
            'id' => $session->id,
            'visitor_token' => $token,
            'status' => $session->status,
            'visitor_name' => $session->visitor_name,
            'visitor_email' => $session->visitor_email,
            'current_url' => $session->current_url,
            'current_path' => $session->current_path,
            'unread_from_admin' => $session->messages()
                ->where('sender_role', 'admin')
                ->where('is_read_by_visitor', false)
                ->count(),
            'last_message_at' => optional($session->last_message_at)->toIso8601String(),
        ];
    }

    /**
     * @param  Collection<int, LiveChatMessage>  $messages
     * @return array<int, array<string, mixed>>
     */
    private function messagePayloadCollection(Collection $messages): array
    {
        return $messages->map(fn (LiveChatMessage $message): array => $this->messagePayload($message))->values()->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function messagePayload(LiveChatMessage $message): array
    {
        return [
            'id' => $message->id,
            'sender_role' => $message->sender_role,
            'body' => $message->body,
            'author_name' => $message->user?->name,
            'is_read_by_visitor' => (bool) $message->is_read_by_visitor,
            'is_read_by_admin' => (bool) $message->is_read_by_admin,
            'created_at' => optional($message->created_at)->toIso8601String(),
        ];
    }
}
