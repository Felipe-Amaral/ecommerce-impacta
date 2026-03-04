<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveChatMessage;
use App\Models\LiveChatSession;
use App\Models\LiveVisitor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LiveChatMonitorController extends Controller
{
    public function index(): View
    {
        $this->assertAdmin();

        $visitors = $this->activeVisitorsPayload();
        $sessions = $this->openSessionsPayload();
        $consultantsOnline = $this->consultantsOnlineCount();

        return view('admin.livechat.index', [
            'initialVisitors' => $visitors,
            'initialSessions' => $sessions,
            'consultantsOnline' => $consultantsOnline,
            'stats' => [
                'active_visitors' => count($visitors),
                'open_sessions' => count($sessions),
                'waiting_messages' => collect($sessions)->sum(fn (array $row): int => (int) ($row['unread_from_visitor'] ?? 0)),
            ],
        ]);
    }

    public function snapshot(): JsonResponse
    {
        $this->assertAdmin();

        $visitors = $this->activeVisitorsPayload();
        $sessions = $this->openSessionsPayload();

        return response()->json([
            'ok' => true,
            'server_time' => now()->toIso8601String(),
            'consultants_online' => $this->consultantsOnlineCount(),
            'active_visitors' => $visitors,
            'sessions' => $sessions,
            'stats' => [
                'active_visitors' => count($visitors),
                'open_sessions' => count($sessions),
                'waiting_messages' => collect($sessions)->sum(fn (array $row): int => (int) ($row['unread_from_visitor'] ?? 0)),
            ],
        ]);
    }

    public function messages(LiveChatSession $liveChatSession): JsonResponse
    {
        $this->assertAdmin();

        $liveChatSession->loadMissing(['user:id,name,email', 'visitor.user:id,name,email,is_admin']);

        $liveChatSession->messages()
            ->where('sender_role', 'visitor')
            ->where('is_read_by_admin', false)
            ->update(['is_read_by_admin' => true]);

        $messages = $liveChatSession->messages()
            ->with('user:id,name')
            ->orderByDesc('id')
            ->take(180)
            ->get()
            ->reverse()
            ->values();

        return response()->json([
            'ok' => true,
            'session' => $this->sessionPayload($liveChatSession->fresh(['user:id,name,email', 'visitor.user:id,name,email,is_admin', 'latestMessage.user:id,name'])),
            'messages' => $messages->map(fn (LiveChatMessage $message): array => $this->messagePayload($message))->values()->all(),
        ]);
    }

    public function storeMessage(Request $request, LiveChatSession $liveChatSession): JsonResponse
    {
        $this->assertAdmin();

        $validated = $request->validate([
            'body' => ['required', 'string', 'min:1', 'max:3000'],
        ]);

        $message = $liveChatSession->messages()->create([
            'sender_role' => 'admin',
            'user_id' => auth()->id(),
            'body' => trim((string) $validated['body']),
            'is_read_by_admin' => true,
            'is_read_by_visitor' => false,
            'metadata' => [
                'channel' => 'live_chat',
            ],
        ]);

        $liveChatSession->forceFill([
            'assigned_admin_id' => $liveChatSession->assigned_admin_id ?: auth()->id(),
            'status' => 'open',
            'closed_at' => null,
            'first_message_at' => $liveChatSession->first_message_at ?: $message->created_at,
            'last_message_at' => $message->created_at,
        ])->save();

        return response()->json([
            'ok' => true,
            'message' => $this->messagePayload($message->fresh('user:id,name')),
            'session' => $this->sessionPayload($liveChatSession->fresh(['user:id,name,email', 'visitor.user:id,name,email,is_admin', 'latestMessage.user:id,name'])),
        ]);
    }

    public function close(LiveChatSession $liveChatSession): JsonResponse
    {
        $this->assertAdmin();

        $liveChatSession->forceFill([
            'status' => 'closed',
            'closed_at' => now(),
            'assigned_admin_id' => $liveChatSession->assigned_admin_id ?: auth()->id(),
        ])->save();

        return response()->json([
            'ok' => true,
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function activeVisitorsPayload(): array
    {
        $visitors = LiveVisitor::query()
            ->active(120)
            ->with('user:id,name,email,is_admin')
            ->withCount('chatSessions')
            ->orderByDesc('last_seen_at')
            ->take(150)
            ->get();

        return $visitors
            ->filter(fn (LiveVisitor $visitor): bool => ! (bool) ($visitor->user?->is_admin))
            ->map(function (LiveVisitor $visitor): array {
                $metadata = is_array($visitor->metadata) ? $visitor->metadata : [];
                $pageStartedAt = (int) ($metadata['page_started_at'] ?? 0);
                $pageSeconds = $pageStartedAt > 0 ? max(0, now()->timestamp - $pageStartedAt) : null;

                $displayName = $visitor->user?->name
                    ?: trim((string) ($metadata['visitor_name'] ?? ''))
                    ?: 'Visitante '.strtoupper(substr($visitor->visitor_token, 0, 6));

                return [
                    'id' => $visitor->id,
                    'visitor_token' => $visitor->visitor_token,
                    'display_name' => $displayName,
                    'email' => $visitor->user?->email ?: ($metadata['visitor_email'] ?? null),
                    'is_logged' => (bool) $visitor->user_id,
                    'ip_address' => $visitor->ip_address,
                    'current_path' => $visitor->current_path,
                    'current_url' => $visitor->current_url,
                    'referrer_url' => $visitor->referrer_url,
                    'page_title' => $visitor->page_title,
                    'timezone' => $visitor->timezone,
                    'language' => $visitor->language,
                    'screen_size' => $visitor->screen_size,
                    'browser' => $metadata['browser'] ?? null,
                    'device_type' => $metadata['device_type'] ?? null,
                    'platform' => $metadata['platform'] ?? null,
                    'page_views_in_session' => isset($metadata['page_views_in_session']) ? (int) $metadata['page_views_in_session'] : null,
                    'visits_count' => isset($metadata['visits_count']) ? (int) $metadata['visits_count'] : null,
                    'chat_sessions_count' => (int) ($visitor->chat_sessions_count ?? 0),
                    'utm_source' => $metadata['utm_source'] ?? null,
                    'utm_medium' => $metadata['utm_medium'] ?? null,
                    'utm_campaign' => $metadata['utm_campaign'] ?? null,
                    'referrer_host' => $metadata['referrer_host'] ?? null,
                    'user_agent' => $visitor->user_agent,
                    'page_seconds' => $pageSeconds,
                    'session_seconds' => $visitor->first_seen_at ? now()->diffInSeconds($visitor->first_seen_at) : null,
                    'first_seen_at' => optional($visitor->first_seen_at)->toIso8601String(),
                    'last_seen_at' => optional($visitor->last_seen_at)->toIso8601String(),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function openSessionsPayload(): array
    {
        $sessions = LiveChatSession::query()
            ->openOrWaiting()
            ->with(['latestMessage.user:id,name', 'user:id,name,email', 'visitor.user:id,name,email,is_admin', 'assignedAdmin:id,name'])
            ->withCount([
                'messages as unread_from_visitor' => fn ($query) => $query
                    ->where('sender_role', 'visitor')
                    ->where('is_read_by_admin', false),
                'messages as unread_from_admin' => fn ($query) => $query
                    ->where('sender_role', 'admin')
                    ->where('is_read_by_visitor', false),
            ])
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->take(150)
            ->get();

        return $sessions->map(fn (LiveChatSession $session): array => $this->sessionPayload($session))->values()->all();
    }

    private function consultantsOnlineCount(): int
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

    /**
     * @return array<string, mixed>
     */
    private function sessionPayload(LiveChatSession $session): array
    {
        $visitor = $session->visitor;
        $visitorUser = $visitor?->user;
        $sessionUser = $session->user;
        $visitorMetadata = is_array($visitor?->metadata) ? $visitor->metadata : [];

        $displayName = $session->visitor_name
            ?: $sessionUser?->name
            ?: $visitorUser?->name
            ?: 'Visitante '.strtoupper(substr($session->visitor_token, 0, 6));

        $currentPath = $session->current_path ?: $visitor?->current_path;
        $currentUrl = $session->current_url ?: $visitor?->current_url;

        return [
            'id' => $session->id,
            'visitor_token' => $session->visitor_token,
            'display_name' => $displayName,
            'email' => $session->visitor_email ?: $sessionUser?->email ?: $visitorUser?->email,
            'phone' => $session->visitor_phone,
            'status' => $session->status,
            'current_path' => $currentPath,
            'current_url' => $currentUrl,
            'last_message_preview' => Str::limit((string) ($session->latestMessage?->body ?? ''), 120),
            'last_message_at' => optional($session->last_message_at)->toIso8601String(),
            'unread_from_visitor' => (int) ($session->unread_from_visitor ?? 0),
            'unread_from_admin' => (int) ($session->unread_from_admin ?? 0),
            'assigned_admin_name' => $session->assignedAdmin?->name,
            'is_logged' => (bool) ($session->user_id ?: $visitor?->user_id),
            'visitor_ip' => $visitor?->ip_address,
            'visitor_referrer' => $visitor?->referrer_url,
            'visitor_timezone' => $visitor?->timezone,
            'visitor_language' => $visitor?->language,
            'visitor_screen_size' => $visitor?->screen_size,
            'visitor_browser' => $visitorMetadata['browser'] ?? null,
            'visitor_device_type' => $visitorMetadata['device_type'] ?? null,
            'visitor_platform' => $visitorMetadata['platform'] ?? null,
            'visitor_page_views_in_session' => isset($visitorMetadata['page_views_in_session']) ? (int) $visitorMetadata['page_views_in_session'] : null,
            'visitor_visits_count' => isset($visitorMetadata['visits_count']) ? (int) $visitorMetadata['visits_count'] : null,
            'visitor_utm_source' => $visitorMetadata['utm_source'] ?? null,
            'visitor_utm_medium' => $visitorMetadata['utm_medium'] ?? null,
            'visitor_utm_campaign' => $visitorMetadata['utm_campaign'] ?? null,
        ];
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
            'created_at' => optional($message->created_at)->toIso8601String(),
        ];
    }

    private function assertAdmin(): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
    }
}
