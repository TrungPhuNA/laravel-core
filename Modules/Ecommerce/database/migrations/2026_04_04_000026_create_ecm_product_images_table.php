<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecm_product_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('variant_id')->nullable()->index();

            $table->string('url', 600);
            $table->string('alt', 255)->nullable();
            $table->unsignedInteger('position')->default(0)->index();
            $table->boolean('is_primary')->default(false)->index();

            $table->timestamps();

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

            $table
                ->foreign('variant_id')
                ->references('id')
                ->on('ecm_product_variants')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecm_product_images');
    }
};
