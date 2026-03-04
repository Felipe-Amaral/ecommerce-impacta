<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_chat_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('live_chat_session_id')->constrained('live_chat_sessions')->cascadeOnDelete();
            $table->string('sender_role', 24)->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('body');
            $table->boolean('is_read_by_visitor')->default(false)->index();
            $table->boolean('is_read_by_admin')->default(false)->index();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['live_chat_session_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_chat_messages');
    }
};
