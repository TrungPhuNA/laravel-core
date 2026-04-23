<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wh_webhook_requests', function (Blueprint $table) {
            $table->json('mapped_payload')->nullable()->after('body');
        });
    }

    public function down(): void
    {
        Schema::table('wh_webhook_requests', function (Blueprint $table) {
            $table->dropColumn('mapped_payload');
        });
    }
};
