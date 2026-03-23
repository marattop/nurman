<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Запуск миграции.
     * Создаёт таблицы для системы ролей и разрешений (RBAC).
     */
    public function up(): void
    {
        // Таблица разрешений (permissions)
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Уникальное имя разрешения, например: products.create');
            $table->string('display_name')->comment('Читаемое название, например: Создание товаров');
            $table->string('group')->default('default')->comment('Группа разрешений: products, sales, finance, users, settings, warehouse');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('group');
        });

        // Таблица связи ролей и разрешений (role_has_permissions)
        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->primary(['role_id', 'permission_id']);
        });

        // Добавляем поля в таблицу roles для иерархии
        Schema::table('roles', function (Blueprint $table) {
            $table->unsignedInteger('level')->default(0)->after('id')->comment('Уровень роли: 100=Owner, 80=Admin, 50=Seller, 20=Warehouse');
            $table->foreignId('parent_id')->nullable()->after('level')->constrained('roles')->onDelete('set null')->comment('Родительская роль');
        });

        // Добавляем таблицу сессий для управления сессиями пользователей
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index()->constrained('users')->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['level', 'parent_id']);
        });
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('permissions');
    }
};
