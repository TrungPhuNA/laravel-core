<?php

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
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_type', 20)->default('USER')->index();

            // Profile fields (keep in users table for MVP)
            $table->string('phone', 30)->nullable();
            $table->string('avatar_url')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 20)->nullable();

            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('ward')->nullable();
            $table->string('district')->nullable();
            $table->string('province')->nullable();
            $table->string('country', 2)->nullable();
            $table->string('postal_code', 20)->nullable();

            $table->string('company')->nullable();
            $table->string('job_title')->nullable();
            $table->string('timezone', 50)->nullable();
            $table->string('locale', 10)->nullable();
            $table->text('bio')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'user_type',
                'phone',
                'avatar_url',
                'date_of_birth',
                'gender',
                'address_line1',
                'address_line2',
                'ward',
                'district',
                'province',
                'country',
                'postal_code',
                'company',
                'job_title',
                'timezone',
                'locale',
                'bio',
            ]);
        });
    }
};
