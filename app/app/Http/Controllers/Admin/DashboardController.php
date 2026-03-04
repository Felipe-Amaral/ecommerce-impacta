<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FulfillmentStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        abort_unless(auth()->user()?->is_admin, 403);

        $recentOrders = Order::query()
            ->withCount('items')
            ->with(['user', 'payments'])
            ->latest()
            ->limit(14)
            ->get();

        $todayOrders = Order::query()
            ->whereDate('created_at', today())
            ->count();

        $todayRevenue = (float) Order::query()
            ->whereDate('created_at', today())
            ->sum('total');

        $pendingPayment = Order::query()
            ->where('payment_status', PaymentStatus::Pending->value)
            ->count();

        $productionOpen = Order::query()
            ->whereIn('fulfillment_status', [
                FulfillmentStatus::Pending->value,
                FulfillmentStatus::Prepress->value,
                FulfillmentStatus::Approved->value,
                FulfillmentStatus::Printing->value,
                FulfillmentStatus::Finishing->value,
            ])
            ->count();

        $queueByFulfillment = collect(FulfillmentStatus::cases())
            ->map(function (FulfillmentStatus $status): array {
                return [
                    'status' => $status,
                    'count' => Order::query()
                        ->where('fulfillment_status', $status->value)
                        ->count(),
                ];
            });

        $pendingFileItems = OrderItem::query()
            ->with('order')
            ->where('production_status', 'pending_file')
            ->latest()
            ->limit(10)
            ->get();

        $unreadContacts = ContactMessage::query()
            ->whereNull('read_at')
            ->count();

        $todayContacts = ContactMessage::query()
            ->whereDate('created_at', today())
            ->count();

        return view('admin.dashboard', [
            'recentOrders' => $recentOrders,
            'todayOrders' => $todayOrders,
            'todayRevenue' => $todayRevenue,
            'pendingPayment' => $pendingPayment,
            'productionOpen' => $productionOpen,
            'queueByFulfillment' => $queueByFulfillment,
            'pendingFileItems' => $pendingFileItems,
            'unreadContacts' => $unreadContacts,
            'todayContacts' => $todayContacts,
        ]);
    }
}
