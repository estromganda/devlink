<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_repositories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('git_connection_id')
                ->nullable()
                ->constrained('git_connections')
                ->nullOnDelete();

            $table->foreignId('connected_by')
                ->constrained('users')
                ->restrictOnDelete();

            // github ou gitlab
            $table->string('provider', 20);

            // Identifiant du dépôt chez le fournisseur.
            $table->string('external_id', 100);

            // Exemple : organisation/mon-projet
            $table->string('full_name', 255);

            $table->string('name', 255);
            $table->text('description')->nullable();

            $table->string('repository_url', 2048);
            $table->string('default_branch', 255)->nullable();

            $table->boolean('is_private')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamp('last_synced_at')->nullable();

            $table->timestamps();

            // Un même dépôt externe ne doit pas être associé
            // plusieurs fois au même projet.
            $table->unique(
                ['project_id', 'provider', 'external_id'],
                'project_repositories_external_unique'
            );

            $table->index(['project_id', 'is_active']);
            $table->index(['provider', 'external_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_repositories');
    }
};
