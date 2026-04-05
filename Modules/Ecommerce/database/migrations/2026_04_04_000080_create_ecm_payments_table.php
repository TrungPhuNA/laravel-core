<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecm_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('order_id')->index();

            $table->string('method', 60)->index(); // COD, BANK_TRANSFER, ...
            $table->string('status', 30)->default('PENDING')->index(); // PENDING, PAID, FAILED, REFUNDED
            $table->decimal('amount', 18, 2)->default(0);
            $table->string('currency', 3)->default('VND');

            $table->string('provider', 80)->nullable()->index(); // VNPay, Stripe, ...
            $table->string('provider_ref', 120)->nullable()->index();
            $table->dateTime('paid_at')->nullable()->index();

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
        Schema::dropIfExists('ecm_payments');
    }
};
