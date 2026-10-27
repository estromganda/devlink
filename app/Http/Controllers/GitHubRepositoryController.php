<?php

namespace App\Http\Controllers;

use App\Models\GitConnection;
use App\Models\Project;
use App\Models\ProjectRepository;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class GitHubRepositoryController extends Controller
{
    /**
     * Display repositories accessible to the connected GitHub account.
     */
    public function index(Request $request): Response|\Illuminate\Http\RedirectResponse
    {
        $connection = GitConnection::query()
            ->where('user_id', $request->user()->id)
            ->where('provider', 'github')
            ->first();

        if (! $connection) {
            return redirect()
                ->route('auth.github.redirect')
                ->with('error', 'Connecte d’abord ton compte GitHub.');
        }

        try {
            $repositories = collect();
            $page = 1;

            do {
                $response = Http::withToken($connection->access_token)
                    ->acceptJson()
                    ->withHeaders([
                        'X-GitHub-Api-Version' => '2022-11-28',
                        'User-Agent' => 'DevLink',
                    ])
                    ->timeout(15)
                    ->get('https://api.github.com/user/repos', [
                        'visibility' => 'all',
                        'affiliation' => 'owner,collaborator,organization_member',
                        'sort' => 'updated',
                        'per_page' => 100,
                        'page' => $page,
                    ]);

                if (! $response->successful()) {
                    report(new \RuntimeException(
                        'GitHub repository API returned HTTP ' . $response->status()
                    ));

                    return redirect()
                        ->route('dashboard')
                        ->with(
                            'error',
                            'Impossible de récupérer les dépôts GitHub. Vérifie les autorisations de connexion.'
                        );
                }

                $batch = collect($response->json());

                $repositories = $repositories->concat(
                    $batch->map(fn(array $repository) => [
                        'external_id' => (string) $repository['id'],
                        'full_name' => $repository['full_name'],
                        'name' => $repository['name'],
                        'description' => $repository['description'],
                        'repository_url' => $repository['html_url'],
                        'default_branch' => $repository['default_branch'],
                        'is_private' => $repository['private'],
                        'owner' => $repository['owner']['login'] ?? null,
                        'updated_at' => $repository['updated_at'] ?? null,
                    ])
                );

                $page++;
            } while ($batch->isNotEmpty() && $batch->count() === 100);

            $projects = Project::query()
                ->where('owner_id', $request->user()->id)
                ->orderBy('name')
                ->get(['id', 'name', 'slug']);

            $repositoryIds = $repositories
                ->pluck('external_id')
                ->all();

            $associations = ProjectRepository::query()
                ->where('provider', 'github')
                ->whereIn('external_id', $repositoryIds)
                ->whereHas('project', function ($query) use ($request) {
                    $query->where('owner_id', $request->user()->id);
                })
                ->get(['project_id', 'external_id'])
                ->keyBy('external_id');

            $repositories = $repositories->map(function ($repository) use ($associations) {
                $association = $associations->get($repository['external_id']);
                $repository['associated_project_id'] = $association?->project_id;
                return $repository;
            });

            return Inertia::render('github/Repositories', [
                'repositories' => $repositories->values(),
                'githubUsername' => $connection->username,
                'projects' => $projects,
            ]);
        } catch (ConnectionException $exception) {
            report($exception);

            return redirect()
                ->route('dashboard')
                ->with('error', 'GitHub est momentanément inaccessible. Réessaie plus tard.');
        }
    }

    /**
     * Associate an accessible GitHub repository with a DevLink project.
     */
    public function associate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'external_id' => ['required', 'string', 'max:255'],
        ]);

        $project = Project::query()
            ->whereKey($validated['project_id'])
            ->where('owner_id', $request->user()->id)
            ->firstOrFail();

        $connection = GitConnection::query()
            ->where('user_id', $request->user()->id)
            ->where('provider', 'github')
            ->firstOrFail();

        $response = Http::withToken($connection->access_token)
            ->acceptJson()
            ->withHeaders([
                'X-GitHub-Api-Version' => '2022-11-28',
                'User-Agent' => 'DevLink',
            ])
            ->timeout(15)
            ->get(
                'https://api.github.com/repositories/'
                    . rawurlencode($validated['external_id'])
            );

        if (! $response->successful()) {
            return to_route('github.repositories.index')
                ->with('error', 'Impossible de vérifier ce dépôt auprès de GitHub.');
        }

        $repository = $response->json();

        if (
            ! isset(
                $repository['id'],
                $repository['full_name'],
                $repository['name'],
                $repository['html_url']
            )
            || (string) $repository['id'] !== $validated['external_id']
        ) {
            return back()->with(
                'error',
                'GitHub a renvoyé des informations de dépôt invalides.'
            );
        }

        try {
            DB::transaction(function () use (
                $project,
                $connection,
                $request,
                $repository
            ) {
                ProjectRepository::updateOrCreate(
                    [
                        'project_id' => $project->id,
                        'provider' => 'github',
                        'external_id' => (string) $repository['id'],
                    ],
                    [
                        'git_connection_id' => $connection->id,
                        'connected_by' => $request->user()->id,
                        'full_name' => $repository['full_name'],
                        'name' => $repository['name'],
                        'description' => $repository['description'] ?? null,
                        'repository_url' => $repository['html_url'],
                        'default_branch' => $repository['default_branch'] ?? null,
                        'is_private' => (bool) $repository['private'],
                        'is_active' => true,
                        'last_synced_at' => now(),
                    ]
                );
            });
        } catch (\Throwable $exception) {
            report($exception);

            return back()->with(
                'error',
                'Une erreur est survenue lors de l’association du dépôt.'
            );
        }

        return to_route('github.repositories.index')
            ->with('success', 'Le dépôt GitHub a été associé au projet DevLink.');
    }

    /**
     * Synchronise les information du dépot
     */
    public function sync(Request $request, string $slug, ProjectRepository $repository): RedirectResponse
    {
        // Seul le propriétaire du projet peut synchroniser ses dépôts.
        $project = Project::query()
            ->where('slug', $slug)
            ->where('owner_id', $request->user()->id)
            ->firstOrFail();

        // Vérifier que le dépôt appartient bien à ce projet.
        abort_unless(
            (int) $repository->project_id === (int) $project->id,
            404
        );

        // Récupérer la connexion GitHub utilisée pour associer le dépôt.
        $connection = GitConnection::query()
            ->whereKey($repository->git_connection_id)
            ->where('user_id', $request->user()->id)
            ->where('provider', 'github')
            ->first();

        if (! $connection || ! $connection->access_token) {
            return to_route('projects.show', $project->slug)
                ->with(
                    'error',
                    'Aucune connexion GitHub valide n’est associée à ce dépôt. Reconnecte ton compte GitHub.'
                );
        }

        try {
            // L'identifiant externe permet de récupérer le dépôt
            // même si son nom ou son propriétaire a changé.
            $response = Http::withToken($connection->access_token)
                ->acceptJson()
                ->withHeaders([
                    'X-GitHub-Api-Version' => '2022-11-28',
                ])
                ->timeout(15)
                ->get(
                    'https://api.github.com/repositories/'
                        . urlencode($repository->external_id)
                );

            if ($response->failed()) {
                Log::warning('GitHub repository sync failed.', [
                    'project_id' => $project->id,
                    'repository_id' => $repository->id,
                    'http_status' => $response->status(),
                ]);

                if ($response->status() === 401) {
                    return to_route('projects.show', $project->slug)
                        ->with(
                            'error',
                            'La connexion GitHub a expiré ou n’est plus valide. Reconnecte ton compte.'
                        );
                }

                if ($response->status() === 404) {
                    return to_route('projects.show', $project->slug)
                        ->with(
                            'error',
                            'Ce dépôt est introuvable ou n’est plus accessible avec cette connexion GitHub.'
                        );
                }

                return to_route('projects.show', $project->slug)
                    ->with(
                        'error',
                        'GitHub n’a pas pu fournir les informations du dépôt. Réessaie plus tard.'
                    );
            }

            $data = $response->json();

            if (
                ! is_array($data)
                || ! isset(
                    $data['id'],
                    $data['name'],
                    $data['full_name'],
                    $data['html_url']
                )
                || (string) $data['id'] !== (string) $repository->external_id
            ) {
                return to_route('projects.show', $project->slug)
                    ->with(
                        'error',
                        'La réponse de GitHub est invalide. Aucune modification n’a été effectuée.'
                    );
            }

            // Actualiser uniquement les métadonnées du dépôt.
            $repository->update([
                'full_name' => $data['full_name'],
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'repository_url' => $data['html_url'],
                'default_branch' => $data['default_branch'] ?? null,
                'is_private' => (bool) ($data['private'] ?? false),
                'last_synced_at' => now(),
            ]);

            return to_route('projects.show', $project->slug)
                ->with(
                    'success',
                    'Les informations du dépôt GitHub ont été synchronisées.'
                );
        } catch (\Illuminate\Http\Client\ConnectionException $exception) {
            Log::warning('GitHub repository sync connection error.', [
                'project_id' => $project->id,
                'repository_id' => $repository->id,
            ]);

            return to_route('projects.show', $project->slug)
                ->with(
                    'error',
                    'Impossible de joindre GitHub pour le moment. Vérifie ta connexion et réessaie.'
                );
        } catch (\Throwable $exception) {
            Log::error('Unexpected GitHub repository sync error.', [
                'project_id' => $project->id,
                'repository_id' => $repository->id,
                'exception' => $exception::class,
            ]);

            return to_route('projects.show', $project->slug)
                ->with(
                    'error',
                    'Une erreur inattendue est survenue pendant la synchronisation.'
                );
        }
    }
}
