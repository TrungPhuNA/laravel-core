<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecm_shipments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('order_id')->index();

            $table->string('status', 30)->default('PENDING')->index(); // PENDING, SHIPPED, DELIVERED, RETURNED
            $table->string('provider', 80)->nullable()->index();
            $table->string('service', 80)->nullable();
            $table->string('tracking_number', 120)->nullable()->index();

            $table->dateTime('shipped_at')->nullable()->index();
            $table->dateTime('delivered_at')->nullable()->index();

            $table->json('meta')->nullable();
            $table->timestamps();

            $table
                ->foreign('shop_id')
                ->references('id')
                ->on('ecm_shops')
                ->cascadeOnDelete();

            $table
                ->foreign('order_id')
                ->references('id')
                ->on('ecm_orders')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecm_shipments');
    }
};
