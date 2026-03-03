<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FulfillmentStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderWorkflowRequest;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(auth()->user()?->is_admin, 403);

        $baseQuery = Order::query();
        $query = Order::query()
            ->withCount([
                'items',
                'messages',
                'items as pending_file_items_count' => fn ($q) => $q->where('production_status', 'pending_file'),
            ])
            ->with(['payments', 'user'])
            ->latest();

        $search = trim((string) $request->query('q', ''));
        $orderStatus = (string) $request->query('status', '');
        $paymentStatus = (string) $request->query('payment_status', '');
        $fulfillmentStatus = (string) $request->query('fulfillment_status', '');
        $awaitingArtwork = (bool) $request->boolean('awaiting_artwork', false);

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        $validOrderStatuses = array_map(static fn (OrderStatus $status): string => $status->value, OrderStatus::cases());
        if (in_array($orderStatus, $validOrderStatuses, true)) {
            $query->where('status', $orderStatus);
        } else {
            $orderStatus = '';
        }

        $validPaymentStatuses = array_map(static fn (PaymentStatus $status): string => $status->value, PaymentStatus::cases());
        if (in_array($paymentStatus, $validPaymentStatuses, true)) {
            $query->where('payment_status', $paymentStatus);
        } else {
            $paymentStatus = '';
        }

        $validFulfillmentStatuses = array_map(static fn (FulfillmentStatus $status): string => $status->value, FulfillmentStatus::cases());
        if (in_array($fulfillmentStatus, $validFulfillmentStatuses, true)) {
            $query->where('fulfillment_status', $fulfillmentStatus);
        } else {
            $fulfillmentStatus = '';
        }

        if ($awaitingArtwork) {
            $query->whereHas('items', fn ($q) => $q->where('production_status', 'pending_file'));
        }

        $orders = $query->paginate(20)->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'filters' => [
                'q' => $search,
                'status' => $orderStatus,
                'payment_status' => $paymentStatus,
                'fulfillment_status' => $fulfillmentStatus,
                'awaiting_artwork' => $awaitingArtwork,
            ],
            'stats' => [
                'total_orders' => (clone $baseQuery)->count(),
                'pending_payment' => (clone $baseQuery)->where('payment_status', PaymentStatus::Pending->value)->count(),
                'in_production' => (clone $baseQuery)->where('status', OrderStatus::InProduction->value)->count(),
                'awaiting_artwork_items' => OrderItem::query()->where('production_status', 'pending_file')->count(),
                'today_orders' => (clone $baseQuery)->whereDate('created_at', today())->count(),
                'today_revenue' => (float) (clone $baseQuery)->whereDate('created_at', today())->sum('total'),
            ],
        ]);
    }

    public function show(Order $order): View
    {
        abort_unless(auth()->user()?->is_admin, 403);

        $order->load([
            'items.artworkFiles' => fn ($query) => $query->latest(),
            'items.artworkFiles.uploadedBy',
            'payments',
            'messages.user',
            'statusHistory' => fn ($query) => $query->latest('created_at')->limit(20),
        ]);

        return view('admin.orders.show', compact('order'));
    }

    public function updateWorkflow(UpdateOrderWorkflowRequest $request, Order $order): RedirectResponse
    {
        abort_unless(auth()->user()?->is_admin, 403);

        $data = $request->validated();
        $oldOrderStatus = $order->status->value;
        $oldPaymentStatus = $order->payment_status->value;

        $order->status = $data['status'];
        $order->payment_status = $data['payment_status'];
        $order->fulfillment_status = $data['fulfillment_status'];

        if ($order->payment_status === PaymentStatus::Paid && $order->paid_at === null) {
            $order->paid_at = now();
        }

        $order->save();

        $payment = $order->payments()->latest()->first();
        if ($payment) {
            $payment->status = $data['payment_status'];
            if ($payment->status === PaymentStatus::Paid && $payment->paid_at === null) {
                $payment->paid_at = now();
            }
            $payment->save();
        }

        $order->statusHistory()->create([
            'from_status' => $oldOrderStatus,
            'to_status' => $order->status->value,
            'actor_type' => 'admin',
            'actor_id' => $request->user()?->id,
            'message' => $data['message'] ?: 'Status atualizado manualmente no painel da gráfica.',
            'metadata' => [
                'payment_status_from' => $oldPaymentStatus,
                'payment_status_to' => $order->payment_status->value,
                'fulfillment_status' => $order->fulfillment_status->value,
            ],
            'created_at' => now(),
        ]);

        return back()->with('success', 'Status do pedido atualizado.');
    }
}
