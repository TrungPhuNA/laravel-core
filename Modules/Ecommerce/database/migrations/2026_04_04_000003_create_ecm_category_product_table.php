<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecm_category_product', function (Blueprint $table) {
            $table->unsignedBigInteger('shop_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('product_id');

            $table->primary(['shop_id', 'category_id', 'product_id']);
            $table->index(['category_id', 'product_id']);

            $table
                ->foreign('shop_id')
                ->references('id')
                ->on('ecm_shops')
                ->cascadeOnDelete();

            $table
                ->foreign('category_id')
                ->references('id')
                ->on('ecm_categories')
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
        Schema::dropIfExists('ecm_category_product');
    }
};
