<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->foreignId('owner_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('name', 150);
            $table->string('slug', 170)->unique();
            $table->text('description');
            $table->text('short_description')->nullable();

            $table->string('status', 30)->default('active');
            $table->string('visibility', 20)->default('public');

            $table->string('repository_url')->nullable();
            $table->string('demo_url')->nullable();
            $table->string('cover_path')->nullable();

            $table->boolean('looking_for_collaborators')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'visibility']);
            $table->index(['owner_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
