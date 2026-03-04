<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactMessageController extends Controller
{
    /**
     * @var array<string, string>
     */
    private const STATUS_LABELS = [
        'new' => 'Novo',
        'in_progress' => 'Em andamento',
        'responded' => 'Respondido',
        'archived' => 'Arquivado',
        'spam' => 'Spam',
    ];

    public function index(Request $request): View
    {
        $this->assertAdmin();

        $search = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));
        $serviceInterest = trim((string) $request->query('service_interest', ''));
        $onlyUnread = (bool) $request->boolean('unread', false);

        $query = ContactMessage::query()
            ->with('user')
            ->latest('created_at');

        if ($search !== '') {
            $query->where(function ($inner) use ($search): void {
                $inner->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%")
                    ->orWhere('order_reference', 'like', "%{$search}%");
            });
        }

        if (array_key_exists($status, self::STATUS_LABELS)) {
            $query->where('status', $status);
        } else {
            $status = '';
        }

        if ($serviceInterest !== '') {
            $query->where('service_interest', $serviceInterest);
        }

        if ($onlyUnread) {
            $query->whereNull('read_at');
        }

        return view('admin.contacts.index', [
            'messages' => $query->paginate(20)->withQueryString(),
            'filters' => [
                'q' => $search,
                'status' => $status,
                'service_interest' => $serviceInterest,
                'unread' => $onlyUnread,
            ],
            'statusLabels' => self::STATUS_LABELS,
            'serviceOptions' => ContactMessage::query()
                ->whereNotNull('service_interest')
                ->where('service_interest', '!=', '')
                ->distinct()
                ->orderBy('service_interest')
                ->pluck('service_interest'),
            'stats' => [
                'total' => ContactMessage::query()->count(),
                'new' => ContactMessage::query()->where('status', 'new')->count(),
                'unread' => ContactMessage::query()->whereNull('read_at')->count(),
                'responded' => ContactMessage::query()->where('status', 'responded')->count(),
                'today' => ContactMessage::query()->whereDate('created_at', today())->count(),
            ],
        ]);
    }

    public function show(ContactMessage $contactMessage): View
    {
        $this->assertAdmin();

        if (! $contactMessage->read_at) {
            $contactMessage->forceFill(['read_at' => now()])->save();
            $contactMessage->refresh();
        }

        return view('admin.contacts.show', [
            'contactMessage' => $contactMessage->loadMissing('user'),
            'statusLabels' => self::STATUS_LABELS,
        ]);
    }

    public function updateStatus(Request $request, ContactMessage $contactMessage): RedirectResponse
    {
        $this->assertAdmin();

        $data = $request->validate([
            'status' => ['required', 'string', Rule::in(array_keys(self::STATUS_LABELS))],
            'mark_as_read' => ['nullable', 'boolean'],
        ]);

        $status = (string) $data['status'];
        $markAsRead = $request->boolean('mark_as_read', false);
        $updates = [
            'status' => $status,
        ];

        if ($markAsRead && ! $contactMessage->read_at) {
            $updates['read_at'] = now();
        }

        if ($status === 'responded' && ! $contactMessage->responded_at) {
            $updates['responded_at'] = now();
        }

        $contactMessage->forceFill($updates)->save();

        return back()->with('success', 'Status da mensagem atualizado.');
    }

    public function markRead(ContactMessage $contactMessage): RedirectResponse
    {
        $this->assertAdmin();

        if (! $contactMessage->read_at) {
            $contactMessage->forceFill(['read_at' => now()])->save();
        }

        return back()->with('success', 'Mensagem marcada como lida.');
    }

    private function assertAdmin(): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
    }
}
