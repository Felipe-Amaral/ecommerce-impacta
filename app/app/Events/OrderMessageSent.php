<?php

namespace App\Events;

use App\Models\Order;
use App\Models\OrderMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderMessageSent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly Order $order,
        public readonly OrderMessage $message,
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('orders.'.$this->order->id.'.chat'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'message' => [
                'id' => $this->message->id,
                'sender_role' => $this->message->sender_role,
                'sender_name' => $this->message->user?->name ?: ($this->message->sender_role === 'admin' ? 'Atendimento da gráfica' : 'Sistema'),
                'body' => $this->message->body,
                'created_at' => optional($this->message->created_at)->toIso8601String(),
                'created_at_human' => optional($this->message->created_at)->format('d/m/Y H:i'),
            ],
        ];
    }
}
