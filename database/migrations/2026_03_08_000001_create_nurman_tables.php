<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Роли
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->json('permissions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Пользователи
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('email')->unique()->nullable();
            $table->string('phone')->unique()->nullable();
            $table->string('password');
            $table->string('full_name')->nullable();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->string('avatar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // Категории
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique()->nullable();
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Товары
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('article')->unique();
            $table->string('slug')->unique()->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->text('description')->nullable();
            $table->string('brand')->nullable();
            $table->string('material')->nullable();
            $table->string('country')->nullable();
            $table->string('photo')->nullable();
            $table->json('photos')->nullable();
            $table->decimal('retail_price', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // Варианты товаров
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->string('sku')->unique();
            $table->decimal('supplier_price', 10, 2)->default(0);
            $table->decimal('wholesale_price', 10, 2)->nullable()->default(0);
            $table->integer('barcode')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['product_id', 'size', 'color']);
        });

        // Магазины
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('manager')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Склады
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->foreignId('manager_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Остатки
        Schema::create('stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->foreignId('shop_id')->nullable()->constrained('shops')->onDelete('cascade');
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->integer('reserved')->default(0);
            $table->integer('min_stock')->default(5);
            $table->timestamps();
            $table->unique(['variant_id', 'shop_id', 'warehouse_id'], 'unique_stock');
        });

        // Закупки
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->nullable();
            $table->foreignId('variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->foreignId('shop_id')->nullable()->constrained('shops')->onDelete('set null');
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->onDelete('set null');
            $table->integer('quantity');
            $table->decimal('supplier_price', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->foreignId('supplier_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('received_by')->constrained('users')->onDelete('cascade');
            $table->text('comment')->nullable();
            $table->date('purchase_date')->nullable();
            $table->timestamps();
        });

        // QR-коды
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->string('qr_code_path')->nullable();
            $table->string('qr_data');
            $table->boolean('is_printed')->default(false);
            $table->timestamp('printed_at')->nullable();
            $table->foreignId('printed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // Купоны
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->enum('discount_type', ['percent', 'fixed', 'wholesale']);
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->decimal('min_purchase_amount', 10, 2)->default(0);
            $table->integer('usage_limit')->default(1);
            $table->integer('usage_count')->default(0);
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        // Транзакции
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->enum('type', ['sale', 'return', 'expense', 'purchase']);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->enum('payment_method', ['cash', 'card', 'transfer']);
            $table->foreignId('shop_id')->constrained('shops')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->onDelete('set null');
            $table->text('comment')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index(['type', 'created_at']);
        });

        // Позиции транзакций
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->foreignId('variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('supplier_price', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });

        // Расходы
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_number')->unique()->nullable();
            $table->enum('type', ['salary', 'rent', 'utilities', 'tax', 'office', 'marketing', 'other']);
            $table->string('category')->nullable();
            $table->decimal('amount', 10, 2);
            $table->foreignId('shop_id')->nullable()->constrained('shops')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->date('expense_date')->nullable();
            $table->string('receipt_photo')->nullable();
            $table->timestamps();
        });

        // Возвраты
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_number')->unique();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->foreignId('variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('refund_amount', 10, 2);
            $table->enum('reason', ['defect', 'size', 'color', 'quality', 'other']);
            $table->text('comment')->nullable();
            $table->foreignId('processed_by')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->timestamps();
        });

        // Профили клиентов
        Schema::create('customer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->integer('bonus_points')->default(0);
            $table->decimal('total_purchases', 10, 2)->default(0);
            $table->integer('purchase_count')->default(0);
            $table->timestamp('last_purchase_at')->nullable();
            $table->boolean('newsletter_subscribed')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // История покупок
        Schema::create('customer_purchase_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('transaction_id')->unique()->constrained('transactions')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->integer('bonus_earned')->default(0);
            $table->integer('bonus_spent')->default(0);
            $table->timestamps();
        });

        // Уведомления
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('type');
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // Журнал действий
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action');
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('customer_purchase_history');
        Schema::dropIfExists('customer_profiles');
        Schema::dropIfExists('returns');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('transaction_items');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('qr_codes');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('stock');
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('shops');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
    }
};
