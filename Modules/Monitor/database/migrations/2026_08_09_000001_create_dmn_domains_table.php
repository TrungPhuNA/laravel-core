<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dmn_domains', function (Blueprint $table) {
            $table->id();
            $table->string('domain')->unique();
            $table->text('note')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->string('registrar')->nullable();
            $table->json('nameservers')->nullable();
            $table->string('check_status')->default('unknown'); // unknown|ok|error
            $table->timestamp('last_check_at')->nullable();
            $table->text('last_check_error')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dmn_domains');
    }
};