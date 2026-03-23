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
        // Исправляем product_variants
        Schema::table('product_variants', function (Blueprint $table) {
            $table->decimal('supplier_price', 10, 2)->default(0)->change();
            $table->decimal('wholesale_price', 10, 2)->nullable()->default(0)->change();
        });
        
        // Исправляем qr_codes - делаем qr_code_path nullable
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->string('qr_code_path')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->decimal('supplier_price', 10, 2)->change();
            $table->decimal('wholesale_price', 10, 2)->nullable()->change();
        });
        
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->string('qr_code_path')->change();
        });
    }
};
