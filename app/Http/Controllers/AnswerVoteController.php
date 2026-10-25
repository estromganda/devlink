<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\AnswerVote;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AnswerVoteController extends Controller
{
    private function ensureQuestionIsAvailable(Post $question): void
    {
        abort_unless(
            $question->type === 'question'
                && $question->status === 'published'
                && $question->deleted_at === null,
            404
        );

        if ($question->community_id !== null) {
            abort_unless(
                $question->community()
                    ->where('is_private', false)
                    ->where('is_active', true)
                    ->exists(),
                404
            );
        }
    }

    public function store(
        Request $request,
        Post $question,
        Answer $answer
    ): RedirectResponse {
        $this->ensureQuestionIsAvailable($question);

        abort_unless(
            (int) $answer->post_id === (int) $question->id,
            404
        );

        abort_if($answer->deleted_at !== null, 404);

        $validated = $request->validate([
            'value' => ['required', 'integer', Rule::in([-1, 1])],
        ]);

        DB::transaction(function () use ($request, $answer, $validated) {
            AnswerVote::query()->updateOrCreate(
                [
                    'answer_id' => $answer->id,
                    'user_id' => $request->user()->id,
                ],
                [
                    'value' => (int) $validated['value'],
                ]
            );
        });

        return redirect()
            ->route('questions.show', $question)
            ->with('success', 'Ton vote a été enregistré.');
    }

    public function destroy(
        Request $request,
        Post $question,
        Answer $answer
    ): RedirectResponse {
        $this->ensureQuestionIsAvailable($question);

        abort_unless(
            (int) $answer->post_id === (int) $question->id,
            404
        );

        AnswerVote::query()
            ->where('answer_id', $answer->id)
            ->where('user_id', $request->user()->id)
            ->delete();

        return redirect()
            ->route('questions.show', $question)
            ->with('success', 'Ton vote a été retiré.');
    }
}
