<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_visitors', function (Blueprint $table): void {
            $table->id();
            $table->string('visitor_token', 64)->unique();
            $table->string('session_id', 120)->nullable()->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address', 45)->nullable()->index();
            $table->string('user_agent', 500)->nullable();
            $table->string('landing_url', 1200)->nullable();
            $table->string('current_url', 1200)->nullable();
            $table->string('current_path', 600)->nullable()->index();
            $table->string('referrer_url', 1200)->nullable();
            $table->string('page_title', 255)->nullable();
            $table->string('country_code', 8)->nullable();
            $table->string('timezone', 80)->nullable();
            $table->string('language', 40)->nullable();
            $table->string('screen_size', 40)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('first_seen_at')->nullable()->index();
            $table->timestamp('last_seen_at')->nullable()->index();
            $table->timestamps();

            $table->index(['user_id', 'last_seen_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_visitors');
    }
};
