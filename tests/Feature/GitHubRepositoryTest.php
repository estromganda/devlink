<?php

use App\Models\GitConnection;
use App\Models\Project;
use App\Models\ProjectRepository;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('redirects guests to login when accessing GitHub repositories', function () {
    $this->get(route('github.repositories.index'))
        ->assertRedirect(route('login'));
});

it('redirects users without a GitHub connection to OAuth', function () {
    $this->actingAs($this->user)
        ->get(route('github.repositories.index'))
        ->assertRedirect(route('auth.github.redirect'));
});

it('displays repositories returned by GitHub', function () {
    GitConnection::create([
        'user_id' => $this->user->id,
        'provider' => 'github',
        'provider_user_id' => '12345',
        'username' => 'devlink-user',
        'email' => $this->user->email,
        'access_token' => 'fake-github-token',
        'scopes' => ['read:user', 'user:email', 'repo'],
        'connected_at' => now(),
    ]);

    Http::fake([
        'api.github.com/user/repos*' => Http::response([
            [
                'id' => 98765,
                'name' => 'example-repository',
                'full_name' => 'devlink-user/example-repository',
                'description' => 'Repository used for testing',
                'html_url' => 'https://github.com/devlink-user/example-repository',
                'default_branch' => 'main',
                'private' => false,
                'owner' => [
                    'login' => 'devlink-user',
                ],
                'updated_at' => now()->toISOString(),
            ],
        ], 200),
    ]);

    $this->actingAs($this->user)
        ->get(route('github.repositories.index'))
        ->assertOk()
        ->assertInertia(
            fn(Assert $page) => $page
                ->component('github/Repositories')
                ->has('repositories', 1)
                ->where(
                    'repositories.0.full_name',
                    'devlink-user/example-repository'
                )
        );
});

it('associates a GitHub repository with a project', function () {
    $connection = GitConnection::create([
        'user_id' => $this->user->id,
        'provider' => 'github',
        'provider_user_id' => '12345',
        'username' => 'devlink-user',
        'email' => $this->user->email,
        'access_token' => 'fake-github-token',
        'scopes' => ['read:user', 'user:email', 'repo'],
        'connected_at' => now(),
    ]);

    $project = Project::create([
        'owner_id' => $this->user->id,
        'name' => 'Test Project',
        'slug' => 'test-project',
        'description' => 'Project used for automated tests',
        'short_description' => 'Test project',
        'status' => 'in_progress',
        'visibility' => 'public',
        'looking_for_collaborators' => false,
    ]);

    Http::fake([
        'api.github.com/repositories/98765' => Http::response([
            'id' => 98765,
            'name' => 'example-repository',
            'full_name' => 'devlink-user/example-repository',
            'description' => 'Repository used for testing',
            'html_url' => 'https://github.com/devlink-user/example-repository',
            'default_branch' => 'main',
            'private' => false,
        ], 200),
    ]);

    $this->actingAs($this->user)
        ->post(route('github.repositories.associate'), [
            'project_id' => $project->id,
            'external_id' => '98765',
        ])
        ->assertRedirect(route('github.repositories.index'));

    $this->assertDatabaseHas('project_repositories', [
        'project_id' => $project->id,
        'git_connection_id' => $connection->id,
        'provider' => 'github',
        'external_id' => '98765',
        'full_name' => 'devlink-user/example-repository',
        'repository_url' => 'https://github.com/devlink-user/example-repository',
        'is_active' => true,
    ]);
});

it('does not allow users to associate repositories with another users project', function () {
    GitConnection::create([
        'user_id' => $this->user->id,
        'provider' => 'github',
        'provider_user_id' => '12345',
        'username' => 'devlink-user',
        'email' => $this->user->email,
        'access_token' => 'fake-github-token',
        'scopes' => ['read:user', 'user:email', 'repo'],
        'connected_at' => now(),
    ]);

    $otherUser = User::factory()->create();

    $project = Project::create([
        'owner_id' => $otherUser->id,
        'name' => 'Other Project',
        'slug' => 'other-project',
        'description' => 'Another user project',
        'short_description' => 'Private test',
        'status' => 'in_progress',
        'visibility' => 'public',
        'looking_for_collaborators' => false,
    ]);

    $this->actingAs($this->user)
        ->post(route('github.repositories.associate'), [
            'project_id' => $project->id,
            'external_id' => '98765',
        ])
        ->assertNotFound();

    $this->assertDatabaseMissing('project_repositories', [
        'project_id' => $project->id,
        'external_id' => '98765',
    ]);
});

/*
|--------------------------------------------------------------------------
| GitHub repository synchronization tests
|--------------------------------------------------------------------------
*/

function createGitHubRepositoryForSync(
    User $user,
    array $connectionOverrides = [],
    array $repositoryOverrides = [],
): array {
    $connection = GitConnection::create(array_merge([
        'user_id' => $user->id,
        'provider' => 'github',
        'provider_user_id' => 'github-user-123',
        'username' => 'devlink-user',
        'email' => $user->email,
        'access_token' => 'fake-github-token',
        'scopes' => ['read:user', 'user:email', 'repo'],
        'connected_at' => now(),
    ], $connectionOverrides));

    $project = Project::create([
        'owner_id' => $user->id,
        'name' => 'Sync Test Project',
        'slug' => 'sync-test-project',
        'description' => 'Project used for synchronization tests',
        'short_description' => 'Sync test',
        'status' => 'in_progress',
        'visibility' => 'public',
        'looking_for_collaborators' => false,
    ]);

    $repository = ProjectRepository::create(array_merge([
        'project_id' => $project->id,
        'git_connection_id' => $connection->id,
        'connected_by' => $user->id,
        'provider' => 'github',
        'external_id' => '98765',
        'full_name' => 'devlink-user/old-repository',
        'name' => 'old-repository',
        'description' => 'Old description',
        'repository_url' => 'https://github.com/devlink-user/old-repository',
        'default_branch' => 'master',
        'is_private' => false,
        'is_active' => true,
    ], $repositoryOverrides));

    return [$project, $connection, $repository];
}

