<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecm_product_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('product_id')->index();

            $table->string('sku', 80)->nullable();
            $table->string('name', 255)->nullable();
            $table->json('options')->nullable(); // { "Color": "Red", "Size": "M" }

            $table->decimal('price', 18, 2)->nullable();
            $table->decimal('compare_at_price', 18, 2)->nullable();
            $table->decimal('cost_price', 18, 2)->nullable();
            $table->string('currency', 3)->nullable();

            $table->integer('stock_qty')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();

            $table->json('meta')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['shop_id', 'sku']);

            $table
                ->foreign('shop_id')
                ->references('id')
                ->on('ecm_shops')
                ->cascadeOnDelete();

            $table
                ->foreign('product_id')
                ->references('id')
                ->on('ecm_products')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecm_product_variants');
    }
};
