<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Миграция для создания таблицы аудита действий администраторов.
 *
 * Хранит полную историю изменений, сделанных администраторами:
 * - Кто сделал изменение (user_id)
 * - Какое действие выполнено (action)
 * - Какую модель изменили (model_type, model_id)
 * - Старые и новые значения (old_values, new_values)
 * - IP адрес и User Agent для отслеживания
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admin_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('action', 100);
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // Индексы для быстрого поиска
            $table->index('user_id');
            $table->index('action');
            $table->index('model_type');
            $table->index('model_id');
            $table->index('created_at');
            
            // Составной индекс для поиска по модели
            $table->index(['model_type', 'model_id']);
            
            // Индекс для поиска по дате и пользователю
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_audit_logs');
    }
};
