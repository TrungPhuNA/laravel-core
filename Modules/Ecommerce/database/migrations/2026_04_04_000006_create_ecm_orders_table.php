<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecm_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->string('code', 32);
            $table->unsignedBigInteger('customer_id')->nullable()->index();

            $table->string('status', 30)->default('NEW')->index();
            $table->string('payment_status', 30)->default('UNPAID')->index();
            $table->string('fulfillment_status', 30)->default('UNFULFILLED')->index();

            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('discount_total', 18, 2)->default(0);
            $table->decimal('tax_total', 18, 2)->default(0);
            $table->decimal('shipping_total', 18, 2)->default(0);
            $table->decimal('total', 18, 2)->default(0);
            $table->string('currency', 3)->default('VND')->index();

            $table->string('customer_name', 200)->nullable();
            $table->string('customer_email', 200)->nullable()->index();
            $table->string('customer_phone', 40)->nullable()->index();
            $table->json('customer_snapshot')->nullable();
            $table->json('shipping_address')->nullable();
            $table->json('billing_address')->nullable();

            $table->string('payment_method', 60)->nullable()->index();
            $table->string('shipping_method', 80)->nullable()->index();
            $table->string('shipping_provider', 80)->nullable()->index();
            $table->string('tracking_number', 120)->nullable()->index();
            $table->string('note', 255)->nullable();
            $table->json('meta')->nullable();

            $table->dateTime('placed_at')->nullable()->index();
            $table->dateTime('paid_at')->nullable()->index();
            $table->dateTime('cancelled_at')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['shop_id', 'code']);

            $table
                ->foreign('shop_id')
                ->references('id')
                ->on('ecm_shops')
                ->cascadeOnDelete();

            $table
                ->foreign('customer_id')
                ->references('id')
                ->on('ecm_customers')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecm_orders');
    }
};
