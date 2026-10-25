<?php

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createQuestionTestTags(int $count = 1): array
{
    return Tag::factory()->count($count)->create()->pluck('id')->all();
}

function createQuestionTestPost(User $user, array $attributes = []): Post
{
    return Post::factory()->create(array_merge([
        'user_id' => $user->id,
        'community_id' => null,
        'type' => 'question',
        'title' => 'Comment utiliser Laravel correctement ?',
        'body' => 'Je cherche à comprendre les bonnes pratiques pour organiser une application Laravel.',
        'status' => 'published',
        'is_pinned' => false,
        'published_at' => now(),
    ], $attributes));
}

it('allows guests to view the questions index', function () {
    $this->get(route('questions.index'))
        ->assertOk()
        ->assertInertia(
            fn(Assert $page) => $page
                ->component('questions/Index')
                ->has('questions')
                ->has('filters')
                ->has('tags')
        );
});

it('allows authenticated users to view the question creation page', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('questions.create'))
        ->assertOk()
        ->assertInertia(
            fn(Assert $page) => $page
                ->component('questions/Create')
                ->has('tags')
        );
});

it('allows an authenticated user to publish a question with tags', function () {
    $user = User::factory()->create();
    $tagIds = createQuestionTestTags(2);

    $response = $this->actingAs($user)->post(route('questions.store'), [
        'title' => 'Comment structurer une application Laravel ?',
        'body' => 'Je voudrais connaître les bonnes pratiques pour organiser mon application.',
        'tags' => $tagIds,
    ]);

    $question = Post::query()
        ->where('user_id', $user->id)
        ->where('type', 'question')
        ->firstOrFail();

    $response->assertRedirect(route('questions.show', $question));

    expect($question->status)->toBe('published')
        ->and($question->community_id)->toBeNull()
        ->and($question->is_pinned)->toBeFalse()
        ->and($question->tags()->pluck('tags.id')->all())->toEqualCanonicalizing($tagIds);
});

it('requires a title, body, and at least one tag to publish a question', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('questions.store'), [
        'title' => '',
        'body' => '',
        'tags' => [],
    ]);

    $response->assertSessionHasErrors(['title', 'body', 'tags']);

    expect(Post::query()->where('type', 'question')->count())->toBe(0);
});

it('rejects a question with a title that is too short', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('questions.store'), [
        'title' => 'Court',
        'body' => 'Voici un contenu suffisamment long pour la question.',
        'tags' => createQuestionTestTags(),
    ]);

    $response->assertSessionHasErrors('title');
});

it('rejects a question with a body that is too short', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('questions.store'), [
        'title' => 'Comment utiliser Laravel correctement ?',
        'body' => 'Trop court',
        'tags' => createQuestionTestTags(),
    ]);

    $response->assertSessionHasErrors('body');
});

it('rejects nonexistent tag identifiers', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('questions.store'), [
        'title' => 'Comment utiliser Laravel correctement ?',
        'body' => 'Je voudrais connaître les bonnes pratiques pour organiser mon application.',
        'tags' => [999999],
    ]);

    $response->assertSessionHasErrors('tags.0');
});

it('allows the author to edit their question', function () {
    $user = User::factory()->create();
    $tagIds = createQuestionTestTags();
    $question = createQuestionTestPost($user);
    $question->tags()->sync($tagIds);

    $this->actingAs($user)
        ->get(route('questions.edit', $question))
        ->assertOk()
        ->assertInertia(
            fn(Assert $page) => $page
                ->component('questions/Edit')
                ->where('question.id', $question->id)
        );
});

it('prevents another user from editing a question', function () {
    $author = User::factory()->create();
    $otherUser = User::factory()->create();
    $question = createQuestionTestPost($author);

    $this->actingAs($otherUser)
        ->get(route('questions.edit', $question))
        ->assertForbidden();
});

it('allows the author to update their question', function () {
    $user = User::factory()->create();
    $tagIds = createQuestionTestTags();
    $question = createQuestionTestPost($user);

    $this->actingAs($user)
        ->put(route('questions.update', $question), [
            'title' => 'Comment améliorer une application Laravel ?',
            'body' => 'Voici le nouveau contenu de la question avec suffisamment de détails.',
            'tags' => $tagIds,
        ])
        ->assertRedirect(route('questions.show', $question));

    $this->assertDatabaseHas('posts', [
        'id' => $question->id,
        'title' => 'Comment améliorer une application Laravel ?',
    ]);

    expect($question->fresh()->tags()->pluck('tags.id')->all())
        ->toEqualCanonicalizing($tagIds);
});

it('prevents another user from updating a question', function () {
    $author = User::factory()->create();
    $otherUser = User::factory()->create();
    $question = createQuestionTestPost($author);

    $this->actingAs($otherUser)
        ->put(route('questions.update', $question), [
            'title' => 'Comment améliorer une application Laravel ?',
            'body' => 'Voici le nouveau contenu de la question avec suffisamment de détails.',
            'tags' => createQuestionTestTags(),
        ])
        ->assertForbidden();
});

it('allows the author to delete their question', function () {
    $user = User::factory()->create();
    $question = createQuestionTestPost($user);

    $this->actingAs($user)
        ->delete(route('questions.destroy', $question))
        ->assertRedirect(route('questions.index'));

    $this->assertSoftDeleted('posts', ['id' => $question->id]);
});

it('prevents another user from deleting a question', function () {
    $author = User::factory()->create();
    $otherUser = User::factory()->create();
    $question = createQuestionTestPost($author);

    $this->actingAs($otherUser)
        ->delete(route('questions.destroy', $question))
        ->assertForbidden();

    $this->assertDatabaseHas('posts', ['id' => $question->id]);
});

it('allows guests to view a published question', function () {
    $user = User::factory()->create();
    $question = createQuestionTestPost($user);

    $this->get(route('questions.show', $question))
        ->assertOk()
        ->assertInertia(
            fn(Assert $page) => $page
                ->component('questions/Show')
                ->where('question.id', $question->id)
                ->where('canAnswer', false)
                ->where('canEdit', false)
        );
});

it('does not display a draft question', function () {
    $user = User::factory()->create();
    $question = createQuestionTestPost($user, ['status' => 'draft']);

    $this->get(route('questions.show', $question))
        ->assertNotFound();
});
