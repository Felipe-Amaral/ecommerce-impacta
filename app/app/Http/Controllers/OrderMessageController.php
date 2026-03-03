<?php

namespace App\Http\Controllers;

use App\Events\OrderMessageSent;
use App\Http\Requests\StoreOrderMessageRequest;
use App\Models\Order;
use App\Models\OrderMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderMessageController extends Controller
{
    public function index(Request $request, Order $order): JsonResponse
    {
        $user = $request->user();
        abort_unless($user, 403);
        $this->authorizeOrderChat($user, $order);

        $afterId = max(0, (int) $request->query('after_id', 0));

        $query = $order->messages()->with('user');
        if ($afterId > 0) {
            $query->where('id', '>', $afterId);
        }

        $messages = $query->limit(100)->get();

        // Mark read receipt for the viewer side.
        if ($messages->isNotEmpty()) {
            $readColumn = $user->is_admin ? 'read_by_admin_at' : 'read_by_client_at';
            OrderMessage::query()
                ->whereIn('id', $messages->pluck('id'))
                ->whereNull($readColumn)
                ->update([$readColumn => now()]);
        }

        return response()->json([
            'messages' => $messages->map(fn (OrderMessage $message): array => $this->serializeMessage($message))->all(),
            'last_id' => (int) ($messages->last()->id ?? $afterId),
        ]);
    }

    public function store(StoreOrderMessageRequest $request, Order $order): JsonResponse
    {
        $user = $request->user();
        abort_unless($user, 403);
        $this->authorizeOrderChat($user, $order);

        $senderRole = $user->is_admin ? 'admin' : 'client';

        $message = $order->messages()->create([
            'user_id' => $user->id,
            'sender_role' => $senderRole,
            'body' => trim((string) $request->validated()['body']),
            'metadata' => [
                'source' => 'chat_widget',
            ],
            'read_by_client_at' => $senderRole === 'client' ? now() : null,
            'read_by_admin_at' => $senderRole === 'admin' ? now() : null,
        ]);

        $message->load('user');

        broadcast(new OrderMessageSent($order, $message))->toOthers();

        return response()->json([
            'message' => $this->serializeMessage($message),
        ], 201);
    }

    private function authorizeOrderChat($user, Order $order): void
    {
        if ($user->is_admin) {
            return;
        }

        abort_unless($order->user_id !== null && $order->user_id === $user->id, 403);
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeMessage(OrderMessage $message): array
    {
        return [
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
        ];
    }
}
