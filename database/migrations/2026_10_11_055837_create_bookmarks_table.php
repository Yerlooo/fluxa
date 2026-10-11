<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title');
            $table->string('url', 2048);
            $table->text('description')->nullable();
            $table->string('category')->default('Personal');
            $table->boolean('is_favorite')->default(false);

            $table->timestamps();

            $table->index(['user_id', 'category']);
            $table->index(['user_id', 'is_favorite']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookmarks');
    }
};
