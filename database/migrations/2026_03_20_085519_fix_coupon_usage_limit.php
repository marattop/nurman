<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Исправляем купоны у которых usage_limit = 0 или NULL
        DB::table('coupons')
            ->where('usage_limit', 0)
            ->orWhereNull('usage_limit')
            ->update(['usage_limit' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Не делаем ничего - обратно не возвращаем
    }
};
