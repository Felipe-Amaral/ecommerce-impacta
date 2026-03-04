<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
            'servicos-graficos' => 'Serviços gráficos',
            'trafego-pago' => 'Tráfego pago e mídia de performance',
            'redes-sociais' => 'Gestão de redes sociais',
            'marketing-integrado' => 'Plano de marketing integrado',
            'tecnologia-octhopus' => 'Soluções de tecnologia (Octhopus Labs)',
            'suporte-pedido' => 'Suporte de pedido',
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

    public function show(Request $request): View
    {
        $serviceOptions = $this->serviceOptions();
        $prefillServiceInterest = trim((string) $request->query('service_interest', ''));
        if (! array_key_exists($prefillServiceInterest, $serviceOptions)) {
            $prefillServiceInterest = '';
        }

        return view('store.pages.contact', [
            'serviceOptions' => $serviceOptions,
            'preferredContactOptions' => $this->preferredContactOptions(),
            'formStartedAt' => now()->timestamp,
            'prefillServiceInterest' => $prefillServiceInterest,
            'prefillSubject' => Str::limit(trim((string) $request->query('subject', '')), 140, ''),
            'prefillMessage' => Str::limit(trim((string) $request->query('message', '')), 5000, ''),
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
