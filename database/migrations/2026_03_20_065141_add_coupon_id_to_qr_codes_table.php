<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->after('variant_id')->constrained('coupons')->onDelete('cascade');
            $table->index('coupon_id');
            
            // Делаем variant_id nullable для QR-кодов купонов
            $table->foreignId('variant_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropIndex(['coupon_id']);
            $table->dropColumn('coupon_id');
            
            $table->foreignId('variant_id')->nullable(false)->change();
        });
    }
};
