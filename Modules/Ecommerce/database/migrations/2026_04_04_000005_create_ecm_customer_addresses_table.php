<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecm_customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('customer_id')->index();

            $table->string('label', 120)->nullable();
            $table->string('name', 200)->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('line1', 255);
            $table->string('line2', 255)->nullable();
            $table->string('city', 120)->nullable();
            $table->string('state', 120)->nullable();
            $table->string('postal_code', 40)->nullable();
            $table->string('country', 2)->default('VN');
            $table->boolean('is_default_shipping')->default(false)->index();
            $table->boolean('is_default_billing')->default(false)->index();

            $table->timestamps();

            $table
                ->foreign('shop_id')
                ->references('id')
                ->on('ecm_shops')
                ->cascadeOnDelete();

            $table
                ->foreign('customer_id')
                ->references('id')
                ->on('ecm_customers')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecm_customer_addresses');
    }
};
