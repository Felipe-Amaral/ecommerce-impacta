<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_is_rendered(): void
    {
        $response = $this->get('/entrar');

        $response->assertOk();
        $response->assertSee('Acesse sua conta');
    }

    public function test_user_can_authenticate_using_valid_credentials(): void
    {
        $this->withoutMiddleware(ValidateCsrfToken::class);

        $user = User::factory()->create([
            'email' => 'cliente@example.com',
            'password' => Hash::make('secret-123'),
        ]);

        $response = $this->post('/entrar', [
            'email' => $user->email,
            'password' => 'secret-123',
        ]);

        $response->assertRedirect(route('account.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_authenticate_with_invalid_password(): void
    {
        $this->withoutMiddleware(ValidateCsrfToken::class);

        $user = User::factory()->create([
            'email' => 'cliente2@example.com',
            'password' => Hash::make('secret-123'),
        ]);

        $response = $this->from('/entrar')->post('/entrar', [
            'email' => $user->email,
            'password' => 'invalid-password',
        ]);

        $response->assertRedirect('/entrar');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
