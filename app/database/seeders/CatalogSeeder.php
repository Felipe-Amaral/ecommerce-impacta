<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Cartões e Papelaria',
                'slug' => 'cartoes-e-papelaria',
                'description' => 'Cartões de visita, papel timbrado, envelopes e materiais institucionais.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Promocionais',
                'slug' => 'promocionais',
                'description' => 'Flyers, folders, panfletos e materiais para campanhas de venda.',
                'sort_order' => 2,
            ],
            [
                'name' => 'Comunicação Visual',
                'slug' => 'comunicacao-visual',
                'description' => 'Banners, faixas, lonas, adesivos e peças de exposição.',
                'sort_order' => 3,
            ],
            [
                'name' => 'Rótulos e Etiquetas',
                'slug' => 'rotulos-e-etiquetas',
                'description' => 'Etiquetas adesivas e rótulos para produtos e embalagens.',
                'sort_order' => 4,
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::query()->updateOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData + ['is_active' => true],
            );
        }

        $catalog = [
            [
                'category_slug' => 'cartoes-e-papelaria',
                'name' => 'Cartão de Visita Premium',
                'slug' => 'cartao-de-visita-premium',
                'sku' => 'CV-PREMIUM',
                'short_description' => 'Cartões de visita em couchê 300g com opções de laminação e verniz.',
                'description' => "Ideal para apresentação profissional de marcas e equipes comerciais.\nProdução com corte preciso e acabamento premium.",
                'is_featured' => true,
                'lead_time_days' => 3,
                'min_quantity' => 100,
                'base_price' => 79.90,
                'specifications' => [
                    'formato' => '9x5 cm',
                    'impressao' => '4x4 cores',
                    'papel' => ['Couchê 300g', 'Supremo 300g'],
                    'acabamentos' => ['Laminação fosca', 'Verniz localizado'],
                ],
                'variants' => [
                    [
                        'name' => '100 un | Couchê 300g | 4x4',
                        'sku' => 'CVP-100-C300-44',
                        'price' => 79.90,
                        'promotional_price' => 69.90,
                        'production_days' => 3,
                        'attributes' => [
                            'tiragem' => '100',
                            'papel' => 'Couchê 300g',
                            'impressao' => '4x4',
                            'acabamento' => 'Sem laminação',
                        ],
                    ],
                    [
                        'name' => '500 un | Couchê 300g | Laminação fosca',
                        'sku' => 'CVP-500-C300-LF',
                        'price' => 169.90,
                        'production_days' => 4,
                        'attributes' => [
                            'tiragem' => '500',
                            'papel' => 'Couchê 300g',
                            'acabamento' => 'Laminação fosca',
                        ],
                    ],
                    [
                        'name' => '1000 un | Couchê 300g | Verniz localizado',
                        'sku' => 'CVP-1000-C300-VL',
                        'price' => 289.90,
                        'production_days' => 5,
                        'attributes' => [
                            'tiragem' => '1000',
                            'papel' => 'Couchê 300g',
                            'acabamento' => 'Verniz localizado',
                        ],
                    ],
                ],
            ],
            [
                'category_slug' => 'promocionais',
                'name' => 'Flyer A5 Promocional',
                'slug' => 'flyer-a5-promocional',
                'sku' => 'FLY-A5',
                'short_description' => 'Flyers em alta qualidade para divulgação local e campanhas sazonais.',
                'description' => "Material versátil para ações de marketing direto, distribuição em pontos de venda e eventos.\nDisponível em tiragens escaláveis.",
                'is_featured' => true,
                'lead_time_days' => 2,
                'min_quantity' => 250,
                'base_price' => 129.90,
                'specifications' => [
                    'formato' => 'A5 (14,8x21 cm)',
                    'papel' => ['Couchê 115g', 'Couchê 150g'],
                    'impressao' => '4x4 ou 4x0',
                ],
                'variants' => [
                    [
                        'name' => '500 un | Couchê 115g | 4x4',
                        'sku' => 'FLA5-500-C115-44',
                        'price' => 129.90,
                        'production_days' => 2,
                        'attributes' => [
                            'tiragem' => '500',
                            'papel' => 'Couchê 115g',
                            'impressao' => '4x4',
                        ],
                    ],
                    [
                        'name' => '1000 un | Couchê 115g | 4x4',
                        'sku' => 'FLA5-1000-C115-44',
                        'price' => 199.90,
                        'promotional_price' => 184.90,
                        'production_days' => 2,
                        'attributes' => [
                            'tiragem' => '1000',
                            'papel' => 'Couchê 115g',
                            'impressao' => '4x4',
                        ],
                    ],
                    [
                        'name' => '2500 un | Couchê 150g | 4x4',
                        'sku' => 'FLA5-2500-C150-44',
                        'price' => 399.90,
                        'production_days' => 3,
                        'attributes' => [
                            'tiragem' => '2500',
                            'papel' => 'Couchê 150g',
                            'impressao' => '4x4',
                        ],
                    ],
                ],
            ],
            [
                'category_slug' => 'comunicacao-visual',
                'name' => 'Banner em Lona 440g',
                'slug' => 'banner-lona-440g',
                'sku' => 'BNR-LONA-440',
                'short_description' => 'Banners para ponto de venda, eventos e divulgação interna/externa.',
                'description' => "Impressão UV ou solvente em lona 440g com acabamento de bastão e corda opcional.\nIdeal para campanhas promocionais e sinalização.",
                'is_featured' => true,
                'lead_time_days' => 3,
                'min_quantity' => 1,
                'base_price' => 89.90,
                'specifications' => [
                    'material' => 'Lona 440g',
                    'acabamento' => ['Bastão e corda', 'Ilhós'],
                    'uso' => ['Interno', 'Externo'],
                ],
                'variants' => [
                    [
                        'name' => '60x90 cm | Bastão e corda',
                        'sku' => 'BNR-60X90-BC',
                        'price' => 89.90,
                        'production_days' => 2,
                        'attributes' => [
                            'tamanho' => '60x90 cm',
                            'acabamento' => 'Bastão e corda',
                        ],
                    ],
                    [
                        'name' => '80x120 cm | Ilhós',
                        'sku' => 'BNR-80X120-IL',
                        'price' => 139.90,
                        'production_days' => 3,
                        'attributes' => [
                            'tamanho' => '80x120 cm',
                            'acabamento' => 'Ilhós',
                        ],
                    ],
                    [
                        'name' => '100x150 cm | Ilhós reforçado',
                        'sku' => 'BNR-100X150-IR',
                        'price' => 209.90,
                        'production_days' => 4,
                        'attributes' => [
                            'tamanho' => '100x150 cm',
                            'acabamento' => 'Ilhós reforçado',
                        ],
                    ],
                ],
            ],
            [
                'category_slug' => 'rotulos-e-etiquetas',
                'name' => 'Etiqueta Adesiva Vinil Corte Especial',
                'slug' => 'etiqueta-adesiva-vinil-corte-especial',
                'sku' => 'ETQ-VINIL-CE',
                'short_description' => 'Etiquetas em vinil com corte personalizado para embalagens e branding.',
                'description' => "Perfeitas para marcas de alimentos, cosméticos e produtos artesanais.\nAlta durabilidade e acabamento profissional.",
                'is_featured' => false,
                'lead_time_days' => 4,
                'min_quantity' => 50,
                'base_price' => 119.90,
                'specifications' => [
                    'material' => 'Vinil adesivo',
                    'acabamento' => ['Brilho', 'Fosco'],
                    'recorte' => 'Especial',
                ],
                'variants' => [
                    [
                        'name' => '100 un | 5x5 cm | Brilho',
                        'sku' => 'ETQ-100-5X5-BR',
                        'price' => 119.90,
                        'production_days' => 4,
                        'attributes' => [
                            'tiragem' => '100',
                            'tamanho' => '5x5 cm',
                            'acabamento' => 'Brilho',
                        ],
                    ],
                    [
                        'name' => '500 un | 5x5 cm | Fosco',
                        'sku' => 'ETQ-500-5X5-FO',
                        'price' => 349.90,
                        'promotional_price' => 319.90,
                        'production_days' => 5,
                        'attributes' => [
                            'tiragem' => '500',
                            'tamanho' => '5x5 cm',
                            'acabamento' => 'Fosco',
                        ],
                    ],
                ],
            ],
        ];

        foreach ($catalog as $productData) {
            $category = Category::query()->where('slug', $productData['category_slug'])->firstOrFail();

            $product = Product::query()->updateOrCreate(
                ['slug' => $productData['slug']],
                [
                    'category_id' => $category->id,
                    'name' => $productData['name'],
                    'sku' => $productData['sku'],
                    'short_description' => $productData['short_description'],
                    'description' => $productData['description'],
                    'product_type' => 'print',
                    'is_customizable' => true,
                    'is_active' => true,
                    'is_featured' => $productData['is_featured'],
                    'lead_time_days' => $productData['lead_time_days'],
                    'min_quantity' => $productData['min_quantity'],
                    'base_price' => $productData['base_price'],
                    'seo_title' => $productData['name'].' | Gráfica Impacta',
                    'seo_description' => $productData['short_description'],
                    'specifications' => $productData['specifications'],
                ],
            );

            $existingVariantIds = [];

            foreach ($productData['variants'] as $index => $variantData) {
                $variant = ProductVariant::query()->updateOrCreate(
                    ['sku' => $variantData['sku']],
                    [
                        'product_id' => $product->id,
                        'name' => $variantData['name'],
                        'attributes' => $variantData['attributes'] ?? [],
                        'price' => $variantData['price'],
                        'promotional_price' => $variantData['promotional_price'] ?? null,
                        'production_days' => $variantData['production_days'] ?? null,
                        'weight_grams' => $variantData['weight_grams'] ?? null,
                        'stock_qty' => null,
                        'is_active' => true,
                        'sort_order' => $index + 1,
                    ],
                );

                $existingVariantIds[] = $variant->id;
            }

            $product->variants()
                ->whereNotIn('id', $existingVariantIds)
                ->delete();
        }
    }
}
