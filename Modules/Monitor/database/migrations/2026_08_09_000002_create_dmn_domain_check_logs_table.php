<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dmn_domain_check_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('domain_id')->constrained('dmn_domains')->cascadeOnDelete();
            $table->string('status'); // ok|error
            $table->timestamp('expires_at_found')->nullable();
            $table->string('registrar')->nullable();
            $table->string('method')->nullable(); // rdap|whois|third_party
            $table->text('error_message')->nullable();
            $table->longText('raw_response')->nullable();
            $table->timestamp('checked_at');
            $table->timestamps();

            $table->index(['domain_id', 'checked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dmn_domain_check_logs');
    }
};