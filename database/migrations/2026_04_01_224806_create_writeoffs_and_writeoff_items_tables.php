<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('writeoffs', function (Blueprint $table) {
            $table->id();
            $table->string('reason'); // damage, loss, expired, other
            $table->string('location_type'); // shop, warehouse
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
        });

        Schema::create('writeoff_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('writeoff_id');
            $table->unsignedBigInteger('variant_id');
            $table->integer('quantity');
            $table->timestamps();

            $table->foreign('writeoff_id')->references('id')->on('writeoffs')->onDelete('cascade');
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('writeoff_items');
        Schema::dropIfExists('writeoffs');
    }
};