function githubRepositorySyncPayload(array $overrides = []): array
{
    return array_merge([
        'id' => 98765,
        'name' => 'new-repository',
        'full_name' => 'devlink-user/new-repository',
        'description' => 'Updated repository description',
        'html_url' => 'https://github.com/devlink-user/new-repository',
        'default_branch' => 'main',
        'private' => true,
    ], $overrides);
}

it('synchronizes GitHub repository metadata successfully', function () {
    [$project, $connection, $repository] =
        createGitHubRepositoryForSync($this->user);

    Http::fake([
        'api.github.com/repositories/98765' => Http::response(
            githubRepositorySyncPayload(),
            200,
        ),
    ]);

    $this->actingAs($this->user)
        ->post(route('projects.repositories.sync', [
            'slug' => $project->slug,
            'repository' => $repository->id,
        ]))
        ->assertRedirect();

    $this->assertDatabaseHas('project_repositories', [
        'id' => $repository->id,
        'full_name' => 'devlink-user/new-repository',
        'name' => 'new-repository',
        'description' => 'Updated repository description',
        'repository_url' => 'https://github.com/devlink-user/new-repository',
        'default_branch' => 'main',
        'is_private' => true,
    ]);

    $repository->refresh();

    expect($repository->last_synced_at)->not->toBeNull();

    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.github.com/repositories/98765'
            && $request->hasHeader('Authorization', 'Bearer fake-github-token');
    });
});

it('returns not found when synchronizing a repository belonging to another project', function () {
    [$project,, $repository] =
        createGitHubRepositoryForSync($this->user);

    $anotherProject = Project::create([
        'owner_id' => $this->user->id,
        'name' => 'Another Project',
        'slug' => 'another-project',
        'description' => 'Another project',
        'short_description' => 'Another project',
        'status' => 'in_progress',
        'visibility' => 'public',
        'looking_for_collaborators' => false,
    ]);

    $this->actingAs($this->user)
        ->post(route('projects.repositories.sync', [
            'slug' => $anotherProject->slug,
            'repository' => $repository->id,
        ]))
        ->assertNotFound();
});

it('redirects with an error when the repository GitHub connection is missing', function () {
    [$project,, $repository] =
        createGitHubRepositoryForSync($this->user);

    $repository->update([
        'git_connection_id' => null,
    ]);

    $this->actingAs($this->user)
        ->post(route('projects.repositories.sync', [
            'slug' => $project->slug,
            'repository' => $repository->id,
        ]))
        ->assertRedirect()
        ->assertSessionHas('error');

    Http::assertNothingSent();
});

it('handles an invalid GitHub token during synchronization', function () {
    [$project,, $repository] =
        createGitHubRepositoryForSync($this->user);

    Http::fake([
        'api.github.com/repositories/98765' => Http::response([
            'message' => 'Bad credentials',
        ], 401),
    ]);

    $this->actingAs($this->user)
        ->post(route('projects.repositories.sync', [
            'slug' => $project->slug,
            'repository' => $repository->id,
        ]))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($repository->fresh()->full_name)
        ->toBe('devlink-user/old-repository');
});

it('handles a repository that no longer exists on GitHub', function () {
    [$project,, $repository] =
        createGitHubRepositoryForSync($this->user);

    Http::fake([
        'api.github.com/repositories/98765' => Http::response([
            'message' => 'Not Found',
        ], 404),
    ]);

    $this->actingAs($this->user)
        ->post(route('projects.repositories.sync', [
            'slug' => $project->slug,
            'repository' => $repository->id,
        ]))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($repository->fresh()->full_name)
        ->toBe('devlink-user/old-repository');
});

it('handles GitHub API server errors during synchronization', function () {
    [$project,, $repository] =
        createGitHubRepositoryForSync($this->user);

    Http::fake([
        'api.github.com/repositories/98765' => Http::response([
            'message' => 'Internal Server Error',
        ], 500),
    ]);

    $this->actingAs($this->user)
        ->post(route('projects.repositories.sync', [
            'slug' => $project->slug,
            'repository' => $repository->id,
        ]))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($repository->fresh()->full_name)
        ->toBe('devlink-user/old-repository');
});

it('handles network failures during synchronization', function () {
    [$project,, $repository] =
        createGitHubRepositoryForSync($this->user);

    Http::fake([
        'api.github.com/repositories/98765' => function () {
            throw new ConnectionException('Simulated network failure.');
        },
    ]);

    Log::spy();

    $this->actingAs($this->user)
        ->post(route('projects.repositories.sync', [
            'slug' => $project->slug,
            'repository' => $repository->id,
        ]))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($repository->fresh()->full_name)
        ->toBe('devlink-user/old-repository');
});
