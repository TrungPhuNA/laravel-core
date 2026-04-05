<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecm_order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('order_id')->index();
            $table->string('from_status', 30)->nullable()->index();
            $table->string('to_status', 30)->index();
            $table->string('note', 255)->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->json('meta')->nullable();
            $table->dateTime('created_at')->useCurrent()->index();

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
        Schema::dropIfExists('ecm_order_status_histories');
    }
};
