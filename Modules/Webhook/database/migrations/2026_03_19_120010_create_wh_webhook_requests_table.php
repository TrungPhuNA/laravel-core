<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wh_webhook_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webhook_id')->constrained('wh_webhooks')->cascadeOnDelete();

            $table->string('method', 10);
            $table->string('ip', 45)->nullable();
            $table->json('headers')->nullable();
            $table->json('query')->nullable();
            $table->longText('body')->nullable();

            $table->timestamp('received_at')->useCurrent();
            $table->timestamps();

            $table->index(['webhook_id', 'received_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wh_webhook_requests');
    }
};

