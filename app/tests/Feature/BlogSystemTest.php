<?php

namespace Tests\Feature;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_index_shows_only_public_posts_and_guest_cannot_open_draft(): void
    {
        $category = BlogCategory::query()->create([
            'name' => 'Conteúdo',
            'slug' => 'conteudo',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $published = BlogPost::query()->create([
            'category_id' => $category->id,
            'title' => 'Post publicado',
            'slug' => 'post-publicado',
            'status' => 'published',
            'excerpt' => 'Resumo publicado',
            'content' => "## Conteúdo\nTexto do artigo publicado.",
            'is_featured' => true,
            'published_at' => now()->subHour(),
            'reading_time_minutes' => 2,
        ]);

        BlogPost::query()->create([
            'category_id' => $category->id,
            'title' => 'Post rascunho',
            'slug' => 'post-rascunho',
            'status' => 'draft',
            'content' => 'Rascunho interno',
            'published_at' => null,
            'reading_time_minutes' => 1,
        ]);

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSee('Post publicado')
            ->assertDontSee('Post rascunho');

        $this->get(route('blog.show', $published->slug))
            ->assertOk()
            ->assertSee('Post publicado')
            ->assertSee('meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1"', false);

        $this->get(route('blog.show', 'post-rascunho'))->assertNotFound();
    }

    public function test_admin_can_create_blog_post_with_seo_and_tags(): void
    {
        $this->withoutMiddleware(ValidateCsrfToken::class);

        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $category = BlogCategory::query()->create([
            'name' => 'Marketing',
            'slug' => 'marketing',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $existingTag = BlogTag::query()->create([
            'name' => 'Branding',
            'slug' => 'branding',
            'is_featured' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.blog.store'), [
            'category_id' => $category->id,
            'title' => 'Estratégia de Impressos para Lançamento',
            'slug' => 'estrategia-impressos-lancamento',
            'status' => 'published',
            'published_at' => now()->format('Y-m-d H:i:s'),
            'excerpt' => 'Resumo para SEO e vitrine.',
            'content' => "## Introdução\nConteúdo completo em markdown.",
            'is_featured' => 1,
            'tag_ids' => [$existingTag->id],
            'new_tags' => 'campanha local, materiais promocionais',
            'seo_title' => 'SEO Title do Artigo',
            'seo_description' => 'Meta description otimizada para CTR e intenção de busca.',
            'focus_keyword' => 'estratégia de impressos',
            'seo_canonical_url' => 'https://example.com/blog/estrategia-impressos-lancamento',
            'seo_og_title' => 'OG title personalizado',
            'seo_og_description' => 'OG description personalizada',
        ]);

        $post = BlogPost::query()->where('slug', 'estrategia-impressos-lancamento')->first();

        $this->assertNotNull($post);
        $response->assertRedirect(route('admin.blog.edit', $post));

        $this->assertDatabaseHas('blog_posts', [
            'id' => $post->id,
            'title' => 'Estratégia de Impressos para Lançamento',
            'status' => 'published',
            'is_featured' => true,
            'seo_title' => 'SEO Title do Artigo',
            'focus_keyword' => 'estratégia de impressos',
        ]);

        $this->assertDatabaseHas('blog_tags', [
            'slug' => 'campanha-local',
        ]);

        $this->assertDatabaseHas('blog_post_tag', [
            'blog_post_id' => $post->id,
            'blog_tag_id' => $existingTag->id,
        ]);
    }

    public function test_admin_can_preview_draft_post_on_public_route(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $draft = BlogPost::query()->create([
            'title' => 'Draft Privado',
            'slug' => 'draft-privado',
            'status' => 'draft',
            'content' => 'Conteúdo em revisão',
            'reading_time_minutes' => 1,
        ]);

        $this->actingAs($admin)
            ->get(route('blog.show', $draft->slug))
            ->assertOk()
            ->assertSee('Pré-visualização de administrador');
    }
}
