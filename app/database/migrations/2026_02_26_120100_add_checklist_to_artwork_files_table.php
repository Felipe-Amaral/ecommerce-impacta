<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artwork_files', function (Blueprint $table): void {
            $table->json('checklist')->nullable()->after('size_bytes');
            $table->json('metadata')->nullable()->after('review_notes');
            $table->foreignId('uploaded_by_user_id')->nullable()->after('metadata')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('artwork_files', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('uploaded_by_user_id');
            $table->dropColumn(['checklist', 'metadata']);
        });
    }
};
