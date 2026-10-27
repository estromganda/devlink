<?php

use App\Models\GitConnection;
use App\Models\Project;
use App\Models\ProjectRepository;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->connection = GitConnection::create([
        'user_id' => $this->user->id,
        'provider' => 'github',
        'provider_user_id' => 'github-user-123',
        'username' => 'devlink-user',
        'email' => $this->user->email,
        'access_token' => 'fake-github-token',
        'scopes' => ['read:user', 'user:email', 'repo'],
        'connected_at' => now(),
    ]);

    $this->project = Project::create([
        'owner_id' => $this->user->id,
        'name' => 'Commit Test Project',
        'slug' => 'commit-test-project',
        'description' => 'Project for commit tests',
        'short_description' => 'Commit tests',
        'status' => 'in_progress',
        'visibility' => 'public',
        'looking_for_collaborators' => false,
    ]);

    $this->repository = ProjectRepository::create([
        'project_id' => $this->project->id,
        'git_connection_id' => $this->connection->id,
        'connected_by' => $this->user->id,
        'provider' => 'github',
        'external_id' => '98765',
        'full_name' => 'devlink-user/test-repository',
        'name' => 'test-repository',
        'description' => 'Repository for testing',
        'repository_url' => 'https://github.com/devlink-user/test-repository',
        'default_branch' => 'main',
        'is_private' => false,
        'is_active' => true,
    ]);
});

/**
 * Simule un commit renvoyé par l'API GitHub.
 */
function githubCommitPayload(array $overrides = []): array
{
    return array_replace_recursive([
        [
            'sha' => 'abcdef1234567890abcdef1234567890abcdef12',
            'html_url' => 'https://github.com/devlink-user/test-repository/commit/abcdef1234567890abcdef1234567890abcdef12',
            'commit' => [
                'message' => 'Add user authentication',
                'author' => [
                    'name' => 'DevLink Developer',
                    'date' => '2026-10-20T10:00:00Z',
                ],
            ],
            'author' => [
                'login' => 'devlink-user',
                'avatar_url' => 'https://avatars.githubusercontent.com/u/12345?v=4',
            ],
        ],
    ], $overrides);
}

it('redirects guests to login when they try to view commits', function () {
    $this->get(route('projects.repositories.commits', [
        'slug' => $this->project->slug,
        'repository' => $this->repository->id,
    ]))->assertRedirect(route('login'));
});

it('displays commits from GitHub for the project owner', function () {
    Http::fake([
        'api.github.com/repositories/98765/commits*' => Http::response(
            githubCommitPayload(),
            200,
        ),
    ]);

    $this->actingAs($this->user)
        ->get(route('projects.repositories.commits', [
            'slug' => $this->project->slug,
            'repository' => $this->repository->id,
        ]))
        ->assertOk()
        ->assertInertia(
            fn(Assert $page) => $page
                ->component('github/Commits')
                ->where('project.slug', 'commit-test-project')
                ->where('repository.full_name', 'devlink-user/test-repository')
                ->has('commits', 1)
                ->where('commits.0.message', 'Add user authentication')
                ->where('commits.0.author_name', 'DevLink Developer')
                ->where('commits.0.author_login', 'devlink-user')
                ->where('commits.0.short_sha', 'abcdef1')
                ->where('pagination.current_page', 1)
                ->where('pagination.has_previous_page', false)
        );

    Http::assertSent(
        fn($request) =>
        str_contains($request->url(), '/repositories/98765/commits')
            && $request->hasHeader('Authorization', 'Bearer fake-github-token')
    );
});

it('requests the requested page of commits from GitHub', function () {
    Http::fake([
        'api.github.com/repositories/98765/commits*' => Http::response(
            githubCommitPayload(),
            200,
        ),
    ]);

    $this->actingAs($this->user)
        ->get(route('projects.repositories.commits', [
            'slug' => $this->project->slug,
            'repository' => $this->repository->id,
        ]) . '?page=2')
        ->assertOk()
        ->assertInertia(
            fn(Assert $page) => $page
                ->component('github/Commits')
                ->where('pagination.current_page', 2)
                ->where('pagination.has_previous_page', true)
        );

    Http::assertSent(
        fn($request) =>
        str_contains($request->url(), 'page=2')
    );
});

