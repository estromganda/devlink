<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_task_activities', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_task_id')
                ->constrained('project_tasks')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('action', 256);

            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            $table->text('description')->nullable();

            $table->timestamps();

            $table->index(['project_task_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_task_activities');
    }
};
