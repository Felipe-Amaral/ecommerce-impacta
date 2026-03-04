<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactController extends Controller
{
    /**
     * @return array<string, string>
     */
    private function serviceOptions(): array
    {
        return [
            'orcamento' => 'Orçamento',
            'suporte-pedido' => 'Suporte de pedido',
            'servicos-graficos' => 'Serviços gráficos',
            'parceria' => 'Parcerias comerciais',
            'outros' => 'Outros assuntos',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function preferredContactOptions(): array
    {
        return [
            'email' => 'E-mail',
            'whatsapp' => 'WhatsApp',
            'phone' => 'Ligação',
        ];
    }

    public function show(): View
    {
        return view('store.pages.contact', [
            'serviceOptions' => $this->serviceOptions(),
            'preferredContactOptions' => $this->preferredContactOptions(),
            'formStartedAt' => now()->timestamp,
        ]);
    }

    public function store(StoreContactMessageRequest $request): RedirectResponse
    {
        $data = $request->validated();

        ContactMessage::query()->create([
            'user_id' => $request->user()?->id,
            'name' => trim((string) $data['name']),
            'email' => trim((string) $data['email']),
            'phone' => filled($data['phone'] ?? null) ? trim((string) $data['phone']) : null,
            'subject' => trim((string) $data['subject']),
            'service_interest' => filled($data['service_interest'] ?? null) ? (string) $data['service_interest'] : null,
            'preferred_contact' => filled($data['preferred_contact'] ?? null) ? (string) $data['preferred_contact'] : null,
            'order_reference' => filled($data['order_reference'] ?? null) ? trim((string) $data['order_reference']) : null,
            'message' => trim((string) $data['message']),
            'lgpd_consent' => (bool) $data['lgpd_consent'],
            'status' => 'new',
            'source_url' => route('pages.contact'),
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);

        return redirect()
            ->route('pages.contact')
            ->with('success', 'Mensagem enviada com sucesso. Nossa equipe retorna em até 1 dia útil.');
    }
}
