<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveChatMessage;
use App\Models\LiveChatSession;
use App\Models\LiveVisitor;
use App\Models\LiveVisitorPageView;
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
        $this->closeInactivePageViews();

        $visitors = $this->activeVisitorsPayload();
        $sessions = $this->openSessionsPayload();
        $consultantsOnline = $this->consultantsOnlineCount();

        return view('admin.livechat.index', [
            'initialVisitors' => $visitors,
            'initialSessions' => $sessions,
            'initialTracking' => $this->trackingPayload(),
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
        $this->closeInactivePageViews();

        $visitors = $this->activeVisitorsPayload();
        $sessions = $this->openSessionsPayload();

        return response()->json([
            'ok' => true,
            'server_time' => now()->toIso8601String(),
            'consultants_online' => $this->consultantsOnlineCount(),
            'active_visitors' => $visitors,
            'sessions' => $sessions,
            'tracking' => $this->trackingPayload(),
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

        $activeVisitors = $visitors
            ->filter(fn (LiveVisitor $visitor): bool => ! (bool) ($visitor->user?->is_admin))
            ->values();

        $journeysByToken = $this->journeysByVisitorToken($activeVisitors);

        return $activeVisitors
            ->map(function (LiveVisitor $visitor) use ($journeysByToken): array {
                $metadata = is_array($visitor->metadata) ? $visitor->metadata : [];
                $pageStartedAt = (int) ($metadata['page_started_at'] ?? 0);
                $pageSeconds = $pageStartedAt > 0 ? max(0, now()->timestamp - $pageStartedAt) : null;
                $journey = $journeysByToken[$visitor->visitor_token] ?? [];
                $lastExit = collect($journey)
                    ->reverse()
                    ->first(fn (array $step): bool => ! (bool) ($step['is_current'] ?? false));

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
                    'journey' => $journey,
                    'last_exit_path' => $lastExit['path'] ?? null,
                    'last_exit_at' => $lastExit['left_at'] ?? null,
                    'last_exit_type' => $lastExit['exit_type'] ?? null,
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

    /**
     * @param  Collection<int, LiveVisitor>  $visitors
     * @return array<string, array<int, array<string, mixed>>>
     */
    private function journeysByVisitorToken(Collection $visitors): array
    {
        $tokens = $visitors
            ->pluck('visitor_token')
            ->filter()
            ->unique()
            ->values();

        if ($tokens->isEmpty()) {
            return [];
        }

        $viewsByToken = LiveVisitorPageView::query()
            ->whereIn('visitor_token', $tokens->all())
            ->where('entered_at', '>=', now()->subDays(3))
            ->orderByDesc('id')
            ->take(4500)
            ->get()
            ->groupBy('visitor_token');

        $journeys = [];

        foreach ($visitors as $visitor) {
            $views = $viewsByToken->get($visitor->visitor_token, collect());

            if ($visitor->session_id) {
                $sessionViews = $views->filter(fn (LiveVisitorPageView $view): bool => $view->session_id === $visitor->session_id);
                if ($sessionViews->isNotEmpty()) {
                    $views = $sessionViews;
                }
            }

            $sorted = $views->sortBy('entered_at')->values();
            if ($sorted->count() > 14) {
                $sorted = $sorted->slice($sorted->count() - 14)->values();
            }

            $journeys[$visitor->visitor_token] = $sorted
                ->map(function (LiveVisitorPageView $view): array {
                    $enteredAt = $view->entered_at;
                    $leftAt = $view->left_at;
                    $durationSeconds = $leftAt
                        ? max(1, (int) ($view->duration_seconds ?? max(1, $leftAt->diffInSeconds($enteredAt ?: $leftAt))))
                        : ($enteredAt ? now()->diffInSeconds($enteredAt) : null);

                    $path = $view->path;
                    if (! $path && $view->url) {
                        $urlPath = parse_url($view->url, PHP_URL_PATH);
                        $path = is_string($urlPath) && $urlPath !== '' ? $urlPath : '/';
                    }

                    return [
                        'path' => $path ?: '/',
                        'url' => $view->url,
                        'page_title' => $view->page_title,
                        'entered_at' => optional($enteredAt)->toIso8601String(),
                        'left_at' => optional($leftAt)->toIso8601String(),
                        'duration_seconds' => $durationSeconds,
                        'exit_type' => $leftAt ? ($view->exit_type ?: 'navigation') : null,
                        'is_current' => ! (bool) $leftAt,
                    ];
                })
                ->values()
                ->all();
        }

        return $journeys;
    }

    private function closeInactivePageViews(): void
    {
        $staleVisitors = LiveVisitor::query()
            ->whereNotNull('last_seen_at')
            ->where('last_seen_at', '<', now()->subSeconds(140))
            ->select(['visitor_token', 'session_id', 'last_seen_at'])
            ->take(600)
            ->get();

        foreach ($staleVisitors as $staleVisitor) {
            if (! $staleVisitor->session_id) {
                continue;
            }

            /** @var LiveVisitorPageView|null $openView */
            $openView = LiveVisitorPageView::query()
                ->where('visitor_token', $staleVisitor->visitor_token)
                ->where('session_id', $staleVisitor->session_id)
                ->whereNull('left_at')
                ->latest('id')
                ->first();

            if (! $openView) {
                continue;
            }

            $leftAt = $staleVisitor->last_seen_at ?: now();
            $enteredAt = $openView->entered_at ?: $openView->created_at ?: $leftAt;
            $durationSeconds = max(1, $leftAt->diffInSeconds($enteredAt));

            $openView->forceFill([
                'left_at' => $leftAt,
                'duration_seconds' => $durationSeconds,
                'exit_type' => 'inactivity',
            ])->save();
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function trackingPayload(): array
    {
        $now = now();
        $timelineStart = $now->copy()->subMinutes(59)->startOfMinute();
        $timelineEnd = $now->copy()->startOfMinute();
        $rangeStart24h = $now->copy()->subHours(24);

        $labels = [];
        $minuteKeys = [];
        $cursor = $timelineStart->copy();

        while ($cursor->lte($timelineEnd)) {
            $minuteKey = $cursor->format('Y-m-d H:i:00');
            $minuteKeys[] = $minuteKey;
            $labels[] = $cursor->format('H:i');
            $cursor->addMinute();
        }

        $entriesMap = array_fill_keys($minuteKeys, 0);
        $exitsMap = array_fill_keys($minuteKeys, 0);

        $enteredRows = LiveVisitorPageView::query()
            ->whereBetween('entered_at', [$timelineStart, $timelineEnd->copy()->endOfMinute()])
            ->selectRaw("DATE_FORMAT(entered_at, '%Y-%m-%d %H:%i:00') as minute_key, COUNT(*) as total")
            ->groupBy('minute_key')
            ->get();

        foreach ($enteredRows as $row) {
            $minuteKey = (string) $row->minute_key;
            if (array_key_exists($minuteKey, $entriesMap)) {
                $entriesMap[$minuteKey] = (int) $row->total;
            }
        }

        $exitRows = LiveVisitorPageView::query()
            ->whereNotNull('left_at')
            ->whereBetween('left_at', [$timelineStart, $timelineEnd->copy()->endOfMinute()])
            ->selectRaw("DATE_FORMAT(left_at, '%Y-%m-%d %H:%i:00') as minute_key, COUNT(*) as total")
            ->groupBy('minute_key')
            ->get();

        foreach ($exitRows as $row) {
            $minuteKey = (string) $row->minute_key;
            if (array_key_exists($minuteKey, $exitsMap)) {
                $exitsMap[$minuteKey] = (int) $row->total;
            }
        }

        $topPaths = LiveVisitorPageView::query()
            ->where('entered_at', '>=', $rangeStart24h)
            ->selectRaw("COALESCE(NULLIF(path, ''), '/') as normalized_path, COUNT(*) as views, AVG(COALESCE(duration_seconds, 0)) as avg_duration_seconds, SUM(COALESCE(duration_seconds, 0)) as total_duration_seconds")
            ->groupByRaw("COALESCE(NULLIF(path, ''), '/')")
            ->orderByDesc('views')
            ->limit(10)
            ->get()
            ->map(fn (LiveVisitorPageView $view): array => [
                'path' => (string) ($view->normalized_path ?? '/'),
                'views' => (int) ($view->views ?? 0),
                'avg_duration_seconds' => max(0, (int) round((float) ($view->avg_duration_seconds ?? 0))),
                'total_duration_seconds' => max(0, (int) ($view->total_duration_seconds ?? 0)),
            ])
            ->values()
            ->all();

        $activePaths = LiveVisitor::query()
            ->active(120)
            ->with('user:id,is_admin')
            ->get()
            ->filter(fn (LiveVisitor $visitor): bool => ! (bool) ($visitor->user?->is_admin))
            ->groupBy(fn (LiveVisitor $visitor): string => $visitor->current_path ?: '/')
            ->map(fn (Collection $collection, string $path): array => [
                'path' => $path,
                'active' => $collection->count(),
            ])
            ->sortByDesc('active')
            ->take(10)
            ->values()
            ->all();

        $exitTypes = LiveVisitorPageView::query()
            ->whereNotNull('left_at')
            ->where('left_at', '>=', $rangeStart24h)
            ->selectRaw("COALESCE(NULLIF(exit_type, ''), 'navigation') as normalized_exit_type, COUNT(*) as total")
            ->groupByRaw("COALESCE(NULLIF(exit_type, ''), 'navigation')")
            ->orderByDesc('total')
            ->get()
            ->map(fn (LiveVisitorPageView $view): array => [
                'type' => (string) ($view->normalized_exit_type ?? 'navigation'),
                'count' => (int) ($view->total ?? 0),
            ])
            ->values()
            ->all();

        $pageViews24h = LiveVisitorPageView::query()
            ->where('entered_at', '>=', $rangeStart24h)
            ->count();

        $avgDuration24h = (int) round((float) LiveVisitorPageView::query()
            ->where('entered_at', '>=', $rangeStart24h)
            ->whereNotNull('duration_seconds')
            ->avg('duration_seconds'));

        $exits24h = LiveVisitorPageView::query()
            ->whereNotNull('left_at')
            ->where('left_at', '>=', $rangeStart24h)
            ->count();

        $activeVisitorsNow = LiveVisitor::query()
            ->active(120)
            ->with('user:id,is_admin')
            ->get()
            ->filter(fn (LiveVisitor $visitor): bool => ! (bool) ($visitor->user?->is_admin))
            ->count();

        return [
            'summary' => [
                'active_visitors_now' => $activeVisitorsNow,
                'page_views_24h' => (int) $pageViews24h,
                'avg_duration_seconds_24h' => max(0, $avgDuration24h),
                'exits_24h' => (int) $exits24h,
            ],
            'timeline' => [
                'labels' => $labels,
                'entries' => array_values($entriesMap),
                'exits' => array_values($exitsMap),
            ],
            'top_paths' => $topPaths,
            'active_paths' => $activePaths,
            'exit_types' => $exitTypes,
        ];
    }

    private function assertAdmin(): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
    }
}
