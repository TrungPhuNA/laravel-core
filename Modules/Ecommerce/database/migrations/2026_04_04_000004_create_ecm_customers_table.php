<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecm_customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->string('code', 40)->nullable();
            $table->string('name', 200)->nullable();
            $table->string('email', 200)->nullable();
            $table->string('phone', 40)->nullable()->index();
            $table->string('gender', 10)->nullable()->index();
            $table->date('birthday')->nullable()->index();
            $table->json('tags')->nullable();
            $table->string('note', 255)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['shop_id', 'code']);
            $table->unique(['shop_id', 'email']);

            $table
                ->foreign('shop_id')
                ->references('id')
                ->on('ecm_shops')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecm_customers');
    }
};
