@extends('layouts.store')

@section('title', 'Criar Conta | Gráfica Uriah Criativa')
@section('meta_description', 'Crie sua conta para fazer pedidos, enviar arte e acompanhar produção e entrega.')

@section('content')
    <section class="auth-shell">
        <article class="card auth-card auth-hero stack-xl">
            <div class="stack">
                <p class="eyebrow">Cadastro do cliente</p>
                <h1 style="font-size: clamp(1.9rem, 3vw, 2.8rem);">Crie sua conta e centralize pedidos, arte e atendimento</h1>
                <p class="lead">
                    Cadastro rápido para comprar, acompanhar produção, conversar com a gráfica no chat do pedido
                    e manter histórico de cobrança e entrega.
                </p>
            </div>

            <div class="hero-proof">
                <span class="pill">Cadastro em 1 clique (social)</span>
                <span class="pill">Chat por pedido</span>
                <span class="pill">Histórico completo</span>
                <span class="pill">Upload de arte por item</span>
            </div>

            <div class="card card-pad stack" style="box-shadow:none;">
                <h3>Depois do cadastro você pode</h3>
                <ul class="check-list">
                    <li>Comprar pelo catálogo com suas tiragens e acabamentos</li>
                    <li>Acompanhar cobrança, pré-impressão, produção e entrega</li>
                    <li>Enviar arte final por item e receber retorno da gráfica</li>
                    <li>Conversar com a equipe no chat do pedido</li>
                </ul>
            </div>
        </article>

        <aside class="auth-side">
            <section class="card auth-card stack-lg">
                <div class="stack">
                    <p class="eyebrow">Criar conta</p>
                    <h2 style="font-size:1.55rem;">Cadastro rápido</h2>
                    <p class="small muted">Use rede social ou preencha seus dados para começar.</p>
                </div>

                @include('auth.partials.social-buttons')

                <form method="POST" action="{{ route('register.store') }}" class="stack">
                    @csrf

                    <div class="field">
                        <label for="register_name">Nome / Razão social</label>
                        <input id="register_name" name="name" type="text" class="input" value="{{ old('name') }}" required autocomplete="name" />
                    </div>

                    <div class="field">
                        <label for="register_email">E-mail</label>
                        <input id="register_email" name="email" type="email" class="input" value="{{ old('email') }}" required autocomplete="email" />
                    </div>

                    <div class="field">
                        <label for="register_phone">Telefone / WhatsApp (opcional)</label>
                        <input id="register_phone" name="phone" type="text" class="input" value="{{ old('phone') }}" autocomplete="tel" />
                    </div>

                    <div class="field">
                        <label for="register_password">Senha</label>
                        <input id="register_password" name="password" type="password" class="input" required autocomplete="new-password" />
                    </div>

                    <div class="field">
                        <label for="register_password_confirmation">Confirmar senha</label>
                        <input id="register_password_confirmation" name="password_confirmation" type="password" class="input" required autocomplete="new-password" />
                    </div>

                    <button type="submit" class="btn btn-primary">Criar conta</button>
                    <a href="{{ route('login') }}" class="btn btn-secondary">Já tenho conta</a>
                </form>
            </section>
        </aside>
    </section>
@endsection
