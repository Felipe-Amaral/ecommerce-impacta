<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CheckoutShippingQuoteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\Account\OrderController as AccountOrderController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\HomeBannerController as AdminHomeBannerController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ArtworkFileReviewController;
use App\Http\Controllers\Admin\CatalogController as AdminCatalogController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ProductVariantController as AdminProductVariantController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\ArtworkFileDownloadController;
use App\Http\Controllers\OrderMessageController;
use App\Http\Controllers\Webhooks\MercadoPagoWebhookController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/entrar', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/entrar', [AuthenticatedSessionController::class, 'store'])->name('login.store');
    Route::get('/cadastro', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/cadastro', [RegisteredUserController::class, 'store'])->name('register.store');
    Route::get('/auth/social/{provider}', [SocialAuthController::class, 'redirect'])->name('auth.social.redirect');
    Route::get('/auth/social/{provider}/callback', [SocialAuthController::class, 'callback'])->name('auth.social.callback');

    Route::redirect('/login', '/entrar');
    Route::redirect('/register', '/cadastro');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/sair', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/minha-conta', AccountController::class)->name('account.dashboard');
    Route::get('/minha-conta/pedidos', [AccountOrderController::class, 'index'])->name('account.orders.index');
    Route::get('/minha-conta/pedidos/{order}', [AccountOrderController::class, 'show'])->name('account.orders.show');
    Route::post('/minha-conta/pedidos/{order}/itens/{item}/arte', [AccountOrderController::class, 'uploadArtwork'])->name('account.orders.items.artwork.store');

    Route::get('/painel', AdminDashboardController::class)->name('admin.dashboard');
    Route::get('/painel/pedidos', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/painel/pedidos/{order}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
    Route::patch('/painel/pedidos/{order}/workflow', [AdminOrderController::class, 'updateWorkflow'])->name('admin.orders.workflow.update');
    Route::patch('/painel/arquivos/{artworkFile}/revisao', ArtworkFileReviewController::class)->name('admin.artwork.review');

    Route::get('/painel/cadastros', AdminCatalogController::class)->name('admin.catalog.index');
    Route::get('/painel/cadastros/categorias', [AdminCategoryController::class, 'index'])->name('admin.catalog.categories.index');
    Route::post('/painel/cadastros/categorias', [AdminCategoryController::class, 'store'])->name('admin.catalog.categories.store');
    Route::get('/painel/cadastros/categorias/{category}/editar', [AdminCategoryController::class, 'edit'])->name('admin.catalog.categories.edit');
    Route::put('/painel/cadastros/categorias/{category}', [AdminCategoryController::class, 'update'])->name('admin.catalog.categories.update');

    Route::get('/painel/cadastros/produtos', [AdminProductController::class, 'index'])->name('admin.catalog.products.index');
    Route::get('/painel/cadastros/produtos/novo', [AdminProductController::class, 'create'])->name('admin.catalog.products.create');
    Route::post('/painel/cadastros/produtos', [AdminProductController::class, 'store'])->name('admin.catalog.products.store');
    Route::get('/painel/cadastros/produtos/{product}/editar', [AdminProductController::class, 'edit'])->name('admin.catalog.products.edit');
    Route::put('/painel/cadastros/produtos/{product}', [AdminProductController::class, 'update'])->name('admin.catalog.products.update');
    Route::post('/painel/cadastros/produtos/{product}/variacoes', [AdminProductVariantController::class, 'store'])->name('admin.catalog.products.variants.store');
    Route::put('/painel/cadastros/produtos/{product}/variacoes/{variant}', [AdminProductVariantController::class, 'update'])->name('admin.catalog.products.variants.update');
    Route::delete('/painel/cadastros/produtos/{product}/variacoes/{variant}', [AdminProductVariantController::class, 'destroy'])->name('admin.catalog.products.variants.destroy');
    Route::get('/painel/cadastros/banners', [AdminHomeBannerController::class, 'index'])->name('admin.catalog.banners.index');
    Route::post('/painel/cadastros/banners', [AdminHomeBannerController::class, 'store'])->name('admin.catalog.banners.store');
    Route::get('/painel/cadastros/banners/{banner}/editar', [AdminHomeBannerController::class, 'edit'])->name('admin.catalog.banners.edit');
    Route::put('/painel/cadastros/banners/{banner}', [AdminHomeBannerController::class, 'update'])->name('admin.catalog.banners.update');
    Route::patch('/painel/cadastros/banners/{banner}/status', [AdminHomeBannerController::class, 'toggleActive'])->name('admin.catalog.banners.status');
    Route::delete('/painel/cadastros/banners/{banner}', [AdminHomeBannerController::class, 'destroy'])->name('admin.catalog.banners.destroy');

    Route::get('/arquivos/arte/{artworkFile}', ArtworkFileDownloadController::class)->name('artwork-files.download');
    Route::get('/pedidos/{order}/chat/mensagens', [OrderMessageController::class, 'index'])->name('orders.chat.messages.index');
    Route::post('/pedidos/{order}/chat/mensagens', [OrderMessageController::class, 'store'])->name('orders.chat.messages.store');
});

Route::get('/', HomeController::class)->name('home');
Route::view('/quem-somos', 'store.pages.blank', ['pageTitle' => 'Quem Somos'])->name('pages.about');
Route::view('/servicos', 'store.pages.blank', ['pageTitle' => 'Serviços'])->name('pages.services');
Route::view('/portfolio', 'store.pages.blank', ['pageTitle' => 'Portfólio'])->name('pages.portfolio');
Route::view('/blog', 'store.pages.blank', ['pageTitle' => 'Blog'])->name('pages.blog');
Route::view('/orcamento', 'store.pages.blank', ['pageTitle' => 'Orçamento'])->name('pages.quote');
Route::view('/contato', 'store.pages.blank', ['pageTitle' => 'Contato'])->name('pages.contact');

Route::get('/catalogo', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalogo/{slug}', [CatalogController::class, 'show'])->name('catalog.show');

Route::get('/carrinho', [CartController::class, 'index'])->name('cart.index');
Route::post('/carrinho/itens', [CartController::class, 'store'])->name('cart.items.store');
Route::patch('/carrinho/itens/{lineId}', [CartController::class, 'update'])->name('cart.items.update');
Route::delete('/carrinho/itens/{lineId}', [CartController::class, 'destroy'])->name('cart.items.destroy');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/frete/cotacoes', CheckoutShippingQuoteController::class)->name('checkout.shipping.quotes');
Route::get('/pedido/{order}/sucesso', [CheckoutController::class, 'success'])
    ->middleware('signed')
    ->name('checkout.success');

Route::post('/webhooks/mercado-pago', MercadoPagoWebhookController::class)
    ->withoutMiddleware([ValidateCsrfToken::class])
    ->name('webhooks.mercadopago');
