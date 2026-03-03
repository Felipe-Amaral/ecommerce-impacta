<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('blog_categories')->nullOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title', 220);
            $table->string('slug', 240)->unique();
            $table->string('status', 30)->default('draft')->index();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('cover_image_url', 1200)->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->unsignedSmallInteger('reading_time_minutes')->default(1);
            $table->unsignedInteger('views_count')->default(0);
            $table->string('seo_title', 255)->nullable();
            $table->string('seo_description', 255)->nullable();
            $table->string('focus_keyword', 120)->nullable();
            $table->string('seo_canonical_url', 1200)->nullable();
            $table->string('seo_og_title', 255)->nullable();
            $table->string('seo_og_description', 255)->nullable();
            $table->string('seo_og_image_url', 1200)->nullable();
            $table->boolean('seo_noindex')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at']);
            $table->index(['is_featured', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
