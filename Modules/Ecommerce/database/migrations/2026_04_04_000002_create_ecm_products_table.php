<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecm_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->string('sku', 80);
            $table->string('name', 255);
            $table->string('slug', 255);
            $table->text('description')->nullable();

            $table->decimal('price', 18, 2)->default(0);
            $table->decimal('compare_at_price', 18, 2)->nullable();
            $table->decimal('cost_price', 18, 2)->nullable();
            $table->string('currency', 3)->default('VND')->index();

            $table->integer('stock_qty')->default(0)->index();
            $table->boolean('track_inventory')->default(true)->index();
            $table->boolean('allow_backorder')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();

            $table->string('barcode', 80)->nullable()->index();
            $table->decimal('weight', 10, 3)->nullable();
            $table->decimal('length', 10, 3)->nullable();
            $table->decimal('width', 10, 3)->nullable();
            $table->decimal('height', 10, 3)->nullable();
            $table->json('meta')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['shop_id', 'sku']);
            $table->unique(['shop_id', 'slug']);

            $table
                ->foreign('shop_id')
                ->references('id')
                ->on('ecm_shops')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecm_products');
    }
};
