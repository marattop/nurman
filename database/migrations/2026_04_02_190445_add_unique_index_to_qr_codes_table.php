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
        Schema::table('qr_codes', function (Blueprint $table) {
            // Удаляем дубликаты, оставляя только последний для каждого variant_id
            $duplicates = DB::select("
                SELECT id, variant_id,
                ROW_NUMBER() OVER (PARTITION BY variant_id ORDER BY created_at DESC) as rn
                FROM qr_codes
            ");
            
            foreach ($duplicates as $duplicate) {
                if ($duplicate->rn > 1) {
                    DB::table('qr_codes')->where('id', $duplicate->id)->delete();
                }
            }
            
            // Добавляем уникальное ограничение
            $table->unique('variant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->dropUnique(['variant_id']);
        });
    }
};
