<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categoryBlueprints = $this->categoryBlueprints();
        $catalogMenu = $this->catalogMenu();
        $galleryPool = $this->galleryPool();

        $categoryIds = [];
        foreach ($categoryBlueprints as $slug => $blueprint) {
            $category = Category::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $blueprint['name'],
                    'description' => $blueprint['description'],
                    'sort_order' => $blueprint['sort_order'],
                    'meta_title' => $blueprint['name'].' | Catalogo Uriah Criativa',
                    'meta_description' => $blueprint['description'],
                    'is_active' => true,
                ],
            );

            $categoryIds[$slug] = $category->id;
        }

        Category::query()
            ->whereNotIn('slug', array_keys($categoryBlueprints))
            ->update(['is_active' => false]);

        $activeProductSlugs = [];
        $globalProductIndex = 0;

        foreach ($catalogMenu as $categorySlug => $productNames) {
            $categoryId = $categoryIds[$categorySlug] ?? null;
            if (! $categoryId) {
                continue;
            }

            foreach ($productNames as $index => $productName) {
                $globalProductIndex++;

                $baseSlug = Str::slug($productName);
                $slug = $baseSlug;
                if (in_array($slug, $activeProductSlugs, true)) {
                    $slug = $baseSlug.'-'.($index + 1);
                }

                $activeProductSlugs[] = $slug;

                $categoryBlueprint = $categoryBlueprints[$categorySlug];
                $productSku = sprintf('%s-%03d', $categoryBlueprint['sku_prefix'], $index + 1);
                $leadTimeDays = (int) $categoryBlueprint['lead_time_days'] + ($index % 2);
                $basePrice = $this->basePriceFor($categorySlug, $index);
                $specifications = $this->specificationsFor($categorySlug, $productName);
                $shortDescription = $this->shortDescriptionFor($categorySlug, $productName);
                $description = $this->longDescriptionFor($productName, $shortDescription);

                $product = Product::query()->updateOrCreate(
                    ['slug' => $slug],
                    [
                        'category_id' => $categoryId,
                        'name' => $productName,
                        'sku' => $productSku,
                        'short_description' => $shortDescription,
                        'description' => $description,
                        'product_type' => 'print',
                        'is_customizable' => true,
                        'is_active' => true,
                        'is_featured' => $index < 2,
                        'lead_time_days' => $leadTimeDays,
                        'min_quantity' => $this->minQuantityFor($categorySlug),
                        'base_price' => $basePrice,
                        'seo_title' => $productName.' | Uriah Criativa',
                        'seo_description' => Str::limit($shortDescription, 250, ''),
                        'specifications' => $specifications,
                    ],
                );

                $variantIds = [];
                foreach ($this->variantsFor($categorySlug, $specifications, $productSku, $basePrice, $leadTimeDays) as $variantIndex => $variantData) {
                    $variant = ProductVariant::query()->updateOrCreate(
                        ['sku' => $variantData['sku']],
                        [
                            'product_id' => $product->id,
                            'name' => $variantData['name'],
                            'attributes' => $variantData['attributes'],
                            'price' => $variantData['price'],
                            'promotional_price' => $variantData['promotional_price'],
                            'production_days' => $variantData['production_days'],
                            'weight_grams' => $this->weightFor($categorySlug),
                            'stock_qty' => null,
                            'is_active' => true,
                            'sort_order' => $variantIndex + 1,
                        ],
                    );

                    $variantIds[] = $variant->id;
                }

                if ($variantIds !== []) {
                    $product->variants()->whereNotIn('id', $variantIds)->delete();
                }

                for ($imageSort = 1; $imageSort <= 3; $imageSort++) {
                    $path = $galleryPool[($globalProductIndex + $imageSort - 1) % count($galleryPool)];

                    ProductImage::query()->updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'sort_order' => $imageSort,
                        ],
                        [
                            'product_variant_id' => null,
                            'path' => $path,
                            'alt_text' => $productName.' - foto '.($imageSort),
                            'is_primary' => $imageSort === 1,
                        ],
                    );
                }

                $product->images()->whereNotIn('sort_order', [1, 2, 3])->delete();
            }
        }

        if ($activeProductSlugs !== []) {
            Product::query()
                ->whereNotIn('slug', $activeProductSlugs)
                ->update([
                    'is_active' => false,
                    'is_featured' => false,
                ]);
        }
    }

    /**
     * @return array<string, array{name: string, description: string, sort_order: int, sku_prefix: string, lead_time_days: int}>
     */
    private function categoryBlueprints(): array
    {
        return [
            'cartoes-e-papelaria' => [
                'name' => 'Papelaria e Impressos',
                'description' => 'Linha completa de papelaria comercial, impressos institucionais e materiais de distribuicao.',
                'sort_order' => 10,
                'sku_prefix' => 'PAP',
                'lead_time_days' => 3,
            ],
            'rotulos-e-etiquetas' => [
                'name' => 'Adesivos e Rotulos',
                'description' => 'Adesivos, rotulos e etiquetas para marca, festas, alimentos e comunicacao promocional.',
                'sort_order' => 20,
                'sku_prefix' => 'ADR',
                'lead_time_days' => 4,
            ],
            'comunicacao-visual' => [
                'name' => 'Comunicacao Visual',
                'description' => 'Materiais de impacto para ponto de venda, eventos, sinalizacao e ambientacao.',
                'sort_order' => 30,
                'sku_prefix' => 'COM',
                'lead_time_days' => 4,
            ],
            'promocionais' => [
                'name' => 'Eventos',
                'description' => 'Itens para eventos, credenciamento, identificacao e experiencia de publico.',
                'sort_order' => 40,
                'sku_prefix' => 'EVT',
                'lead_time_days' => 4,
            ],
            'brindes-personalizados' => [
                'name' => 'Brindes Personalizados',
                'description' => 'Brindes com identidade da marca para relacionamento, ativacoes e campanhas.',
                'sort_order' => 50,
                'sku_prefix' => 'BRI',
                'lead_time_days' => 5,
            ],
            'produtos-corporativos' => [
                'name' => 'Produtos Corporativos',
                'description' => 'Materiais corporativos para seguranca, controle, credenciamento e operacao.',
                'sort_order' => 60,
                'sku_prefix' => 'COR',
                'lead_time_days' => 5,
            ],
            'outros-produtos' => [
                'name' => 'Outros Produtos',
                'description' => 'Solucoes complementares para varejo, embalagem, eventos e aplicacoes especiais.',
                'sort_order' => 70,
                'sku_prefix' => 'OUT',
                'lead_time_days' => 6,
            ],
        ];
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function catalogMenu(): array
    {
        return [
            'cartoes-e-papelaria' => [
                'Cartoes de Visita',
                'Cartoes Duplos',
                'Mini Cartoes',
                'Filipetas',
                'Panfletos',
                'Mini Panfletos',
                'Marcadores de Pagina',
                'Papel Timbrado',
                'Papel Folha Avulsa',
                'Encartes',
                'Jornal',
                'Pastas Personalizadas',
                'Receituarios',
                'Cadernos',
                'Apostilas',
                'Agendas',
                'Postais',
                'Cartazes',
                'Posters',
                'Folders',
                'Mala Direta',
                'PVC (placas rigidas)',
            ],
            'rotulos-e-etiquetas' => [
                'Adesivos para Parede',
                'Adesivos Recortados',
                'Adesivos BOPP',
                'Rotulos em Bobina',
                'Etiquetas Promocionais',
                'Adesivos para Festa',
                'Adesivos para Marmitinha',
                'Adesivos para Latinha',
                'Adesivos para Garrafinha',
            ],
            'comunicacao-visual' => [
                'Banners',
                'Faixas',
                'Lonas',
                'Display de Mesa',
                'Display de Chao',
                'Wind Banner',
                'Banner Roll-up',
                'Wobbler',
                'Mobiles',
                'PVC Expandido',
                'Cobre Placa',
                'Forro de Bandeja',
                'Tapetes Personalizados',
                'Capachos',
            ],
            'promocionais' => [
                'Convites',
                'Cordoes para Cracha',
                'Crachas',
                'Ingressos de Seguranca',
                'Mascaras',
                'Pulseiras de Identificacao',
                'Porta-copos',
                'Ventarolas',
                'Viseiras',
            ],
            'brindes-personalizados' => [
                'Canecas',
                'Copos',
                'Squeezes',
                'Canetas',
                'Chaveiros',
                'Capas de Celular',
                'Mouse Pads',
                'Chinelos',
                'Lixeira para Carro',
                'Lapis Personalizados',
            ],
            'produtos-corporativos' => [
                'Certificados',
                'Certificados Profissionais',
                'Cartoes de Proximidade',
                'CD / DVD Encartes',
                'Credenciais',
                'Fichas',
                'Pen Cards',
                'Rifas',
            ],
            'outros-produtos' => [
                'Sacolas Personalizadas',
                'Sacos Plasticos Personalizados',
                'Totens',
                'Balcoes',
                'Capas de Mala',
                'Tapetes para Carro',
                'Papel para Presente',
            ],
        ];
    }

    /**
     * @return array<int, string>
     */
    private function galleryPool(): array
    {
        return [
            '/demo/banners/cartao-visita-premium.svg',
            '/demo/banners/promocionais-campanha.svg',
            '/demo/banners/comunicacao-visual-pdv.svg',
            '/demo/banners/papelaria-premium.svg',
            '/demo/banners/adesivamento-vinil.svg',
        ];
    }

    private function minQuantityFor(string $categorySlug): int
    {
        return match ($categorySlug) {
            'comunicacao-visual' => 1,
            'outros-produtos' => 5,
            'brindes-personalizados' => 20,
            default => 50,
        };
    }

    private function basePriceFor(string $categorySlug, int $index): float
    {
        $base = match ($categorySlug) {
            'cartoes-e-papelaria' => 79.90,
            'rotulos-e-etiquetas' => 89.90,
            'comunicacao-visual' => 129.90,
            'promocionais' => 99.90,
            'brindes-personalizados' => 139.90,
            'produtos-corporativos' => 109.90,
            'outros-produtos' => 149.90,
            default => 99.90,
        };

        return round($base + (($index % 5) * 9.5), 2);
    }

    private function weightFor(string $categorySlug): int
    {
        return match ($categorySlug) {
            'comunicacao-visual' => 1000,
            'outros-produtos' => 900,
            'brindes-personalizados' => 500,
            default => 260,
        };
    }

    /**
     * @return array{tamanho: string, material: string, acabamento: string}
     */
    private function specificationsFor(string $categorySlug, string $productName): array
    {
        $normalized = Str::lower($productName);

        $size = match (true) {
            str_contains($normalized, 'mini') => '8 x 5 cm',
            str_contains($normalized, 'roll-up') => '85 x 200 cm',
            str_contains($normalized, 'wind banner') => '70 x 250 cm',
            str_contains($normalized, 'banner') => '80 x 120 cm',
            str_contains($normalized, 'cartao') => '9 x 5 cm',
            str_contains($normalized, 'poster') => 'A2',
            str_contains($normalized, 'cartaz') => 'A3',
            str_contains($normalized, 'folder') => 'A4 aberto',
            str_contains($normalized, 'panfleto') => 'A5',
            str_contains($normalized, 'jornal') => 'Tabloide',
            str_contains($normalized, 'cracha') => '10 x 14 cm',
            str_contains($normalized, 'pulseira') => '25 x 1,8 cm',
            str_contains($normalized, 'caneca') => '325 ml',
            str_contains($normalized, 'copo') => '500 ml',
            str_contains($normalized, 'squeeze') => '600 ml',
            str_contains($normalized, 'mouse pad') => '22 x 18 cm',
            str_contains($normalized, 'tapete') => '60 x 40 cm',
            str_contains($normalized, 'capacho') => '60 x 40 cm',
            default => 'Sob medida',
        };

        $material = match ($categorySlug) {
            'cartoes-e-papelaria' => 'Papel couche 250g',
            'rotulos-e-etiquetas' => 'Vinil adesivo / BOPP',
            'comunicacao-visual' => 'Lona 440g / PVC',
            'promocionais' => 'Papel couche 250g / PVC',
            'brindes-personalizados' => 'Polimero, aluminio ou tecido',
            'produtos-corporativos' => 'PVC, papel offset ou cartao',
            'outros-produtos' => 'Material conforme aplicacao',
            default => 'Material personalizado',
        };

        $finish = match ($categorySlug) {
            'cartoes-e-papelaria' => 'Corte reto, dobra e opcao de laminacao',
            'rotulos-e-etiquetas' => 'Corte especial e protecao fosca ou brilho',
            'comunicacao-visual' => 'Ilhos, bastao, solda e reforco',
            'promocionais' => 'Impressao colorida e acabamento de evento',
            'brindes-personalizados' => 'Aplicacao UV, silk ou sublimacao',
            'produtos-corporativos' => 'Numeracao, personalizacao e controle',
            'outros-produtos' => 'Acabamento definido em briefing tecnico',
            default => 'Acabamento sob consulta',
        };

        return [
            'tamanho' => $size,
            'papel_ou_material' => $material,
            'material' => $material,
            'acabamento' => $finish,
        ];
    }

    /**
     * @param  array{tamanho: string, material: string, acabamento: string}  $specifications
     * @return array<int, array{name: string, sku: string, attributes: array<string, string>, price: float, promotional_price: float|null, production_days: int}>
     */
    private function variantsFor(string $categorySlug, array $specifications, string $productSku, float $basePrice, int $leadTimeDays): array
    {
        $quantities = match ($categorySlug) {
            'comunicacao-visual' => [1, 5, 10],
            'outros-produtos' => [5, 10, 25],
            'brindes-personalizados' => [20, 50, 100],
            default => [100, 500, 1000],
        };

        $multipliers = [1.00, 1.95, 3.25];
        $variants = [];

        foreach ($quantities as $position => $quantity) {
            $price = round($basePrice * $multipliers[$position], 2);

            $variants[] = [
                'name' => sprintf('%d un | %s | %s', $quantity, (string) Arr::get($specifications, 'papel_ou_material', (string) Arr::get($specifications, 'material', 'Material')), (string) Arr::get($specifications, 'acabamento', 'Acabamento')),
                'sku' => $productSku.'-'.$quantity,
                'attributes' => [
                    'tiragem' => (string) $quantity,
                    'tamanho' => (string) Arr::get($specifications, 'tamanho', 'Sob medida'),
                    'material' => (string) Arr::get($specifications, 'papel_ou_material', (string) Arr::get($specifications, 'material', 'Material')),
                    'acabamento' => (string) Arr::get($specifications, 'acabamento', 'Acabamento'),
                ],
                'price' => $price,
                'promotional_price' => $position === 1 ? round($price * 0.93, 2) : null,
                'production_days' => $leadTimeDays + $position,
            ];
        }

        return $variants;
    }

    private function shortDescriptionFor(string $categorySlug, string $productName): string
    {
        $prefix = match ($categorySlug) {
            'cartoes-e-papelaria' => 'Papelaria comercial de alta qualidade para fortalecer identidade e apoio de vendas.',
            'rotulos-e-etiquetas' => 'Adesivos e rotulos com impressao profissional para marca, evento e embalagem.',
            'comunicacao-visual' => 'Comunicacao visual para destaque de marca em loja, evento e ponto de venda.',
            'promocionais' => 'Materiais para eventos com foco em organizacao, identificacao e impacto visual.',
            'brindes-personalizados' => 'Brindes personalizados para relacionamento, ativacao e lembranca de marca.',
            'produtos-corporativos' => 'Produtos corporativos para operacao, controle e apresentacao profissional.',
            'outros-produtos' => 'Solucoes especiais para projetos personalizados de comunicacao e varejo.',
            default => 'Produto grafico personalizado para sua demanda.',
        };

        return $productName.' - '.$prefix;
    }

    private function longDescriptionFor(string $productName, string $shortDescription): string
    {
        return $shortDescription."\n\n"
            .'Este produto permite personalizacao de arte, quantidade e observacoes tecnicas conforme sua necessidade.'
            ."\n".'Nossa equipe valida o arquivo e orienta o melhor acabamento antes da producao.';
    }
}
