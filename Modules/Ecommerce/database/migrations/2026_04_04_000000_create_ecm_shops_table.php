<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecm_shops', function (Blueprint $table) {
            $table->id();
            $table->string('code', 60)->unique();
            $table->string('name', 200);
            $table->string('domain', 200)->nullable()->unique();
            $table->string('timezone', 60)->default('Asia/Ho_Chi_Minh');
            $table->string('currency', 3)->default('VND');
            $table->boolean('is_active')->default(true)->index();
            $table->json('meta')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ecm_shop_users', function (Blueprint $table) {
            $table->unsignedBigInteger('shop_id');
            $table->unsignedBigInteger('user_id');
            $table->string('role', 40)->default('STAFF')->index();
            $table->timestamps();

            $table->primary(['shop_id', 'user_id']);
            $table->index(['user_id', 'shop_id']);

            $table
                ->foreign('shop_id')
                ->references('id')
                ->on('ecm_shops')
                ->cascadeOnDelete();

            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecm_shop_users');
        Schema::dropIfExists('ecm_shops');
    }
};

