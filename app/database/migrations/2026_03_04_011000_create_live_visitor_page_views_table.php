<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_visitor_page_views', function (Blueprint $table): void {
            $table->id();
            $table->string('visitor_token', 64)->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id', 120)->nullable()->index();
            $table->string('path', 600)->nullable()->index();
            $table->string('url', 1200)->nullable();
            $table->string('page_title', 255)->nullable();
            $table->string('referrer_url', 1200)->nullable();
            $table->timestamp('entered_at')->nullable()->index();
            $table->timestamp('left_at')->nullable()->index();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->string('exit_type', 30)->nullable()->index();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['visitor_token', 'session_id', 'entered_at'], 'live_visitor_page_views_visitor_session_entered');
            $table->index(['session_id', 'left_at'], 'live_visitor_page_views_session_left');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_visitor_page_views');
    }
};
