<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('answer_votes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('answer_id')
                ->constrained('answers')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->smallInteger('value')->default(1);

            $table->timestamps();

            $table->unique(
                ['answer_id', 'user_id'],
                'answer_votes_answer_user_unique'
            );

            $table->index(['user_id', 'answer_id']);
        });
        // PostgreSQL accepte cette contrainte ; SQLite ne l'ajoute pas ainsi.
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement(
                'ALTER TABLE answer_votes
                ADD CONSTRAINT answer_votes_value_check
                CHECK (value IN (-1, 1))'
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('answer_votes');
    }
};
