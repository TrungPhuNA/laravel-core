<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecm_cart_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('cart_id')->index();
            $table->unsignedBigInteger('product_id')->nullable()->index();
            $table->unsignedBigInteger('variant_id')->nullable()->index();

            $table->string('sku', 80)->nullable()->index();
            $table->string('name', 255);
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 18, 2)->default(0);
            $table->decimal('total_price', 18, 2)->default(0);
            $table->json('meta')->nullable();

            $table->timestamps();

            $table
                ->foreign('shop_id')
                ->references('id')
                ->on('ecm_shops')
                ->cascadeOnDelete();

            $table
                ->foreign('cart_id')
                ->references('id')
                ->on('ecm_carts')
                ->cascadeOnDelete();

            $table
                ->foreign('product_id')
                ->references('id')
                ->on('ecm_products')
                ->nullOnDelete();

            $table
                ->foreign('variant_id')
                ->references('id')
                ->on('ecm_product_variants')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecm_cart_items');
    }
};