it('detects the next page when GitHub returns a pagination link', function () {
    Http::fake([
        'api.github.com/repositories/98765/commits*' => Http::response(
            githubCommitPayload(),
            200,
            [
                'Link' => '<https://api.github.com/repositories/98765/commits?per_page=20&page=2>; rel="next"',
            ],
        ),
    ]);

    $this->actingAs($this->user)
        ->get(route('projects.repositories.commits', [
            'slug' => $this->project->slug,
            'repository' => $this->repository->id,
        ]))
        ->assertOk()
        ->assertInertia(
            fn(Assert $page) => $page
                ->component('github/Commits')
                ->where('pagination.has_next_page', true)
        );
});

it('returns not found when the repository belongs to another project', function () {
    $anotherProject = Project::create([
        'owner_id' => $this->user->id,
        'name' => 'Another Project',
        'slug' => 'another-commit-project',
        'description' => 'Another project',
        'short_description' => 'Another project',
        'status' => 'in_progress',
        'visibility' => 'public',
        'looking_for_collaborators' => false,
    ]);

    $this->actingAs($this->user)
        ->get(route('projects.repositories.commits', [
            'slug' => $anotherProject->slug,
            'repository' => $this->repository->id,
        ]))
        ->assertNotFound();

    Http::assertNothingSent();
});

it('returns not found when a different user tries to view the project commits', function () {
    $anotherUser = User::factory()->create();

    $this->actingAs($anotherUser)
        ->get(route('projects.repositories.commits', [
            'slug' => $this->project->slug,
            'repository' => $this->repository->id,
        ]))
        ->assertNotFound();

    Http::assertNothingSent();
});

it('returns not found for an inactive repository', function () {
    $this->repository->update([
        'is_active' => false,
    ]);

    $this->actingAs($this->user)
        ->get(route('projects.repositories.commits', [
            'slug' => $this->project->slug,
            'repository' => $this->repository->id,
        ]))
        ->assertNotFound();

    Http::assertNothingSent();
});

it('returns not found for a repository from an unsupported provider', function () {
    $this->repository->update([
        'provider' => 'gitlab',
    ]);

    $this->actingAs($this->user)
        ->get(route('projects.repositories.commits', [
            'slug' => $this->project->slug,
            'repository' => $this->repository->id,
        ]))
        ->assertNotFound();

    Http::assertNothingSent();
});

it('handles an invalid GitHub token when loading commits', function () {
    Http::fake([
        'api.github.com/repositories/98765/commits*' => Http::response([
            'message' => 'Bad credentials',
        ], 401),
    ]);

    $this->actingAs($this->user)
        ->get(route('projects.repositories.commits', [
            'slug' => $this->project->slug,
            'repository' => $this->repository->id,
        ]))
        ->assertRedirect();

    Http::assertSent(
        fn($request) =>
        str_contains($request->url(), '/repositories/98765/commits')
    );
});

it('handles a repository that GitHub can no longer find', function () {
    Http::fake([
        'api.github.com/repositories/98765/commits*' => Http::response([
            'message' => 'Not Found',
        ], 404),
    ]);

    $this->actingAs($this->user)
        ->get(route('projects.repositories.commits', [
            'slug' => $this->project->slug,
            'repository' => $this->repository->id,
        ]))
        ->assertRedirect();
});

it('handles GitHub API server errors when loading commits', function () {
    Http::fake([
        'api.github.com/repositories/98765/commits*' => Http::response([
            'message' => 'Internal Server Error',
        ], 500),
    ]);

    $this->actingAs($this->user)
        ->get(route('projects.repositories.commits', [
            'slug' => $this->project->slug,
            'repository' => $this->repository->id,
        ]))
        ->assertRedirect();
});

it('handles network failures when loading commits', function () {
    Http::fake([
        'api.github.com/repositories/98765/commits*' => function () {
            throw new ConnectionException('Simulated network failure.');
        },
    ]);

    $this->actingAs($this->user)
        ->get(route('projects.repositories.commits', [
            'slug' => $this->project->slug,
            'repository' => $this->repository->id,
        ]))
        ->assertRedirect();
});
