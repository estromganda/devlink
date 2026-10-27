<?php

namespace App\Http\Controllers;

use App\Models\GitConnection;
use App\Models\Project;
use App\Models\ProjectRepository;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class ProjectRepositoryCommitController extends Controller
{
    public function index(Request $request, string $slug, ProjectRepository $repository): Response|RedirectResponse
    {
        // Seul le propriétaire du projet peut consulter cet historique
        // dans cette première version.
        $project = Project::query()
            ->where('slug', $slug)
            ->where('owner_id', $request->user()->id)
            ->firstOrFail();

        // Vérifier que le dépôt appartient au projet demandé.
        abort_unless(
            (int) $repository->project_id === (int) $project->id
                && $repository->is_active
                && $repository->provider === 'github',
            404
        );

        // Récupérer la connexion GitHub associée au dépôt.
        $connection = GitConnection::query()
            ->whereKey($repository->git_connection_id)
            ->where('provider', 'github')
            ->first();

        if (! $connection || ! $connection->access_token) {
            return to_route('projects.show', $project->slug)
                ->with(
                    'error',
                    'Aucune connexion GitHub valide n’est disponible pour ce dépôt.'
                );
        }

        $page = max(1, min((int) $request->query('page', 1), 10000));
        $perPage = 20;

        try {
            $response = Http::withToken($connection->access_token)
                ->acceptJson()
                ->withHeaders([
                    'X-GitHub-Api-Version' => '2022-11-28',
                ])
                ->timeout(15)
                ->get(
                    'https://api.github.com/repositories/'
                        . urlencode($repository->external_id)
                        . '/commits',
                    [
                        'per_page' => $perPage,
                        'page' => $page,
                    ]
                );

            if ($response->failed()) {
                Log::warning('GitHub commits request failed.', [
                    'project_id' => $project->id,
                    'repository_id' => $repository->id,
                    'http_status' => $response->status(),
                ]);

                $message = match ($response->status()) {
                    401 => 'La connexion GitHub n’est plus valide. Reconnecte le compte associé au dépôt.',
                    403 => 'GitHub refuse l’accès à cet historique. Vérifie les autorisations du compte connecté ou réessaie plus tard.',
                    404 => 'Ce dépôt ou son historique est introuvable ou inaccessible.',
                    409 => 'Ce dépôt ne contient pas encore d’historique de commits consultable.',
                    default => 'Impossible de récupérer les commits GitHub pour le moment.',
                };

                return to_route('projects.show', $project->slug)
                    ->with('error', $message);
            }

            $data = $response->json();

            if (! is_array($data)) {
                return to_route('projects.show', $project->slug)
                    ->with(
                        'error',
                        'GitHub a retourné une réponse inattendue.'
                    );
            }

            $commits = collect($data)
                ->filter(fn($commit) => is_array($commit))
                ->map(function (array $item): array {
                    $commit = $item['commit'] ?? [];
                    $author = $commit['author'] ?? [];
                    $githubAuthor = $item['author'] ?? null;

                    return [
                        'sha' => $item['sha'] ?? '',
                        'short_sha' => substr(
                            (string) ($item['sha'] ?? ''),
                            0,
                            7
                        ),
                        'message' => trim(
                            (string) ($commit['message'] ?? 'Commit sans message')
                        ),
                        'author_name' => $author['name'] ?? 'Auteur inconnu',
                        'author_login' => $githubAuthor['login'] ?? null,
                        'author_avatar' => $githubAuthor['avatar_url'] ?? null,
                        'committed_at' => $author['date'] ?? null,
                        'html_url' => $item['html_url'] ?? null,
                    ];
                })
                ->values();

            // Déterminer si GitHub indique une page suivante.
            $linkHeader = $response->header('Link');

            $hasNextPage = is_string($linkHeader)
                && preg_match(
                    '/;\s*rel\s*=\s*"?next"?\s*(?:,|$)/i',
                    $linkHeader
                ) === 1;

            // Si la page contient 20 commits, il peut exister
            // une page suivante même si l'en-tête Link est absent ou inexploitable.
            if (! $hasNextPage && $commits->count() === $perPage) {
                $hasNextPage = true;
            }

            $hasPreviousPage = $page > 1;

            return Inertia::render('github/Commits', [
                'project' => [
                    'id' => $project->id,
                    'name' => $project->name,
                    'slug' => $project->slug,
                ],
                'repository' => [
                    'id' => $repository->id,
                    'full_name' => $repository->full_name,
                    'repository_url' => $repository->repository_url,
                    'default_branch' => $repository->default_branch,
                ],
                'commits' => $commits,
                'pagination' => [
                    'current_page' => $page,
                    'has_next_page' => $hasNextPage,
                    'has_previous_page' => $hasPreviousPage,
                ],
            ]);
        } catch (ConnectionException $exception) {
            Log::warning('GitHub commits connection error.', [
                'project_id' => $project->id,
                'repository_id' => $repository->id,
            ]);

            return to_route('projects.show', $project->slug)
                ->with(
                    'error',
                    'Impossible de joindre GitHub. Vérifie la connexion et réessaie.'
                );
        }
    }
}
