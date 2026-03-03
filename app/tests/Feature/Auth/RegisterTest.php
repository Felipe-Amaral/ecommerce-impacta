<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_screen_is_rendered(): void
    {
        $response = $this->get('/cadastro');

        $response->assertOk();
        $response->assertSee('Cadastro rápido');
    }

    public function test_customer_can_register_with_email_and_password(): void
    {
        $this->withoutMiddleware(ValidateCsrfToken::class);

        $response = $this->post('/cadastro', [
            'name' => 'Cliente Novo',
            'email' => 'novo-cliente@example.com',
            'phone' => '(11) 97777-0000',
            'password' => 'secret-123',
            'password_confirmation' => 'secret-123',
        ]);

        $response->assertRedirect(route('account.dashboard'));

        $user = User::query()->where('email', 'novo-cliente@example.com')->first();
        $this->assertNotNull($user);
        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->email_verified_at);
    }
}
