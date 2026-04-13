<?php
/*
 * (c) trungphu.nhanthuan <trungphu.nhanthuan@gmail.com>
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('wh_webhook_requests', function (Blueprint $table) {
            // success: xu ly hop le, error: co loi say ra (auth, validate, logic)
            $table->string('status', 20)->default('success')->after('body');
            $table->string('error_type', 50)->nullable()->after('status');
            $table->text('error_message')->nullable()->after('error_type');
            
            $table->index(['webhook_id', 'status', 'received_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wh_webhook_requests', function (Blueprint $table) {
            $table->dropIndex(['webhook_id', 'status', 'received_at']);
            $table->dropColumn(['status', 'error_type', 'error_message']);
        });
    }
};
