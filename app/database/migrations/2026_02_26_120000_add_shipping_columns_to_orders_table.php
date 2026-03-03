<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->string('shipping_method_code')->nullable()->after('shipping_total')->index();
            $table->string('shipping_method_label')->nullable()->after('shipping_method_code');
            $table->string('shipping_provider')->nullable()->after('shipping_method_label');
            $table->unsignedSmallInteger('shipping_delivery_days')->nullable()->after('shipping_provider');
            $table->json('shipping_quote_payload')->nullable()->after('shipping_address');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn([
                'shipping_method_code',
                'shipping_method_label',
                'shipping_provider',
                'shipping_delivery_days',
                'shipping_quote_payload',
            ]);
        });
    }
};
