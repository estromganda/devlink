<?php

use App\Models\Answer;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

function createAnswerVoteTestQuestion(User $user, array $attributes = []): Post
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

function createAnswerVoteTestAnswer(
    Post $question,
    User $user,
    array $attributes = []
): Answer {
    return Answer::factory()->create(array_merge([
        'post_id' => $question->id,
        'user_id' => $user->id,
        'body' => 'Voici une réponse détaillée qui explique comment résoudre ce problème.',
        'is_accepted' => false,
    ], $attributes));
}

it('allows an authenticated user to upvote an answer', function () {
    $voter = User::factory()->create();
    $question = createAnswerVoteTestQuestion(User::factory()->create());
    $answer = createAnswerVoteTestAnswer($question, User::factory()->create());

    $this->actingAs($voter)
        ->put(route('questions.answers.vote', [$question, $answer]), [
            'value' => 1,
        ])
        ->assertRedirect(route('questions.show', $question));

    $this->assertDatabaseHas('answer_votes', [
        'answer_id' => $answer->id,
        'user_id' => $voter->id,
        'value' => 1,
    ]);
});

it('allows an authenticated user to downvote an answer', function () {
    $voter = User::factory()->create();
    $question = createAnswerVoteTestQuestion(User::factory()->create());
    $answer = createAnswerVoteTestAnswer($question, User::factory()->create());

    $this->actingAs($voter)
        ->put(route('questions.answers.vote', [$question, $answer]), [
            'value' => -1,
        ])
        ->assertRedirect(route('questions.show', $question));

    $this->assertDatabaseHas('answer_votes', [
        'answer_id' => $answer->id,
        'user_id' => $voter->id,
        'value' => -1,
    ]);
});

it('rejects an invalid vote value', function () {
    $voter = User::factory()->create();
    $question = createAnswerVoteTestQuestion(User::factory()->create());
    $answer = createAnswerVoteTestAnswer($question, User::factory()->create());

    $this->actingAs($voter)
        ->put(route('questions.answers.vote', [$question, $answer]), [
            'value' => 0,
        ])
        ->assertSessionHasErrors('value');

    $this->assertDatabaseMissing('answer_votes', [
        'answer_id' => $answer->id,
        'user_id' => $voter->id,
    ]);
});

it('updates an existing vote instead of creating a duplicate', function () {
    $voter = User::factory()->create();
    $question = createAnswerVoteTestQuestion(User::factory()->create());
    $answer = createAnswerVoteTestAnswer($question, User::factory()->create());

    $this->actingAs($voter)
        ->put(route('questions.answers.vote', [$question, $answer]), [
            'value' => 1,
        ])
        ->assertRedirect(route('questions.show', $question));

    $this->actingAs($voter)
        ->put(route('questions.answers.vote', [$question, $answer]), [
            'value' => -1,
        ])
        ->assertRedirect(route('questions.show', $question));

    $this->assertDatabaseHas('answer_votes', [
        'answer_id' => $answer->id,
        'user_id' => $voter->id,
        'value' => -1,
    ]);

    expect(
        DB::table('answer_votes')
            ->where('answer_id', $answer->id)
            ->where('user_id', $voter->id)
            ->count()
    )->toBe(1);
});

it('allows a user to remove their vote', function () {
    $voter = User::factory()->create();
    $question = createAnswerVoteTestQuestion(User::factory()->create());
    $answer = createAnswerVoteTestAnswer($question, User::factory()->create());

    $this->actingAs($voter)
        ->put(route('questions.answers.vote', [$question, $answer]), [
            'value' => 1,
        ])
        ->assertRedirect(route('questions.show', $question));

    $this->actingAs($voter)
        ->delete(route('questions.answers.vote.destroy', [$question, $answer]))
        ->assertRedirect(route('questions.show', $question));

    $this->assertDatabaseMissing('answer_votes', [
        'answer_id' => $answer->id,
        'user_id' => $voter->id,
    ]);
});

it('redirects guests when they attempt to vote', function () {
    $question = createAnswerVoteTestQuestion(User::factory()->create());
    $answer = createAnswerVoteTestAnswer($question, User::factory()->create());

    $this->put(route('questions.answers.vote', [$question, $answer]), [
        'value' => 1,
    ])->assertRedirect(route('login'));
});

it('returns not found when voting on an answer belonging to another question', function () {
    $voter = User::factory()->create();
    $questionA = createAnswerVoteTestQuestion(User::factory()->create());
    $questionB = createAnswerVoteTestQuestion(User::factory()->create(), [
        'title' => 'Une autre question sur Laravel et son architecture',
    ]);
    $answer = createAnswerVoteTestAnswer($questionA, User::factory()->create());

    $this->actingAs($voter)
        ->put(route('questions.answers.vote', [$questionB, $answer]), [
            'value' => 1,
        ])
        ->assertNotFound();

    $this->assertDatabaseMissing('answer_votes', [
        'answer_id' => $answer->id,
        'user_id' => $voter->id,
    ]);
});
