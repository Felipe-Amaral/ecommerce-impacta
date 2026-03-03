<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_and_admin_can_exchange_messages_in_order_chat(): void
    {
        $this->withoutMiddleware(ValidateCsrfToken::class);
        $this->seed(DatabaseSeeder::class);

        $customer = User::query()->where('email', 'cliente@graficaimpacta.local')->firstOrFail();
        $admin = User::query()->where('email', 'admin@graficaimpacta.local')->firstOrFail();
        $order = Order::query()
            ->where('user_id', $customer->id)
            ->latest('id')
            ->firstOrFail();

        $this->actingAs($customer);

        $this->getJson(route('orders.chat.messages.index', $order))
            ->assertOk()
            ->assertJsonStructure([
                'messages',
                'last_id',
            ]);

        $this->postJson(route('orders.chat.messages.store', $order), [
            'body' => 'Olá, preciso confirmar o acabamento fosco e o prazo.',
        ])->assertCreated()
            ->assertJsonPath('message.sender_role', 'client');

        $this->assertDatabaseHas('order_messages', [
            'order_id' => $order->id,
            'user_id' => $customer->id,
            'sender_role' => 'client',
        ]);

        $this->actingAs($admin);

        $this->postJson(route('orders.chat.messages.store', $order), [
            'body' => 'Perfeito, vamos confirmar no arquivo e retornar a aprovação.',
        ])->assertCreated()
            ->assertJsonPath('message.sender_role', 'admin');

        $this->getJson(route('orders.chat.messages.index', $order))
            ->assertOk()
            ->assertJsonFragment([
                'body' => 'Olá, preciso confirmar o acabamento fosco e o prazo.',
            ])
            ->assertJsonFragment([
                'body' => 'Perfeito, vamos confirmar no arquivo e retornar a aprovação.',
            ]);
    }

    public function test_other_customer_cannot_access_chat_from_order_that_is_not_theirs(): void
    {
        $this->seed(DatabaseSeeder::class);

        $customer = User::query()->where('email', 'cliente@graficaimpacta.local')->firstOrFail();
        $other = User::factory()->create();
        $order = Order::query()
            ->where('user_id', $customer->id)
            ->latest('id')
            ->firstOrFail();

        $this->actingAs($other)
            ->getJson(route('orders.chat.messages.index', $order))
            ->assertForbidden();
    }
}
