<?php

namespace Database\Seeders;

use App\Models\HomeBanner;
use Illuminate\Database\Seeder;

class HomeBannerSeeder extends Seeder
{
    public function run(): void
    {
        $baseUrl = rtrim((string) config('app.url', 'http://localhost:8080'), '/');

        $banners = [
            [
                'name' => 'Linha premium papelaria corporativa',
                'badge' => 'Uriah Criativa • Premium',
                'headline' => 'Papelaria premium com acabamento sofisticado para marcas exigentes',
                'subheadline' => 'Cartões, pastas, envelopes e kits corporativos',
                'description' => 'Escolha materiais, tiragens e acabamentos com visual elegante e finalize o pedido em minutos. Atendimento comercial e conferência técnica em cada etapa.',
                'cta_label' => 'Explorar papelaria',
                'cta_url' => '/catalogo?categoria=cartoes-e-papelaria',
                'secondary_cta_label' => 'Falar com a equipe',
                'secondary_cta_url' => '/entrar',
                'theme' => 'gold',
                'background_image_url' => $baseUrl.'/demo/banners/papelaria-premium.svg',
                'metadata' => ['text_side' => 'left'],
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Campanhas e promocionais',
                'badge' => 'Campanhas • Giro rápido',
                'headline' => 'Flyers, folders e promocionais com produção organizada para ações sazonais',
                'subheadline' => 'Compra rápida com foco em prazo, preço e conferência de arquivo',
                'description' => 'Ideal para varejo, eventos e campanhas. Fluxo de pedido online com cobrança, chat por pedido e acompanhamento de produção.',
                'cta_label' => 'Ver promocionais',
                'cta_url' => '/catalogo?categoria=promocionais',
                'secondary_cta_label' => 'Ir para catálogo',
                'secondary_cta_url' => '/catalogo',
                'theme' => 'obsidian',
                'background_image_url' => $baseUrl.'/demo/banners/promocionais-campanha.svg',
                'metadata' => ['text_side' => 'right'],
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Comunicação visual e PDV',
                'badge' => 'PDV • Comunicação visual',
                'headline' => 'Banners e comunicação visual para pontos de venda, eventos e ativações',
                'subheadline' => 'Retirada no balcão ou expedição com acompanhamento',
                'description' => 'Configure peças por aplicação, finalize o pedido e acompanhe cobrança, pré-impressão, produção e entrega em um único fluxo.',
                'cta_label' => 'Comunicação visual',
                'cta_url' => '/catalogo?categoria=comunicacao-visual',
                'secondary_cta_label' => 'Área do cliente',
                'secondary_cta_url' => '/minha-conta',
                'theme' => 'ivory',
                'background_image_url' => $baseUrl.'/demo/banners/comunicacao-visual-pdv.svg',
                'metadata' => ['text_side' => 'left'],
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($banners as $banner) {
            HomeBanner::query()->updateOrCreate(
                ['name' => $banner['name']],
                $banner,
            );
        }
    }
}
