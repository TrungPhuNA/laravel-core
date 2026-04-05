<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite limitation: adding foreign keys via Schema::table may require DBAL.
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('ecm_order_items', function (Blueprint $table) {
            $table
                ->foreign('variant_id')
                ->references('id')
                ->on('ecm_product_variants')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('ecm_order_items', function (Blueprint $table) {
            $table->dropForeign(['variant_id']);
        });
    }
};

