<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wh_webhook_destinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webhook_id')->constrained('wh_webhooks')->cascadeOnDelete();

            $table->string('name', 150);
            $table->string('url', 2048);
            $table->string('http_method', 10)->default('POST');
            $table->boolean('is_active')->default(true);

            // Extra headers to send to destination. Example: {"Authorization":"Bearer ..."}
            $table->json('headers')->nullable();

            // Payload transform settings.
            // send_mode:
            // - merge       : send full payload + mapped fields (override)
            // - mapped_only : only send mapped fields
            $table->string('send_mode', 20)->default('merge');
            // List of mapping items: [{"from":"username","to":"u_username"}]
            $table->json('field_mappings')->nullable();
            $table->boolean('drop_mapped_sources')->default(false);

            $table->unsignedSmallInteger('timeout_seconds')->default(10);

            $table->timestamps();

            $table->index(['webhook_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wh_webhook_destinations');
    }
};

