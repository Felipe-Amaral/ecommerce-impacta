<?php

namespace Database\Seeders;

use App\Enums\ArtworkFileStatus;
use App\Enums\FulfillmentStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Address;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DemoOrdersSeeder extends Seeder
{
    public function run(): void
    {
        $variants = ProductVariant::query()
            ->with('product')
            ->where('is_active', true)
            ->orderBy('id')
            ->get()
            ->keyBy('sku');

        if ($variants->isEmpty()) {
            return;
        }

        $profiles = $this->customerProfiles();
        $this->seedAddresses($profiles);
        $admin = User::query()->firstWhere('email', 'admin@graficaimpacta.local');

        Order::query()
            ->where('order_number', 'like', 'VRTDEMO-%')
            ->delete();

        Storage::disk('public')->deleteDirectory('artworks/demo-orders');

        $shippingProfiles = $this->shippingProfiles();
        $now = now();

        $scenarios = [
            [
                'profile' => 'cliente_demo',
                'hours_ago' => 1,
                'status' => OrderStatus::PendingPayment->value,
                'payment_status' => PaymentStatus::Pending->value,
                'fulfillment_status' => FulfillmentStatus::Pending->value,
                'payment_method' => PaymentMethod::Pix->value,
                'payment_provider' => 'mercadopago',
                'shipping' => 'pickup',
                'items' => [
                    ['sku' => 'CVP-500-C300-LF', 'qty' => 1, 'production_status' => 'pending_file'],
                ],
                'notes' => 'Cliente solicitou retirada no balcão e contato por WhatsApp.',
            ],
            [
                'profile' => 'cliente_demo',
                'hours_ago' => 3,
                'status' => OrderStatus::PendingPayment->value,
                'payment_status' => PaymentStatus::Pending->value,
                'fulfillment_status' => FulfillmentStatus::Pending->value,
                'payment_method' => PaymentMethod::Boleto->value,
                'payment_provider' => 'mercadopago',
                'shipping' => 'pac',
                'items' => [
                    ['sku' => 'FLA5-1000-C115-44', 'qty' => 1, 'production_status' => 'pending_file'],
                ],
                'notes' => 'Aguardando compensação do boleto para liberar atendimento.',
            ],
            [
                'profile' => 'cliente_demo',
                'hours_ago' => 6,
                'status' => OrderStatus::PendingPayment->value,
                'payment_status' => PaymentStatus::Pending->value,
                'fulfillment_status' => FulfillmentStatus::Pending->value,
                'payment_method' => PaymentMethod::CreditCard->value,
                'payment_provider' => 'mercadopago',
                'shipping' => 'sedex',
                'items' => [
                    [
                        'sku' => 'ETQ-100-5X5-BR',
                        'qty' => 2,
                        'production_status' => 'file_sent',
                        'artwork' => ['status' => ArtworkFileStatus::Uploaded->value],
                    ],
                ],
                'notes' => 'Arte enviada junto ao pedido, aguardando retorno de cobrança.',
            ],
            [
                'profile' => 'agencia_lume',
                'hours_ago' => 9,
                'status' => OrderStatus::Paid->value,
                'payment_status' => PaymentStatus::Paid->value,
                'fulfillment_status' => FulfillmentStatus::Prepress->value,
                'payment_method' => PaymentMethod::Pix->value,
                'payment_provider' => 'mercadopago',
                'shipping' => 'carrier',
                'items' => [
                    [
                        'sku' => 'BNR-80X120-IL',
                        'qty' => 2,
                        'production_status' => 'file_under_review',
                        'artwork' => ['status' => ArtworkFileStatus::UnderReview->value],
                    ],
                ],
                'notes' => 'Cliente pediu validação de cor antes de imprimir o lote final.',
            ],
            [
                'profile' => 'lojista_norte',
                'hours_ago' => 12,
                'status' => OrderStatus::Paid->value,
                'payment_status' => PaymentStatus::Paid->value,
                'fulfillment_status' => FulfillmentStatus::Approved->value,
                'payment_method' => PaymentMethod::Pix->value,
                'payment_provider' => 'mercadopago',
                'shipping' => 'pac',
                'items' => [
                    [
                        'sku' => 'CVP-1000-C300-VL',
                        'qty' => 1,
                        'production_status' => 'file_approved',
                        'artwork' => ['status' => ArtworkFileStatus::Approved->value],
                    ],
                ],
                'notes' => 'Arte aprovada e aguardando encaixe de produção.',
            ],
            [
                'profile' => 'cliente_demo',
                'hours_ago' => 20,
                'status' => OrderStatus::InProduction->value,
                'payment_status' => PaymentStatus::Paid->value,
                'fulfillment_status' => FulfillmentStatus::Printing->value,
                'payment_method' => PaymentMethod::CreditCard->value,
                'payment_provider' => 'mercadopago',
                'shipping' => 'sedex',
                'items' => [
                    [
                        'sku' => 'FLA5-2500-C150-44',
                        'qty' => 1,
                        'production_status' => 'printing',
                        'artwork' => ['status' => ArtworkFileStatus::Approved->value],
                    ],
                ],
                'notes' => 'Pedido de campanha com prazo curto, imprimir no mesmo dia.',
            ],
            [
                'profile' => 'agencia_lume',
                'hours_ago' => 26,
                'status' => OrderStatus::InProduction->value,
                'payment_status' => PaymentStatus::Paid->value,
                'fulfillment_status' => FulfillmentStatus::Finishing->value,
                'payment_method' => PaymentMethod::BankTransfer->value,
                'payment_provider' => 'manual',
                'shipping' => 'carrier',
                'items' => [
                    [
                        'sku' => 'BNR-100X150-IR',
                        'qty' => 1,
                        'production_status' => 'finishing',
                        'artwork' => ['status' => ArtworkFileStatus::Approved->value],
                    ],
                    [
                        'sku' => 'FLA5-500-C115-44',
                        'qty' => 2,
                        'production_status' => 'finishing',
                        'artwork' => ['status' => ArtworkFileStatus::Approved->value],
                    ],
                ],
                'notes' => 'Acabamento final e conferência de embalagem.',
            ],
            [
                'profile' => 'lojista_norte',
                'hours_ago' => 34,
                'status' => OrderStatus::Shipped->value,
                'payment_status' => PaymentStatus::Paid->value,
                'fulfillment_status' => FulfillmentStatus::Shipped->value,
                'payment_method' => PaymentMethod::Boleto->value,
                'payment_provider' => 'mercadopago',
                'shipping' => 'carrier',
                'items' => [
                    [
                        'sku' => 'ETQ-500-5X5-FO',
                        'qty' => 1,
                        'production_status' => 'shipped',
                        'artwork' => ['status' => ArtworkFileStatus::Approved->value],
                    ],
                ],
                'notes' => 'Pedido expedido para transportadora econômica.',
            ],
            [
                'profile' => 'cliente_demo',
                'hours_ago' => 46,
                'status' => OrderStatus::Delivered->value,
                'payment_status' => PaymentStatus::Paid->value,
                'fulfillment_status' => FulfillmentStatus::Delivered->value,
                'payment_method' => PaymentMethod::Pix->value,
                'payment_provider' => 'mercadopago',
                'shipping' => 'pickup',
                'items' => [
                    [
                        'sku' => 'CVP-100-C300-44',
                        'qty' => 3,
                        'production_status' => 'delivered',
                        'artwork' => ['status' => ArtworkFileStatus::Approved->value],
                    ],
                ],
                'notes' => 'Retirado no balcão pelo cliente.',
            ],
            [
                'profile' => 'cliente_demo',
                'hours_ago' => 58,
                'status' => OrderStatus::Delivered->value,
                'payment_status' => PaymentStatus::Paid->value,
                'fulfillment_status' => FulfillmentStatus::Delivered->value,
                'payment_method' => PaymentMethod::CreditCard->value,
                'payment_provider' => 'mercadopago',
                'shipping' => 'pac',
                'items' => [
                    [
                        'sku' => 'FLA5-1000-C115-44',
                        'qty' => 1,
                        'production_status' => 'delivered',
                        'artwork' => ['status' => ArtworkFileStatus::Approved->value],
                    ],
                    [
                        'sku' => 'CVP-500-C300-LF',
                        'qty' => 1,
                        'production_status' => 'delivered',
                        'artwork' => ['status' => ArtworkFileStatus::Approved->value],
                    ],
                ],
                'notes' => 'Pedido entregue via Correios com confirmação de recebimento.',
            ],
            [
                'profile' => 'agencia_lume',
                'hours_ago' => 72,
                'status' => OrderStatus::PendingPayment->value,
                'payment_status' => PaymentStatus::Failed->value,
                'fulfillment_status' => FulfillmentStatus::Canceled->value,
                'payment_method' => PaymentMethod::CreditCard->value,
                'payment_provider' => 'mercadopago',
                'shipping' => 'sedex',
                'items' => [
                    ['sku' => 'BNR-60X90-BC', 'qty' => 1, 'production_status' => 'pending_file'],
                ],
                'notes' => 'Tentativa de cartão recusada; pedido aguardando novo pagamento.',
            ],
            [
                'profile' => 'lojista_norte',
                'hours_ago' => 84,
                'status' => OrderStatus::Paid->value,
                'payment_status' => PaymentStatus::Paid->value,
                'fulfillment_status' => FulfillmentStatus::Prepress->value,
                'payment_method' => PaymentMethod::Pix->value,
                'payment_provider' => 'mercadopago',
                'shipping' => 'pac',
                'items' => [
                    [
                        'sku' => 'ETQ-500-5X5-FO',
                        'qty' => 1,
                        'production_status' => 'file_adjustment_requested',
                        'artwork' => ['status' => ArtworkFileStatus::NeedsAdjustment->value],
                    ],
                ],
                'notes' => 'Arquivo com baixa resolução; solicitado ajuste ao cliente.',
            ],
            [
                'profile' => 'guest_corporativo',
                'hours_ago' => 96,
                'status' => OrderStatus::PendingPayment->value,
                'payment_status' => PaymentStatus::Pending->value,
                'fulfillment_status' => FulfillmentStatus::Pending->value,
                'payment_method' => PaymentMethod::Boleto->value,
                'payment_provider' => 'manual',
                'shipping' => 'carrier',
                'items' => [
                    ['sku' => 'CVP-500-C300-LF', 'qty' => 2, 'production_status' => 'pending_file'],
                    ['sku' => 'FLA5-500-C115-44', 'qty' => 1, 'production_status' => 'pending_file'],
                ],
                'notes' => 'Pedido feito como convidado para orçamento recorrente.',
            ],
            [
                'profile' => 'cliente_demo',
                'hours_ago' => 110,
                'status' => OrderStatus::InProduction->value,
                'payment_status' => PaymentStatus::Paid->value,
                'fulfillment_status' => FulfillmentStatus::Printing->value,
                'payment_method' => PaymentMethod::Pix->value,
                'payment_provider' => 'mercadopago',
                'shipping' => 'pac',
                'items' => [
                    [
                        'sku' => 'CVP-500-C300-LF',
                        'qty' => 1,
                        'production_status' => 'printing',
                        'artwork' => ['status' => ArtworkFileStatus::Approved->value],
                    ],
                    [
                        'sku' => 'ETQ-100-5X5-BR',
                        'qty' => 1,
                        'production_status' => 'printing',
                        'artwork' => ['status' => ArtworkFileStatus::Approved->value],
                    ],
                ],
                'notes' => 'Produção conjunta de papelaria e rótulos.',
            ],
            [
                'profile' => 'agencia_lume',
                'hours_ago' => 132,
                'status' => OrderStatus::Delivered->value,
                'payment_status' => PaymentStatus::Paid->value,
                'fulfillment_status' => FulfillmentStatus::Delivered->value,
                'payment_method' => PaymentMethod::BankTransfer->value,
                'payment_provider' => 'manual',
                'shipping' => 'sedex',
                'items' => [
                    [
                        'sku' => 'BNR-80X120-IL',
                        'qty' => 1,
                        'production_status' => 'delivered',
                        'artwork' => ['status' => ArtworkFileStatus::Approved->value],
                    ],
                ],
                'notes' => 'Cliente confirmou recebimento e qualidade da impressão.',
            ],
            [
                'profile' => 'guest_evento',
                'hours_ago' => 150,
                'status' => OrderStatus::Shipped->value,
                'payment_status' => PaymentStatus::Paid->value,
                'fulfillment_status' => FulfillmentStatus::Shipped->value,
                'payment_method' => PaymentMethod::Pix->value,
                'payment_provider' => 'mercadopago',
                'shipping' => 'sedex',
                'items' => [
                    [
                        'sku' => 'BNR-60X90-BC',
                        'qty' => 4,
                        'production_status' => 'shipped',
                        'artwork' => ['status' => ArtworkFileStatus::Approved->value],
                    ],
                ],
                'notes' => 'Pedido para evento promocional em trânsito.',
            ],
        ];

        Model::unguarded(function () use ($scenarios, $profiles, $variants, $shippingProfiles, $admin, $now): void {
            foreach ($scenarios as $index => $scenario) {
                $placedAt = $now->copy()->subHours((int) $scenario['hours_ago']);
                $this->createDemoOrder(
                    orderNumber: sprintf('VRTDEMO-%04d', $index + 1),
                    placedAt: $placedAt,
                    scenario: $scenario,
                    profile: $profiles[$scenario['profile']],
                    variants: $variants,
                    shippingProfile: $shippingProfiles[$scenario['shipping']],
                    admin: $admin,
                );
            }
        });
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function customerProfiles(): array
    {
        $clienteDemo = User::query()->firstWhere('email', 'cliente@graficaimpacta.local');

        $agenciaLume = User::query()->updateOrCreate(
            ['email' => 'agencia.lume@graficaimpacta.local'],
            [
                'name' => 'Agência Lume',
                'phone' => '(11) 97777-3300',
                'document' => '12.345.678/0001-90',
                'is_admin' => false,
                'password' => Hash::make('password'),
            ],
        );

        $lojistaNorte = User::query()->updateOrCreate(
            ['email' => 'compras.lojistanorte@graficaimpacta.local'],
            [
                'name' => 'Lojista Norte',
                'phone' => '(11) 96666-2200',
                'document' => '45.987.123/0001-55',
                'is_admin' => false,
                'password' => Hash::make('password'),
            ],
        );

        return [
            'cliente_demo' => [
                'user' => $clienteDemo,
                'name' => 'Cliente Demo',
                'email' => 'cliente@graficaimpacta.local',
                'phone' => '(11) 98888-0000',
                'document' => '123.456.789-00',
                'address' => [
                    'recipient_name' => 'Cliente Demo',
                    'phone' => '(11) 98888-0000',
                    'zipcode' => '01310-100',
                    'street' => 'Avenida Paulista',
                    'number' => '1000',
                    'complement' => 'Conj. 101',
                    'district' => 'Bela Vista',
                    'city' => 'São Paulo',
                    'state' => 'SP',
                    'country' => 'BR',
                ],
            ],
            'agencia_lume' => [
                'user' => $agenciaLume,
                'name' => 'Agência Lume Comunicação',
                'email' => 'agencia.lume@graficaimpacta.local',
                'phone' => '(11) 97777-3300',
                'document' => '12.345.678/0001-90',
                'address' => [
                    'recipient_name' => 'Agência Lume Comunicação',
                    'phone' => '(11) 97777-3300',
                    'zipcode' => '04538-132',
                    'street' => 'Rua Gomes de Carvalho',
                    'number' => '1629',
                    'complement' => 'Sala 4',
                    'district' => 'Vila Olímpia',
                    'city' => 'São Paulo',
                    'state' => 'SP',
                    'country' => 'BR',
                ],
            ],
            'lojista_norte' => [
                'user' => $lojistaNorte,
                'name' => 'Lojista Norte Comércio',
                'email' => 'compras.lojistanorte@graficaimpacta.local',
                'phone' => '(11) 96666-2200',
                'document' => '45.987.123/0001-55',
                'address' => [
                    'recipient_name' => 'Lojista Norte Comércio',
                    'phone' => '(11) 96666-2200',
                    'zipcode' => '02042-001',
                    'street' => 'Rua Voluntários da Pátria',
                    'number' => '3500',
                    'complement' => 'Loja 12',
                    'district' => 'Santana',
                    'city' => 'São Paulo',
                    'state' => 'SP',
                    'country' => 'BR',
                ],
            ],
            'guest_corporativo' => [
                'user' => null,
                'name' => 'Hospital Vida Nova',
                'email' => 'compras@vidanova.exemplo',
                'phone' => '(31) 98877-1000',
                'document' => '08.331.200/0001-10',
                'address' => [
                    'recipient_name' => 'Hospital Vida Nova',
                    'phone' => '(31) 98877-1000',
                    'zipcode' => '30130-110',
                    'street' => 'Avenida Afonso Pena',
                    'number' => '1450',
                    'complement' => 'Suprimentos',
                    'district' => 'Centro',
                    'city' => 'Belo Horizonte',
                    'state' => 'MG',
                    'country' => 'BR',
                ],
            ],
            'guest_evento' => [
                'user' => null,
                'name' => 'Feira TechSul',
                'email' => 'producao@techsul-eventos.exemplo',
                'phone' => '(51) 97766-5500',
                'document' => '19.552.330/0001-47',
                'address' => [
                    'recipient_name' => 'Feira TechSul',
                    'phone' => '(51) 97766-5500',
                    'zipcode' => '90010-150',
                    'street' => 'Rua dos Andradas',
                    'number' => '800',
                    'complement' => 'Pavilhão B',
                    'district' => 'Centro Histórico',
                    'city' => 'Porto Alegre',
                    'state' => 'RS',
                    'country' => 'BR',
                ],
            ],
        ];
    }

    /**
     * @param  array<string, array<string, mixed>>  $profiles
     */
    private function seedAddresses(array $profiles): void
    {
        foreach ($profiles as $key => $profile) {
            /** @var User|null $user */
            $user = $profile['user'];
            if (! $user) {
                continue;
            }

            $address = (array) $profile['address'];

            Address::query()->updateOrCreate(
                ['user_id' => $user->id, 'label' => 'Principal'],
                [
                    'recipient_name' => (string) ($address['recipient_name'] ?? $user->name),
                    'phone' => (string) ($address['phone'] ?? $user->phone),
                    'zipcode' => (string) ($address['zipcode'] ?? '01001-000'),
                    'street' => (string) ($address['street'] ?? 'Rua Exemplo'),
                    'number' => (string) ($address['number'] ?? '100'),
                    'complement' => $address['complement'] ?? null,
                    'district' => (string) ($address['district'] ?? 'Centro'),
                    'city' => (string) ($address['city'] ?? 'São Paulo'),
                    'state' => (string) ($address['state'] ?? 'SP'),
                    'country' => (string) ($address['country'] ?? 'BR'),
                    'is_default_shipping' => true,
                    'is_default_billing' => true,
                ],
            );

            if ($key === 'cliente_demo') {
                Address::query()->updateOrCreate(
                    ['user_id' => $user->id, 'label' => 'Retirada / Loja'],
                    [
                        'recipient_name' => 'Cliente Demo',
                        'phone' => '(11) 98888-0000',
                        'zipcode' => '01001-000',
                        'street' => 'Praça da Sé',
                        'number' => '100',
                        'complement' => 'Sala 2',
                        'district' => 'Sé',
                        'city' => 'São Paulo',
                        'state' => 'SP',
                        'country' => 'BR',
                        'is_default_shipping' => false,
                        'is_default_billing' => false,
                    ],
                );
            }
        }
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function shippingProfiles(): array
    {
        return [
            'pickup' => [
                'code' => 'pickup_counter',
                'label' => 'Retirada no balcão',
                'provider' => 'pickup',
                'cost' => 0.0,
                'delivery_days' => 1,
                'is_pickup' => true,
                'payload' => [
                    'type' => 'pickup',
                    'address' => 'Retirada no balcão mediante confirmação do pedido',
                ],
            ],
            'pac' => [
                'code' => 'correios_pac',
                'label' => 'Correios PAC',
                'provider' => 'correios',
                'cost' => 24.25,
                'delivery_days' => 4,
                'is_pickup' => false,
                'payload' => ['mode' => 'local_rate_table'],
            ],
            'sedex' => [
                'code' => 'correios_sedex',
                'label' => 'Correios SEDEX',
                'provider' => 'correios',
                'cost' => 36.60,
                'delivery_days' => 2,
                'is_pickup' => false,
                'payload' => ['mode' => 'local_rate_table'],
            ],
            'carrier' => [
                'code' => 'transportadora_economica',
                'label' => 'Transportadora econômica',
                'provider' => 'transportadora',
                'cost' => 29.05,
                'delivery_days' => 3,
                'is_pickup' => false,
                'payload' => ['mode' => 'local_rate_table'],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $scenario
     * @param  array<string, mixed>  $profile
     * @param  \Illuminate\Support\Collection<string, ProductVariant>  $variants
     * @param  array<string, mixed>  $shippingProfile
     */
    private function createDemoOrder(
        string $orderNumber,
        Carbon $placedAt,
        array $scenario,
        array $profile,
        $variants,
        array $shippingProfile,
        ?User $admin,
    ): void {
        $itemsPayload = [];
        $subtotal = 0.0;

        foreach ((array) $scenario['items'] as $row) {
            $variant = $variants->get((string) $row['sku']) ?: $variants->first();
            if (! $variant) {
                continue;
            }

            $quantity = max(1, (int) ($row['qty'] ?? 1));
            $unitPrice = (float) ($variant->promotional_price ?: $variant->price);
            $lineTotal = round($unitPrice * $quantity, 2);

            $itemsPayload[] = [
                'variant' => $variant,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'line_total' => $lineTotal,
                'production_status' => (string) ($row['production_status'] ?? 'pending_file'),
                'artwork' => $row['artwork'] ?? null,
            ];

            $subtotal += $lineTotal;
        }

        $shippingTotal = round((float) ($shippingProfile['cost'] ?? 0), 2);
        $total = round($subtotal + $shippingTotal, 2);
        $paymentStatus = (string) $scenario['payment_status'];
        $orderStatus = (string) $scenario['status'];
        $fulfillmentStatus = (string) $scenario['fulfillment_status'];
        $paidAt = $paymentStatus === PaymentStatus::Paid->value ? $placedAt->copy()->addMinutes(15) : null;

        $billingAddress = (array) $profile['address'];
        $shippingAddress = (array) $profile['address'];

        $order = Order::query()->create([
            'order_number' => $orderNumber,
            'user_id' => $profile['user']?->id,
            'status' => $orderStatus,
            'payment_status' => $paymentStatus,
            'fulfillment_status' => $fulfillmentStatus,
            'customer_name' => (string) $profile['name'],
            'customer_email' => (string) $profile['email'],
            'customer_phone' => (string) ($profile['phone'] ?? ''),
            'currency' => 'BRL',
            'subtotal' => round($subtotal, 2),
            'discount_total' => 0,
            'shipping_total' => $shippingTotal,
            'shipping_method_code' => (string) $shippingProfile['code'],
            'shipping_method_label' => (string) $shippingProfile['label'],
            'shipping_provider' => (string) $shippingProfile['provider'],
            'shipping_delivery_days' => (int) $shippingProfile['delivery_days'],
            'tax_total' => 0,
            'total' => $total,
            'notes' => (string) ($scenario['notes'] ?? ''),
            'billing_address' => $billingAddress,
            'shipping_address' => $shippingAddress,
            'shipping_quote_payload' => (array) ($shippingProfile['payload'] ?? []),
            'metadata' => [
                'customer_document' => (string) ($profile['document'] ?? ''),
                'installments' => (string) $scenario['payment_method'] === PaymentMethod::CreditCard->value ? 3 : 1,
                'shipping' => [
                    'is_pickup' => (bool) ($shippingProfile['is_pickup'] ?? false),
                ],
                'request' => [
                    'channel' => 'seed-demo',
                    'ip' => '127.0.0.1',
                ],
            ],
            'placed_at' => $placedAt,
            'paid_at' => $paidAt,
        ]);

        $order->forceFill([
            'created_at' => $placedAt,
            'updated_at' => $placedAt->copy()->addMinutes(2),
        ])->saveQuietly();

        $createdItems = [];

        foreach ($itemsPayload as $itemIndex => $itemPayload) {
            /** @var ProductVariant $variant */
            $variant = $itemPayload['variant'];

            $item = $order->items()->create([
                'product_id' => $variant->product_id,
                'product_variant_id' => $variant->id,
                'product_name' => $variant->product?->name ?? $variant->name,
                'variant_name' => $variant->name,
                'sku' => $variant->sku,
                'quantity' => $itemPayload['quantity'],
                'unit_price' => $itemPayload['unit_price'],
                'discount_total' => 0,
                'total' => $itemPayload['line_total'],
                'configuration' => (array) ($variant->attributes ?? []),
                'artwork_notes' => 'Arquivo em PDF/X-1a, conferir sangria e texto em curva.',
                'production_status' => $itemPayload['production_status'],
                'created_at' => $placedAt->copy()->addMinutes(3 + $itemIndex),
                'updated_at' => $placedAt->copy()->addMinutes(3 + $itemIndex),
            ]);

            $createdItems[] = $item;

            if (! is_array($itemPayload['artwork'])) {
                continue;
            }

            $artworkStatus = (string) ($itemPayload['artwork']['status'] ?? ArtworkFileStatus::Uploaded->value);
            $filePath = sprintf('artworks/demo-orders/%s/item-%d/arte-final-%d.pdf', $orderNumber, $item->id, $itemIndex + 1);

            Storage::disk('public')->put(
                $filePath,
                "Arquivo demo de arte final para {$orderNumber}\nItem {$item->product_name}\n",
            );

            $reviewedAt = in_array($artworkStatus, [
                ArtworkFileStatus::Approved->value,
                ArtworkFileStatus::NeedsAdjustment->value,
                ArtworkFileStatus::Rejected->value,
                ArtworkFileStatus::UnderReview->value,
            ], true)
                ? $placedAt->copy()->addHours(2)
                : null;

            $item->artworkFiles()->create([
                'storage_disk' => 'public',
                'path' => $filePath,
                'original_name' => 'arte-final.pdf',
                'mime_type' => 'application/pdf',
                'size_bytes' => 18432,
                'checklist' => [
                    'cmyk' => true,
                    'bleed' => true,
                    'outlined_fonts' => true,
                    'high_resolution_images' => $artworkStatus !== ArtworkFileStatus::NeedsAdjustment->value,
                ],
                'status' => $artworkStatus,
                'reviewed_at' => $reviewedAt,
                'review_notes' => match ($artworkStatus) {
                    ArtworkFileStatus::Approved->value => 'Arquivo aprovado para produção.',
                    ArtworkFileStatus::UnderReview->value => 'Arquivo em conferência pela pré-impressão.',
                    ArtworkFileStatus::NeedsAdjustment->value => 'Ajustar resolução das imagens e reenviar.',
                    default => null,
                },
                'metadata' => [
                    'seeded' => true,
                    'uploaded_at' => $placedAt->copy()->addMinutes(25)->toIso8601String(),
                    'reviewed_by_user_id' => $admin?->id,
                ],
                'uploaded_by_user_id' => $profile['user']?->id,
                'created_at' => $placedAt->copy()->addMinutes(25),
                'updated_at' => $reviewedAt ?? $placedAt->copy()->addMinutes(25),
            ]);
        }

        $paymentCreatedAt = $placedAt->copy()->addMinutes(4);
        $gatewayPayload = [
            'mode' => (string) $scenario['payment_provider'],
        ];

        if ((string) $scenario['payment_provider'] === 'mercadopago') {
            $gatewayPayload['mercadopago'] = array_filter([
                'payment_id' => 'MP-'.$orderNumber,
                'status' => $paymentStatus === PaymentStatus::Paid->value ? 'approved' : ($paymentStatus === PaymentStatus::Failed->value ? 'rejected' : 'pending'),
                'ticket_url' => (string) $scenario['payment_method'] === PaymentMethod::Boleto->value ? 'https://exemplo.local/boleto/'.$orderNumber : null,
                'checkout_url' => $paymentStatus !== PaymentStatus::Paid->value ? 'https://www.mercadopago.com.br/checkout/v1/redirect?pref_id='.$orderNumber : null,
                'last_synced_at' => $placedAt->copy()->addMinutes(16)->toIso8601String(),
            ]);
        } elseif ((string) $scenario['payment_method'] === PaymentMethod::BankTransfer->value) {
            $gatewayPayload['manual_reference'] = 'TED-'.$orderNumber;
        }

        $payment = $order->payments()->create([
            'provider' => (string) $scenario['payment_provider'],
            'method' => (string) $scenario['payment_method'],
            'status' => $paymentStatus,
            'transaction_id' => $paymentStatus === PaymentStatus::Pending->value ? null : 'TXN-'.$orderNumber,
            'amount' => $total,
            'currency' => 'BRL',
            'gateway_payload' => $gatewayPayload,
            'paid_at' => $paidAt,
            'failed_at' => $paymentStatus === PaymentStatus::Failed->value ? $placedAt->copy()->addMinutes(14) : null,
            'created_at' => $paymentCreatedAt,
            'updated_at' => $paidAt ?? $paymentCreatedAt,
        ]);

        $this->seedStatusHistory(
            $order,
            placedAt: $placedAt,
            paymentStatus: $paymentStatus,
            orderStatus: $orderStatus,
            fulfillmentStatus: $fulfillmentStatus,
            adminId: $admin?->id,
            userId: $profile['user']?->id,
            hasArtwork: collect($createdItems)->contains(fn ($item) => $item->artworkFiles()->exists()),
        );

        $order->forceFill([
            'updated_at' => max(
                $order->updated_at,
                $payment->updated_at ?? $order->updated_at,
            ),
        ])->saveQuietly();
    }

    private function seedStatusHistory(
        Order $order,
        Carbon $placedAt,
        string $paymentStatus,
        string $orderStatus,
        string $fulfillmentStatus,
        ?int $adminId,
        ?int $userId,
        bool $hasArtwork,
    ): void {
        $order->statusHistory()->create([
            'from_status' => null,
            'to_status' => OrderStatus::PendingPayment->value,
            'actor_type' => $userId ? 'user' : 'guest',
            'actor_id' => $userId,
            'message' => 'Pedido criado via checkout online.',
            'metadata' => ['seeded' => true],
            'created_at' => $placedAt,
        ]);

        if ($paymentStatus === PaymentStatus::Paid->value) {
            $order->statusHistory()->create([
                'from_status' => OrderStatus::PendingPayment->value,
                'to_status' => in_array($orderStatus, [OrderStatus::Paid->value, OrderStatus::InProduction->value, OrderStatus::Shipped->value, OrderStatus::Delivered->value], true)
                    ? OrderStatus::Paid->value
                    : $orderStatus,
                'actor_type' => 'system',
                'actor_id' => null,
                'message' => 'Pagamento confirmado e pedido liberado para pré-impressão.',
                'metadata' => [
                    'payment_status_to' => PaymentStatus::Paid->value,
                    'seeded' => true,
                ],
                'created_at' => $placedAt->copy()->addMinutes(15),
            ]);
        }

        if ($hasArtwork) {
            $order->statusHistory()->create([
                'from_status' => $order->status->value,
                'to_status' => $order->status->value,
                'actor_type' => 'admin',
                'actor_id' => $adminId,
                'message' => 'Conferência de arte registrada para itens do pedido.',
                'metadata' => [
                    'fulfillment_status' => $fulfillmentStatus,
                    'seeded' => true,
                ],
                'created_at' => $placedAt->copy()->addHours(2),
            ]);
        }

        if ($orderStatus === OrderStatus::InProduction->value) {
            $order->statusHistory()->create([
                'from_status' => OrderStatus::Paid->value,
                'to_status' => OrderStatus::InProduction->value,
                'actor_type' => 'admin',
                'actor_id' => $adminId,
                'message' => 'Produção liberada pela gráfica.',
                'metadata' => ['fulfillment_status' => $fulfillmentStatus, 'seeded' => true],
                'created_at' => $placedAt->copy()->addHours(4),
            ]);
        }

        if ($orderStatus === OrderStatus::Shipped->value) {
            $order->statusHistory()->create([
                'from_status' => OrderStatus::InProduction->value,
                'to_status' => OrderStatus::Shipped->value,
                'actor_type' => 'admin',
                'actor_id' => $adminId,
                'message' => 'Pedido expedido para entrega.',
                'metadata' => ['fulfillment_status' => $fulfillmentStatus, 'seeded' => true],
                'created_at' => $placedAt->copy()->addHours(8),
            ]);
        }

        if ($orderStatus === OrderStatus::Delivered->value) {
            $order->statusHistory()->create([
                'from_status' => OrderStatus::Shipped->value,
                'to_status' => OrderStatus::Delivered->value,
                'actor_type' => 'system',
                'actor_id' => null,
                'message' => 'Entrega confirmada.',
                'metadata' => ['fulfillment_status' => $fulfillmentStatus, 'seeded' => true],
                'created_at' => $placedAt->copy()->addHours(24),
            ]);
        }

        if ($paymentStatus === PaymentStatus::Failed->value) {
            $order->statusHistory()->create([
                'from_status' => OrderStatus::PendingPayment->value,
                'to_status' => OrderStatus::PendingPayment->value,
                'actor_type' => 'system',
                'actor_id' => null,
                'message' => 'Pagamento recusado. Pedido aguardando nova tentativa.',
                'metadata' => ['payment_status_to' => PaymentStatus::Failed->value, 'seeded' => true],
                'created_at' => $placedAt->copy()->addMinutes(14),
            ]);
        }
    }
}
