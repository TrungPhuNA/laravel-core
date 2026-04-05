<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecm_inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('product_id')->nullable()->index();
            $table->unsignedBigInteger('variant_id')->nullable()->index();

            $table->string('type', 30)->index(); // ADJUST, ORDER, RETURN, IMPORT, ...
            $table->integer('quantity_delta'); // +10 / -2
            $table->string('reason', 120)->nullable();
            $table->unsignedBigInteger('ref_id')->nullable()->index(); // order_id / ...
            $table->string('ref_type', 60)->nullable()->index(); // ecm_orders / ...
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->json('meta')->nullable();

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
        Schema::dropIfExists('ecm_inventory_movements');
    }
};
