<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends Controller
{
    /**
     * @var list<string>
     */
    private array $supportedProviders = ['google', 'facebook', 'github'];

    public function redirect(string $provider): RedirectResponse
    {
        $provider = $this->normalizeProvider($provider);
        $this->validateProviderAvailability($provider);

        $driver = Socialite::driver($provider);
        if ($this->shouldUseStateless()) {
            $driver = $driver->stateless();
        }

        return $driver->redirect();
    }

    public function callback(Request $request, string $provider): RedirectResponse
    {
        $provider = $this->normalizeProvider($provider);
        $this->validateProviderAvailability($provider);

        try {
            $driver = Socialite::driver($provider);
            if ($this->shouldUseStateless()) {
                $driver = $driver->stateless();
            }

            $socialUser = $driver->user();
        } catch (Throwable) {
            return redirect()
                ->route('login')
                ->with('error', 'Não foi possível concluir o login com a rede social agora. Tente novamente.');
        }

        $providerUserId = (string) $socialUser->getId();
        if ($providerUserId === '') {
            return redirect()
                ->route('login')
                ->with('error', 'A rede social não retornou um identificador válido.');
        }

        $account = SocialAccount::query()
            ->with('user')
            ->where('provider', $provider)
            ->where('provider_user_id', $providerUserId)
            ->first();

        if ($account?->user) {
            $this->syncSocialAccount($account, $socialUser);

            Auth::login($account->user, remember: true);
            $request->session()->regenerate();

            return redirect()->intended(route('account.dashboard'));
        }

        $email = trim((string) $socialUser->getEmail());
        $name = trim((string) ($socialUser->getName() ?: $socialUser->getNickname() ?: 'Cliente'));

        $user = null;
        if ($email !== '') {
            $user = User::query()->where('email', $email)->first();
        }

        if (! $user) {
            $user = User::query()->create([
                'name' => $name !== '' ? $name : 'Cliente',
                'email' => $email !== '' ? $email : 'social+'.Str::uuid().'@no-email.local',
                'password' => Hash::make(Str::random(32)),
                'email_verified_at' => $email !== '' ? now() : null,
            ]);
        } else {
            if (! $user->email_verified_at && $email !== '') {
                $user->email_verified_at = now();
                $user->save();
            }
        }

        $account = $user->socialAccounts()->create([
            'provider' => $provider,
            'provider_user_id' => $providerUserId,
            'provider_email' => $email !== '' ? $email : null,
            'provider_name' => $name !== '' ? $name : null,
            'avatar_url' => $socialUser->getAvatar(),
            'access_token' => $socialUser->token ?? null,
            'refresh_token' => $socialUser->refreshToken ?? null,
            'token_expires_at' => isset($socialUser->expiresIn) && $socialUser->expiresIn
                ? now()->addSeconds((int) $socialUser->expiresIn)
                : null,
            'provider_data' => array_filter([
                'nickname' => $socialUser->getNickname(),
            ]),
        ]);

        $this->syncSocialAccount($account, $socialUser);

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return redirect()
            ->intended(route('account.dashboard'))
            ->with('success', 'Conta conectada com sucesso usando '.strtoupper($provider).'.');
    }

    private function normalizeProvider(string $provider): string
    {
        return strtolower(trim($provider));
    }

    private function validateProviderAvailability(string $provider): void
    {
        abort_unless(in_array($provider, $this->supportedProviders, true), 404);
        abort_unless(in_array($provider, $this->enabledProviders(), true), 404);

        $serviceConfig = (array) config("services.{$provider}", []);
        abort_unless(
            ! empty($serviceConfig['client_id']) && ! empty($serviceConfig['client_secret']) && ! empty($serviceConfig['redirect']),
            503,
        );
    }

    /**
     * @return list<string>
     */
    private function enabledProviders(): array
    {
        return array_values(array_filter(
            (array) config('storefront.auth.social.providers', []),
            fn ($value) => is_string($value) && in_array($value, $this->supportedProviders, true)
        ));
    }

    private function shouldUseStateless(): bool
    {
        return (bool) config('storefront.auth.social.stateless', false);
    }

    private function syncSocialAccount(SocialAccount $account, $socialUser): void
    {
        $account->forceFill([
            'provider_email' => $socialUser->getEmail() ?: $account->provider_email,
            'provider_name' => $socialUser->getName() ?: $socialUser->getNickname() ?: $account->provider_name,
            'avatar_url' => $socialUser->getAvatar() ?: $account->avatar_url,
            'access_token' => $socialUser->token ?? $account->access_token,
            'refresh_token' => $socialUser->refreshToken ?? $account->refresh_token,
            'token_expires_at' => isset($socialUser->expiresIn) && $socialUser->expiresIn
                ? now()->addSeconds((int) $socialUser->expiresIn)
                : $account->token_expires_at,
            'provider_data' => array_filter(array_merge(
                (array) $account->provider_data,
                [
                    'nickname' => $socialUser->getNickname(),
                    'updated_via_login' => now()->toIso8601String(),
                ]
            ), fn ($value) => $value !== null && $value !== ''),
        ])->save();
    }
}
