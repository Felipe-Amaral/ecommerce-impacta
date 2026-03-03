<?php

namespace Tests\Feature;

use App\Enums\ArtworkFileStatus;
use App\Enums\FulfillmentStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PrintShopOperationalFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_checkout_upload_artwork_and_admin_can_review_and_advance_workflow(): void
    {
        $this->withoutMiddleware(ValidateCsrfToken::class);
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        $customer = User::query()->where('email', 'cliente@graficaimpacta.local')->firstOrFail();
        $admin = User::query()->where('email', 'admin@graficaimpacta.local')->firstOrFail();
        $variant = ProductVariant::query()->active()->firstOrFail();

        $this->actingAs($customer);

        $this->post(route('cart.items.store'), [
            'variant_id' => $variant->id,
            'quantity' => 1,
        ])->assertRedirect(route('cart.index'));

        $shippingQuoteResponse = $this->getJson(route('checkout.shipping.quotes', [
            'zipcode' => '01310-100',
        ]));

        $shippingQuoteResponse
            ->assertOk()
            ->assertJsonStructure([
                'quotes',
                'selected' => ['code', 'label', 'provider', 'cost', 'delivery_days', 'is_pickup'],
                'cart' => ['subtotal', 'discount_total', 'shipping_total', 'total'],
            ]);

        $selectedShipping = $shippingQuoteResponse->json('selected');

        $checkoutPayload = [
            'customer' => [
                'name' => 'Cliente Demo',
                'email' => 'cliente@graficaimpacta.local',
                'phone' => '(11) 98888-0000',
                'document' => '123.456.789-00',
            ],
            'notes' => 'Pedido de teste com fluxo de arte e aprovação.',
            'same_as_billing' => false,
            'billing' => [
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
            'shipping' => [
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
            'payment' => [
                'method' => 'pix',
                'installments' => 1,
            ],
            'shipping_option' => [
                'code' => (string) ($selectedShipping['code'] ?? 'pickup_counter'),
                'label' => (string) ($selectedShipping['label'] ?? 'Retirada no balcão'),
                'provider' => (string) ($selectedShipping['provider'] ?? 'pickup'),
                'cost' => (float) ($selectedShipping['cost'] ?? 0),
                'delivery_days' => $selectedShipping['delivery_days'] ?? 1,
                'is_pickup' => (bool) ($selectedShipping['is_pickup'] ?? true),
            ],
        ];

        $checkoutResponse = $this->post(route('checkout.store'), $checkoutPayload);
        $checkoutResponse->assertRedirect();

        $order = Order::query()->latest('id')->with(['items', 'payments'])->firstOrFail();
        $item = $order->items->firstOrFail();
        $payment = $order->payments->firstOrFail();

        $this->assertSame(OrderStatus::PendingPayment, $order->status);
        $this->assertSame(PaymentStatus::Pending, $order->payment_status);
        $this->assertSame(FulfillmentStatus::Pending, $order->fulfillment_status);
        $this->assertSame((string) $checkoutPayload['shipping_option']['code'], $order->shipping_method_code);
        $this->assertSame('pending_file', $item->production_status);
        $this->assertSame(PaymentStatus::Pending, $payment->status);

        $this->get(route('account.orders.show', $order))
            ->assertOk()
            ->assertSee($order->order_number);

        $uploadResponse = $this->post(route('account.orders.items.artwork.store', [$order, $item]), [
            'file' => UploadedFile::fake()->create('arte-final.pdf', 200, 'application/pdf'),
            'customer_notes' => 'Arquivo fechado em PDF/X-1a.',
            'checklist' => [
                'cmyk' => 1,
                'bleed' => 1,
                'outlined_fonts' => 1,
                'high_resolution_images' => 1,
            ],
        ]);

        $uploadResponse->assertRedirect();

        $item->refresh();
        $artworkFile = $item->artworkFiles()->latest('id')->firstOrFail();

        $this->assertSame('file_sent', $item->production_status);
        $this->assertSame(ArtworkFileStatus::Uploaded, $artworkFile->status);
        Storage::disk('public')->assertExists($artworkFile->path);

        $this->actingAs($admin);

        $this->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee($order->order_number);

        $this->patch(route('admin.artwork.review', $artworkFile), [
            'status' => ArtworkFileStatus::Approved->value,
            'review_notes' => 'Arquivo aprovado para produção.',
        ])->assertRedirect();

        $artworkFile->refresh();
        $item->refresh();
        $order->refresh();

        $this->assertSame(ArtworkFileStatus::Approved, $artworkFile->status);
        $this->assertSame('file_approved', $item->production_status);
        $this->assertSame(FulfillmentStatus::Approved, $order->fulfillment_status);

        $this->patch(route('admin.orders.workflow.update', $order), [
            'status' => OrderStatus::InProduction->value,
            'payment_status' => PaymentStatus::Paid->value,
            'fulfillment_status' => FulfillmentStatus::Printing->value,
            'message' => 'Cobrança confirmada e produção liberada.',
        ])->assertRedirect();

        $order->refresh();
        $payment->refresh();

        $this->assertSame(OrderStatus::InProduction, $order->status);
        $this->assertSame(PaymentStatus::Paid, $order->payment_status);
        $this->assertSame(FulfillmentStatus::Printing, $order->fulfillment_status);
        $this->assertSame(PaymentStatus::Paid, $payment->status);
        $this->assertNotNull($order->paid_at);
        $this->assertNotNull($payment->paid_at);
    }
}
