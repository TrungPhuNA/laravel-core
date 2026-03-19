<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wh_webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('name', 150);
            $table->uuid('public_id')->unique(); // ID public de nhan request tu ben ngoai

            $table->boolean('is_active')->default(true);
            $table->json('allowed_methods')->nullable(); // ["GET","POST"] (null = mac dinh ca 2)

            // auth_type:
            // - none  : khong can auth
            // - token : header X-Webhook-Token hoac query token=...
            $table->string('auth_type', 20)->default('none');
            $table->string('auth_token_hash', 255)->nullable();

            // Dung cho auth_type=hmac (luu secret dang encrypt de co the verify signature).
            $table->text('auth_secret_encrypted')->nullable();

            // Luu rule validate cho payload nhan vao (Laravel validation rules).
            // Example: {"email":"required|email","amount":"nullable|numeric"}
            $table->json('validation_rules')->nullable();

            $table->string('description', 255)->nullable();
            $table->timestamp('last_received_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wh_webhooks');
    }
};

