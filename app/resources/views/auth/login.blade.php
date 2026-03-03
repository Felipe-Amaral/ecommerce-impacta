@extends('layouts.store')

@section('title', 'Entrar | Gráfica Uriah Criativa')
@section('meta_description', 'Acesse sua conta para acompanhar pedidos e histórico da gráfica.')

@section('content')
    <section class="auth-shell">
        <article class="card auth-card auth-hero stack-xl">
            <div class="stack">
                <p class="eyebrow">Acesso do cliente</p>
                <h1 style="font-size: clamp(1.9rem, 3vw, 2.8rem);">Entre para acompanhar seus pedidos</h1>
                <p class="lead">
                    Visualize histórico, status de produção e próximos passos da sua compra.
                    Acompanhe cobrança, pré-impressão, produção e entrega em um painel claro.
                </p>
            </div>

            <div class="hero-proof">
                <span class="pill">Histórico de pedidos</span>
                <span class="pill">Status de produção</span>
                <span class="pill">Base para upload de arte</span>
            </div>

            <div class="metric-grid">
                <div class="metric-card">
                    <strong>24h</strong>
                    <span>Suporte comercial</span>
                </div>
                <div class="metric-card">
                    <strong>PIX</strong>
                    <span>Cobrança rápida</span>
                </div>
                <div class="metric-card">
                    <strong>QA</strong>
                    <span>Conferência de arte</span>
                </div>
            </div>

            <div class="card card-pad" style="overflow:hidden; box-shadow:none; background:
                radial-gradient(circle at 12% 16%, rgba(195,58,29,.10), transparent 44%),
                radial-gradient(circle at 88% 12%, rgba(15,93,245,.10), transparent 44%),
                linear-gradient(145deg, rgba(255,255,255,.84), rgba(247,240,231,.94));">
                <div class="stack" style="gap:10px;">
                        <div class="link-row">
                            <div class="stack" style="gap:4px;">
                            <span class="section-kicker">Portal</span>
                            <h3 style="margin:0;">Área do Cliente</h3>
                            </div>
                        <span class="badge">Acompanhamento</span>
                    </div>
                    <div class="print-scene-wrap" style="padding: 4px 2px 2px;">
                        @include('store.partials.print-mockup', ['categorySlug' => 'cartoes-e-papelaria', 'size' => 'lg', 'title' => 'Portal Cliente'])
                    </div>
                    <div class="checkout-progress">
                        <span class="step-chip"><span class="n">1</span> Pedido</span>
                        <span class="step-chip"><span class="n">2</span> Pagamento</span>
                        <span class="step-chip"><span class="n">3</span> Produção</span>
                    </div>
                </div>
            </div>

            <div class="card card-pad stack">
                <h3>O que você acompanha aqui</h3>
                <ul class="check-list">
                    <li>Catálogo com variantes por tiragem e acabamento</li>
                    <li>Carrinho e checkout com criação de pedido</li>
                    <li>Status de pedido, pagamento e produção</li>
                    <li>Histórico de pedidos e dados de entrega</li>
                </ul>
            </div>
        </article>

        <aside class="auth-side">
            <section class="card auth-card stack-lg">
                <div class="stack">
                    <p class="eyebrow">Entrar</p>
                    <h2 style="font-size:1.55rem;">Acesse sua conta</h2>
                    <p class="small muted">Use seu e-mail para acompanhar pedidos e status da produção.</p>
                </div>

                @include('auth.partials.social-buttons')

                <form method="POST" action="{{ route('login.store') }}" class="stack">
                    @csrf

                    <div class="field">
                        <label for="email">E-mail</label>
                        <input id="email" name="email" type="email" class="input" value="{{ old('email') }}" required autofocus autocomplete="email" />
                    </div>

                    <div class="field">
                        <label for="password">Senha</label>
                        <input id="password" name="password" type="password" class="input" required autocomplete="current-password" />
                    </div>

                    <label class="radio-card" for="remember">
                        <input id="remember" type="checkbox" name="remember" value="1" @checked(old('remember')) />
                        <span>Lembrar meu acesso neste navegador</span>
                    </label>

                    <button type="submit" class="btn btn-primary">Entrar na conta</button>
                    <a href="{{ route('register') }}" class="btn btn-secondary">Criar conta</a>
                    <a href="{{ route('home') }}" class="btn btn-secondary">Voltar para a loja</a>
                </form>
            </section>

            <section class="card auth-card stack">
                <h3>Acesso de demonstração</h3>
                <div class="stack small">
                    <div class="glass-panel stack" style="gap:8px;">
                        <div><strong>Admin:</strong> <span class="mono">admin@graficaimpacta.local</span></div>
                        <div><strong>Senha:</strong> <span class="mono">password</span></div>
                        <button type="button" class="btn btn-secondary btn-sm" data-demo-login="admin">Preencher admin demo</button>
                    </div>
                    <div class="glass-panel stack" style="gap:8px;">
                        <div><strong>Cliente:</strong> <span class="mono">cliente@graficaimpacta.local</span></div>
                        <div><strong>Senha:</strong> <span class="mono">password</span></div>
                        <button type="button" class="btn btn-secondary btn-sm" data-demo-login="cliente">Preencher cliente demo</button>
                    </div>
                </div>
                <p class="tiny muted">Esses acessos estão disponíveis para testes locais da loja e do painel de pedidos.</p>
            </section>
        </aside>
    </section>
@endsection

@push('scripts')
<script>
    (function () {
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        if (!emailInput || !passwordInput) return;

        const users = {
            admin: 'admin@graficaimpacta.local',
            cliente: 'cliente@graficaimpacta.local'
        };

        document.querySelectorAll('[data-demo-login]').forEach((button) => {
            button.addEventListener('click', () => {
                const type = button.getAttribute('data-demo-login');
                if (!type || !users[type]) return;
                emailInput.value = users[type];
                passwordInput.value = 'password';
                emailInput.focus();
            });
        });
    })();
</script>
@endpush
