<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('git_connections', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('provider', 20);
            $table->string('provider_user_id', 100);
            $table->string('username', 255);
            $table->string('email')->nullable();
            $table->string('avatar_url', 2048)->nullable();

            // Ces champs seront chiffrés par le modèle.
            $table->text('access_token');
            $table->text('refresh_token')->nullable();

            $table->timestamp('token_expires_at')->nullable();
            $table->json('scopes')->nullable();

            $table->timestamp('connected_at')->nullable();

            $table->timestamps();

            $table->unique(
                ['user_id', 'provider', 'provider_user_id'],
                'git_connections_identity_unique'
            );

            $table->index(['user_id', 'provider']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('git_connections');
    }
};
