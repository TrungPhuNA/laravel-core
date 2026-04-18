<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cheat_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 255);
            $table->longText('body');
            $table->string('visibility', 20)->default('private'); // private|unlisted|public
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'updated_at']);
            $table->index(['visibility', 'published_at']);
        });

        Schema::create('cheat_sheet_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('slug', 120);
            $table->timestamps();

            $table->unique(['user_id', 'slug']);
            $table->index(['user_id', 'name']);
        });

        Schema::create('cheat_sheet_tag', function (Blueprint $table) {
            $table->foreignId('cheat_sheet_id')->constrained('cheat_sheets')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('cheat_sheet_tags')->cascadeOnDelete();

            $table->primary(['cheat_sheet_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cheat_sheet_tag');
        Schema::dropIfExists('cheat_sheet_tags');
        Schema::dropIfExists('cheat_sheets');
    }
};

