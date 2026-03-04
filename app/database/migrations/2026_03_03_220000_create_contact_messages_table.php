<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 120);
            $table->string('email', 190)->index();
            $table->string('phone', 40)->nullable();
            $table->string('subject', 140);
            $table->string('service_interest', 120)->nullable();
            $table->string('preferred_contact', 30)->nullable();
            $table->string('order_reference', 80)->nullable();
            $table->text('message');
            $table->boolean('lgpd_consent')->default(false);
            $table->string('status', 30)->default('new')->index();
            $table->string('source_url', 1200)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
