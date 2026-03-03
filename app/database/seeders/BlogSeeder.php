<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('is_admin', true)->first();

        $categories = collect([
            [
                'name' => 'Marketing Impresso',
                'slug' => 'marketing-impresso',
                'description' => 'Estratégias e ideias para usar impressos em campanhas comerciais.',
                'color_hex' => '#B56A24',
                'seo_title' => 'Marketing Impresso | Blog Uriah Criativa',
                'seo_description' => 'Ideias de materiais impressos para melhorar conversão, presença de marca e vendas locais.',
                'sort_order' => 10,
            ],
            [
                'name' => 'Acabamentos e Papel',
                'slug' => 'acabamentos-e-papel',
                'description' => 'Comparativos de papéis, laminações, vernizes e acabamentos especiais.',
                'color_hex' => '#6F5620',
                'seo_title' => 'Acabamentos e Papel | Blog Uriah Criativa',
                'seo_description' => 'Descubra como escolher papel, gramatura e acabamento para cada peça gráfica.',
                'sort_order' => 20,
            ],
            [
                'name' => 'Pré-impressão',
                'slug' => 'pre-impressao',
                'description' => 'Boas práticas para envio de arquivo e redução de retrabalho.',
                'color_hex' => '#2D5BAF',
                'seo_title' => 'Pré-impressão | Blog Uriah Criativa',
                'seo_description' => 'Checklist para preparar arquivos gráficos com qualidade e sem erros de produção.',
                'sort_order' => 30,
            ],
        ])->mapWithKeys(fn (array $row) => [
            $row['slug'] => BlogCategory::query()->updateOrCreate(['slug' => $row['slug']], $row),
        ]);

        $tags = collect([
            ['name' => 'Cartão de visita', 'slug' => 'cartao-de-visita', 'is_featured' => true],
            ['name' => 'Flyer', 'slug' => 'flyer', 'is_featured' => true],
            ['name' => 'Papel couchê', 'slug' => 'papel-couche', 'is_featured' => false],
            ['name' => 'Verniz localizado', 'slug' => 'verniz-localizado', 'is_featured' => false],
            ['name' => 'Fechamento de arquivo', 'slug' => 'fechamento-de-arquivo', 'is_featured' => true],
            ['name' => 'Branding', 'slug' => 'branding', 'is_featured' => false],
            ['name' => 'PDV', 'slug' => 'pdv', 'is_featured' => false],
        ])->mapWithKeys(fn (array $row) => [
            $row['slug'] => BlogTag::query()->updateOrCreate(['slug' => $row['slug']], $row),
        ]);

        $posts = [
            [
                'title' => 'Como escolher o acabamento ideal para cartão de visita premium',
                'category_slug' => 'acabamentos-e-papel',
                'status' => 'published',
                'is_featured' => true,
                'published_at' => now()->subDays(5),
                'cover_image_url' => '/demo/banners/cartao-visita-premium.svg',
                'excerpt' => 'Laminação fosca, verniz localizado, hot stamping ou canto arredondado: entenda quando usar cada acabamento e como elevar a percepção da sua marca.',
                'content' => <<<'MD'
Um cartão de visita premium não precisa ser o mais caro. Ele precisa ser coerente com o posicionamento da marca.

## 1) Comece pela proposta da marca
Se a sua marca é minimalista e elegante, aposte em:
- papel mais encorpado,
- laminação fosca,
- pouco texto e bom respiro.

Se a marca é vibrante e promocional, combine:
- cores saturadas,
- verniz localizado em áreas estratégicas,
- papel com boa resposta de cor.

## 2) Compare os acabamentos mais usados
**Laminação fosca:** toque suave e visual sofisticado.

**Laminação brilho:** cores vivas e contraste forte.

**Verniz localizado:** destaque pontual para logotipo ou headline.

**Hot stamping:** efeito metálico para marcas premium.

## 3) Evite erros clássicos
- Texto pequeno demais.
- Falta de sangria e margem de segurança.
- Excesso de informação no verso.

## 4) Checklist rápido
Antes de aprovar a produção:
1. Revise ortografia e telefone.
2. Confirme modo de cor CMYK.
3. Verifique se as fontes estão convertidas em curva.

Quando o acabamento reforça a estratégia da marca, o cartão vira um ativo comercial, não apenas um contato.
MD,
                'seo_title' => 'Acabamento para Cartão de Visita Premium | Blog Uriah Criativa',
                'seo_description' => 'Saiba como escolher acabamento para cartão de visita premium com foco em branding, legibilidade e impacto visual.',
                'focus_keyword' => 'acabamento cartão de visita premium',
                'seo_og_title' => 'Cartão de visita premium: acabamentos que valorizam sua marca',
                'seo_og_description' => 'Guia prático para escolher acabamento gráfico com estratégia.',
                'tags' => ['cartao-de-visita', 'verniz-localizado', 'branding'],
            ],
            [
                'title' => 'Checklist de pré-impressão: 12 pontos para não travar sua produção',
                'category_slug' => 'pre-impressao',
                'status' => 'published',
                'is_featured' => false,
                'published_at' => now()->subDays(3),
                'cover_image_url' => '/demo/banners/papelaria-premium.svg',
                'excerpt' => 'Um checklist objetivo para reduzir retrabalho, evitar arquivos reprovados e acelerar a liberação do seu pedido.',
                'content' => <<<'MD'
Retrabalho em gráfica quase sempre nasce no arquivo.

## Checklist essencial
1. **CMYK** em todos os elementos.
2. **300 DPI** para imagens principais.
3. **Sangria** configurada corretamente.
4. **Margem de segurança** para textos.
5. **Fontes em curva**.
6. **Preto composto** apenas onde necessário.
7. **Overprint** revisado.
8. **Transparências** verificadas.
9. **PDF/X** quando possível.
10. **Tamanho final** da arte conferido.
11. **Nomenclatura de arquivo** clara.
12. **Versão final única** enviada.

## Resultado prático
Com esse padrão, seu time reduz aprovação pendente e ganha velocidade de produção sem perder qualidade.
MD,
                'seo_title' => 'Checklist de Pré-impressão para Gráfica | Blog Uriah Criativa',
                'seo_description' => 'Confira 12 pontos críticos de pré-impressão para evitar erro de arquivo e ganhar velocidade na produção gráfica.',
                'focus_keyword' => 'checklist pré-impressão gráfica',
                'tags' => ['fechamento-de-arquivo', 'papel-couche'],
            ],
            [
                'title' => 'Flyers que convertem no balcão: estrutura visual para campanhas locais',
                'category_slug' => 'marketing-impresso',
                'status' => 'published',
                'is_featured' => false,
                'published_at' => now()->subDays(2),
                'cover_image_url' => '/demo/banners/promocionais-campanha.svg',
                'excerpt' => 'Aprenda a montar flyers de alta performance com hierarquia visual, chamada de ação clara e oferta irresistível.',
                'content' => <<<'MD'
Flyer bom não é flyer bonito. É flyer que gera ação.

## Estrutura recomendada
- **Topo:** benefício principal em 1 frase.
- **Meio:** prova ou argumento de confiança.
- **Rodapé:** chamada com urgência + contato.

## Elementos que ajudam a converter
- Uma oferta principal por peça.
- Tipografia forte para preço e prazo.
- QR Code com destino rastreável.

## Métrica mínima
Se você não mede retorno por bairro ou ponto de distribuição, está imprimindo no escuro.
MD,
                'seo_title' => 'Como Criar Flyers que Convertem | Blog Uriah Criativa',
                'seo_description' => 'Guia para estruturar flyers comerciais com foco em conversão, oferta e chamada para ação.',
                'focus_keyword' => 'flyer que converte',
                'tags' => ['flyer', 'branding', 'pdv'],
            ],
            [
                'title' => 'Guia rápido de gramatura: quando usar 90g, 150g, 250g e 300g',
                'category_slug' => 'acabamentos-e-papel',
                'status' => 'published',
                'is_featured' => false,
                'published_at' => now()->subDay(),
                'cover_image_url' => '/demo/banners/adesivamento-vinil.svg',
                'excerpt' => 'Escolher a gramatura certa evita custo desnecessário e melhora a experiência final do impresso.',
                'content' => <<<'MD'
A gramatura impacta custo, toque e percepção de qualidade.

## Regra simples
- **90g a 120g:** materiais de alto volume e baixo custo.
- **150g a 210g:** folhetos e peças de apoio.
- **250g a 300g:** cartões, capas e materiais premium.

## Pense no uso real
Um folder manuseado várias vezes pede gramatura superior. Um encarte de distribuição massiva pede equilíbrio de custo.
MD,
                'seo_title' => 'Gramatura de Papel na Prática | Blog Uriah Criativa',
                'seo_description' => 'Entenda como escolher gramatura de papel para cada tipo de material gráfico.',
                'focus_keyword' => 'gramatura de papel para gráfica',
                'tags' => ['papel-couche', 'flyer'],
            ],
            [
                'title' => 'Campanhas de PDV: como integrar materiais impressos e presença digital',
                'category_slug' => 'marketing-impresso',
                'status' => 'draft',
                'is_featured' => false,
                'published_at' => null,
                'cover_image_url' => '/demo/banners/comunicacao-visual-pdv.svg',
                'excerpt' => 'Do display ao QR Code: combinação de canais para aumentar resultado em campanhas de ponto de venda.',
                'content' => <<<'MD'
Conteúdo em desenvolvimento.
MD,
                'seo_title' => 'Campanhas de PDV Integradas | Blog Uriah Criativa',
                'seo_description' => 'Estratégia omnichannel aplicada a materiais de ponto de venda.',
                'focus_keyword' => 'campanha pdv integrada',
                'tags' => ['pdv', 'branding'],
            ],
        ];

        foreach ($posts as $row) {
            $slug = Str::slug((string) $row['title']);
            $category = $categories->get($row['category_slug']);
            $tagIds = collect($row['tags'] ?? [])->map(fn (string $tagSlug) => $tags->get($tagSlug)?->id)->filter()->values();

            $post = BlogPost::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $category?->id,
                    'author_id' => $admin?->id,
                    'title' => $row['title'],
                    'status' => $row['status'],
                    'excerpt' => $row['excerpt'],
                    'content' => $row['content'],
                    'cover_image_url' => $row['cover_image_url'] ?? null,
                    'is_featured' => (bool) ($row['is_featured'] ?? false),
                    'published_at' => $row['published_at'] ?? null,
                    'reading_time_minutes' => max(1, (int) ceil(str_word_count(strip_tags((string) $row['content'])) / 210)),
                    'seo_title' => $row['seo_title'] ?? null,
                    'seo_description' => $row['seo_description'] ?? null,
                    'focus_keyword' => $row['focus_keyword'] ?? null,
                    'seo_canonical_url' => null,
                    'seo_og_title' => $row['seo_og_title'] ?? null,
                    'seo_og_description' => $row['seo_og_description'] ?? null,
                    'seo_og_image_url' => null,
                    'seo_noindex' => false,
                ],
            );

            $post->tags()->sync($tagIds);
        }
    }
}
