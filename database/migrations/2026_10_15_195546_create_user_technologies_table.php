<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_technologies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('technology_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('skill_level', 30)->default('beginner');
            $table->unsignedSmallInteger('years_experience')->nullable();
            $table->boolean('is_primary')->default(false);

            $table->timestamps();

            $table->unique(['user_id', 'technology_id']);

            $table->index(['technology_id', 'skill_level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_technologies');
    }
};
