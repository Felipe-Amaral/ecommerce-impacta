<?php

namespace App\Http\Controllers\Account;

use App\Enums\ArtworkFileStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArtworkUploadRequest;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user, 403);

        $query = $user->orders()
            ->withCount(['items', 'messages'])
            ->with(['payments'])
            ->latest();

        $orderStatus = (string) $request->query('status', '');
        $paymentStatus = (string) $request->query('payment_status', '');
        $search = trim((string) $request->query('q', ''));

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

        $orders = $query->paginate(12)->withQueryString();

        return view('account.orders.index', [
            'orders' => $orders,
            'stats' => [
                'total' => $user->orders()->count(),
                'pending_payment' => $user->orders()->where('payment_status', PaymentStatus::Pending->value)->count(),
                'in_production' => $user->orders()->whereIn('status', [
                    OrderStatus::Paid->value,
                    OrderStatus::InProduction->value,
                    OrderStatus::Shipped->value,
                ])->count(),
                'delivered' => $user->orders()->where('status', OrderStatus::Delivered->value)->count(),
                'total_value' => (float) $user->orders()->sum('total'),
            ],
            'filters' => [
                'q' => $search,
                'status' => $orderStatus,
                'payment_status' => $paymentStatus,
            ],
        ]);
    }

    public function show(Order $order): View
    {
        abort_unless(auth()->check(), 403);
        abort_unless($order->user_id === auth()->id(), 403);

        $order->load([
            'items.artworkFiles' => fn ($query) => $query->latest(),
            'items.artworkFiles.uploadedBy',
            'payments',
            'messages.user',
            'statusHistory' => fn ($query) => $query->latest('created_at')->limit(12),
        ]);

        return view('account.orders.show', compact('order'));
    }

    public function uploadArtwork(ArtworkUploadRequest $request, Order $order, OrderItem $item): RedirectResponse
    {
        abort_unless($order->user_id === auth()->id(), 403);
        abort_unless($item->order_id === $order->id, 404);

        $uploadedFile = $request->file('file');
        $extension = $uploadedFile->getClientOriginalExtension();
        $safeBaseName = Str::slug(pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME));
        $filename = now()->format('Ymd_His').'-'.Str::limit($safeBaseName !== '' ? $safeBaseName : 'arte-final', 40, '').($extension ? '.'.$extension : '');
        $path = $uploadedFile->storeAs(
            'artworks/'.$order->order_number.'/item-'.$item->id,
            $filename,
            'public',
        );

        $item->artworkFiles()->create([
            'storage_disk' => 'public',
            'path' => $path,
            'original_name' => $uploadedFile->getClientOriginalName(),
            'mime_type' => $uploadedFile->getMimeType(),
            'size_bytes' => $uploadedFile->getSize(),
            'checklist' => (array) $request->input('checklist', []),
            'status' => ArtworkFileStatus::Uploaded,
            'review_notes' => null,
            'metadata' => [
                'customer_notes' => $request->input('customer_notes'),
                'uploaded_at' => now()->toIso8601String(),
            ],
            'uploaded_by_user_id' => $request->user()?->id,
        ]);

        if ($item->production_status === 'pending_file') {
            $item->forceFill(['production_status' => 'file_sent'])->save();
        }

        return back()->with('success', 'Arte final enviada com sucesso. A equipe fará a conferência técnica.');
    }
}
