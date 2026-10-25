<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnswerController extends Controller
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

    public function store(Request $request, Post $question): RedirectResponse
    {
        $this->ensureQuestionIsAvailable($question);

        $validated = $request->validate([
            'body' => ['required', 'string', 'min:10', 'max:20000'],
        ]);

        $question->answers()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
            'is_accepted' => false,
        ]);

        return redirect()
            ->route('questions.show', $question)
            ->with('success', 'Ta réponse a été publiée.');
    }

    public function update(
        Request $request,
        Post $question,
        Answer $answer
    ): RedirectResponse {
        $this->ensureQuestionIsAvailable($question);

        abort_unless(
            (int) $answer->post_id === (int) $question->id,
            404
        );

        abort_unless(
            (int) $answer->user_id === (int) $request->user()->id,
            403
        );

        abort_if($answer->deleted_at !== null, 404);

        $validated = $request->validate([
            'body' => ['required', 'string', 'min:10', 'max:20000'],
        ]);

        $answer->update([
            'body' => $validated['body'],
        ]);

        return redirect()
            ->route('questions.show', $question)
            ->with('success', 'Ta réponse a été modifiée.');
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

        abort_unless(
            (int) $answer->user_id === (int) $request->user()->id,
            403
        );

        abort_if($answer->deleted_at !== null, 404);

        $answer->delete();

        return redirect()
            ->route('questions.show', $question)
            ->with('success', 'Ta réponse a été supprimée.');
    }

    public function accept(
        Request $request,
        Post $question,
        Answer $answer
    ): RedirectResponse {
        $this->ensureQuestionIsAvailable($question);

        abort_unless(
            (int) $question->user_id === (int) $request->user()->id,
            403
        );

        abort_unless(
            (int) $answer->post_id === (int) $question->id,
            404
        );

        abort_if($answer->deleted_at !== null, 404);

        DB::transaction(function () use ($question, $answer) {
            // Verrouille la question afin d'éviter deux acceptations simultanées.
            $lockedQuestion = Post::query()
                ->whereKey($question->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                $lockedQuestion->type === 'question'
                    && $lockedQuestion->status === 'published'
                    && $lockedQuestion->deleted_at === null,
                404
            );

            Answer::query()
                ->where('post_id', $lockedQuestion->id)
                ->update(['is_accepted' => false]);

            $answer->update(['is_accepted' => true]);
        });

        return redirect()
            ->route('questions.show', $question)
            ->with('success', 'La réponse a été acceptée.');
    }
}
