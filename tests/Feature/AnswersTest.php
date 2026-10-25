<?php

use App\Models\Answer;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createAnswerTestQuestion(User $user, array $attributes = []): Post
{
    return Post::factory()->create(array_merge([
        'user_id' => $user->id,
        'community_id' => null,
        'type' => 'question',
        'title' => 'Comment organiser correctement une application Laravel ?',
        'body' => 'Je cherche des conseils pour organiser mon application Laravel.',
        'status' => 'published',
        'is_pinned' => false,
        'published_at' => now(),
        'deleted_at' => null,
    ], $attributes));
}

function createAnswerTestAnswer(Post $question, User $user, array $attributes = []): Answer
{
    return Answer::factory()->create(array_merge([
        'post_id' => $question->id,
        'user_id' => $user->id,
        'body' => 'Voici une réponse détaillée qui explique comment résoudre ce problème.',
        'is_accepted' => false,
    ], $attributes));
}

it('allows an authenticated user to submit an answer', function () {
    $author = User::factory()->create();
    $responder = User::factory()->create();
    $question = createAnswerTestQuestion($author);

    $this->actingAs($responder)
        ->post(route('questions.answers.store', $question), [
            'body' => 'Voici une réponse suffisamment détaillée à cette question.',
        ])
        ->assertRedirect(route('questions.show', $question));

    $this->assertDatabaseHas('answers', [
        'post_id' => $question->id,
        'user_id' => $responder->id,
        'body' => 'Voici une réponse suffisamment détaillée à cette question.',
    ]);
});

it('redirects guests when they attempt to submit an answer', function () {
    $question = createAnswerTestQuestion(User::factory()->create());

    $this->post(route('questions.answers.store', $question), [
        'body' => 'Voici une réponse suffisamment détaillée à cette question.',
    ])->assertRedirect(route('login'));
});

it('rejects an answer with a body that is too short', function () {
    $user = User::factory()->create();
    $question = createAnswerTestQuestion(User::factory()->create());

    $this->actingAs($user)
        ->post(route('questions.answers.store', $question), [
            'body' => 'Court',
        ])
        ->assertSessionHasErrors('body');

    $this->assertDatabaseMissing('answers', [
        'post_id' => $question->id,
        'user_id' => $user->id,
        'body' => 'Trop court',
    ]);
});

it('allows the author to update their answer', function () {
    $user = User::factory()->create();
    $question = createAnswerTestQuestion(User::factory()->create());
    $answer = createAnswerTestAnswer($question, $user);

    $newBody = 'Voici le contenu corrigé de ma réponse avec davantage de détails utiles.';

    $this->actingAs($user)
        ->put(route('questions.answers.update', [$question, $answer]), [
            'body' => $newBody,
        ])
        ->assertRedirect(route('questions.show', $question));

    $this->assertDatabaseHas('answers', [
        'id' => $answer->id,
        'body' => $newBody,
    ]);
});

it('prevents another user from updating an answer', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $question = createAnswerTestQuestion(User::factory()->create());
    $answer = createAnswerTestAnswer($question, $owner);

    $this->actingAs($otherUser)
        ->put(route('questions.answers.update', [$question, $answer]), [
            'body' => 'Je tente de modifier la réponse de quelqu’un d’autre.',
        ])
        ->assertForbidden();

    $this->assertDatabaseHas('answers', [
        'id' => $answer->id,
        'body' => $answer->body,
    ]);
});

it('allows the author to delete their answer', function () {
    $user = User::factory()->create();
    $question = createAnswerTestQuestion(User::factory()->create());
    $answer = createAnswerTestAnswer($question, $user);

    $this->actingAs($user)
        ->delete(route('questions.answers.destroy', [$question, $answer]))
        ->assertRedirect(route('questions.show', $question));

    $this->assertSoftDeleted('answers', [
        'id' => $answer->id,
    ]);
});

it('prevents another user from deleting an answer', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $question = createAnswerTestQuestion(User::factory()->create());
    $answer = createAnswerTestAnswer($question, $owner);

    $this->actingAs($otherUser)
        ->delete(route('questions.answers.destroy', [$question, $answer]))
        ->assertForbidden();

    $this->assertDatabaseHas('answers', [
        'id' => $answer->id,
        'deleted_at' => null,
    ]);
});

it('allows the question author to accept an answer', function () {
    $questionAuthor = User::factory()->create();
    $question = createAnswerTestQuestion($questionAuthor);
    $answerAuthor = User::factory()->create();
    $answer = createAnswerTestAnswer($question, $answerAuthor);

    $this->actingAs($questionAuthor)
        ->post(route('questions.answers.accept', [$question, $answer]))
        ->assertRedirect(route('questions.show', $question));

    $this->assertDatabaseHas('answers', [
        'id' => $answer->id,
        'is_accepted' => true,
    ]);
});

it('prevents another user from accepting an answer', function () {
    $question = createAnswerTestQuestion(User::factory()->create());
    $answer = createAnswerTestAnswer($question, User::factory()->create());
    $otherUser = User::factory()->create();

    $this->actingAs($otherUser)
        ->post(route('questions.answers.accept', [$question, $answer]))
        ->assertForbidden();

    $this->assertDatabaseHas('answers', [
        'id' => $answer->id,
        'is_accepted' => false,
    ]);
});

it('returns not found when an answer belongs to another question', function () {
    $user = User::factory()->create();
    $questionA = createAnswerTestQuestion(User::factory()->create());
    $questionB = createAnswerTestQuestion(User::factory()->create(), [
        'title' => 'Une autre question sur Laravel et son architecture',
    ]);
    $answer = createAnswerTestAnswer($questionA, $user);

    $this->actingAs($user)
        ->put(route('questions.answers.update', [$questionB, $answer]), [
            'body' => 'Je tente de modifier une réponse associée à une autre question.',
        ])
        ->assertNotFound();
});
