<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wh_webhook_dispatch_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webhook_id')->constrained('wh_webhooks')->cascadeOnDelete();
            $table->foreignId('webhook_request_id')->constrained('wh_webhook_requests')->cascadeOnDelete();
            $table->foreignId('destination_id')->constrained('wh_webhook_destinations')->cascadeOnDelete();

            // pending|success|failed
            $table->string('status', 20)->default('pending');

            $table->timestamp('dispatched_at')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();

            // What we sent.
            $table->longText('request_body')->nullable();

            // What we received.
            $table->unsignedSmallInteger('response_status')->nullable();
            $table->json('response_headers')->nullable();
            $table->longText('response_body')->nullable();

            // In case of error.
            $table->string('error_type', 50)->nullable();
            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->index(['webhook_id', 'created_at']);
            $table->index(['webhook_id', 'status', 'created_at']);
            $table->index(['destination_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wh_webhook_dispatch_logs');
    }
};

