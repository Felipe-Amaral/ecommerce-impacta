<?php

use App\Models\Order;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('orders.{orderId}.chat', function ($user, $orderId) {
    if (! $user) {
        return false;
    }

    if ($user->is_admin) {
        return true;
    }

    $order = Order::query()
        ->select(['id', 'user_id'])
        ->find((int) $orderId);

    return $order && $order->user_id !== null && (int) $order->user_id === (int) $user->id;
});
