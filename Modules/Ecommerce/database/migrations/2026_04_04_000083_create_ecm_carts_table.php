<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecm_carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('customer_id')->nullable()->index();
            $table->string('status', 30)->default('ACTIVE')->index(); // ACTIVE, CHECKED_OUT, ABANDONED
            $table->string('currency', 3)->default('VND')->index();
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('total', 18, 2)->default(0);
            $table->json('meta')->nullable();
            $table->dateTime('expires_at')->nullable()->index();
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
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecm_carts');
    }
};
