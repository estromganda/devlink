<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class QuestionController extends Controller
{
    public function index(Request $request): Response
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'tag' => ['nullable', 'string', 'max:100'],
            'filter' => ['nullable', 'in:latest,oldest,votes,unanswered'],
        ]);

        $search = $validated['search'] ?? null;
        $tagSlug = $validated['tag'] ?? null;
        $filter = $validated['filter'] ?? 'latest';

        $questions = Post::query()
            ->where('type', 'question')
            ->where('status', 'published')
            ->whereNull('deleted_at')
            ->where(function ($query) {
                $query->whereNull('community_id')
                    ->orWhereHas('community', function ($communityQuery) {
                        $communityQuery
                            ->where('is_private', false)
                            ->where('is_active', true);
                    });
            })
            ->with([
                'user:id,name',
                'tags:id,name,slug',
            ])
            ->withCount([
                'answers' => fn($query) => $query
                    ->whereNull('deleted_at'),
                'likes',
            ]);

        if ($search) {
            $questions->where(function ($query) use ($search) {
                $query->where('title', 'ilike', "%{$search}%")
                    ->orWhere('body', 'ilike', "%{$search}%");
            });
        }

        if ($tagSlug) {
            $questions->whereHas('tags', function ($query) use ($tagSlug) {
                $query->where('slug', $tagSlug);
            });
        }

        if ($filter === 'unanswered') {
            $questions->whereDoesntHave('answers', function ($query) {
                $query->whereNull('deleted_at');
            });
        }

        match ($filter) {
            'oldest' => $questions->orderBy('created_at'),
            'votes' => $questions->orderByDesc('likes_count'),
            default => $questions->orderByDesc('created_at'),
        };

        $questions = $questions
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('questions/Index', [
            'questions' => $questions,
            'filters' => [
                'search' => $search,
                'tag' => $tagSlug,
                'filter' => $filter,
            ],
            'tags' => Tag::query()
                ->whereHas('posts', function ($query) {
                    $query->where('type', 'question')
                        ->where('status', 'published');
                })
                ->orderBy('name')
                ->get(['id', 'name', 'slug']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('questions/Create', [
            'tags' => Tag::query()
                ->orderBy('name')
                ->get(['id', 'name', 'slug']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:10', 'max:200'],
            'body' => ['required', 'string', 'min:20', 'max:20000'],
            'tags' => ['required', 'array', 'min:1', 'max:5'],
            'tags.*' => ['required', 'integer', 'distinct', 'exists:tags,id'],
        ]);

        $question = DB::transaction(function () use ($request, $validated) {
            $question = Post::create([
                'user_id' => $request->user()->id,
                'community_id' => null,
                'type' => 'question',
                'title' => $validated['title'],
                'body' => $validated['body'],
                'status' => 'published',
                'is_pinned' => false,
                'published_at' => now(),
            ]);

            $question->tags()->sync($validated['tags']);

            return $question;
        });

        return redirect()
            ->route('questions.show', $question)
            ->with('success', 'Question publiée avec succès.');
    }

    public function show(Request $request, Post $question): Response
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

        $question->load([
            'user:id,name',
            'tags:id,name,slug',
        ]);

        $question->loadCount([
            'likes',
            'answers' => fn($query) => $query->whereNull('deleted_at'),
        ]);

        $answers = $question->answers()
            ->whereNull('deleted_at')
            ->with('user:id,name')
            ->withCount('votes')
            ->withSum('votes', 'value')
            ->orderByDesc('is_accepted')
            ->orderByDesc('votes_sum_value')
            ->orderBy('created_at')
            ->paginate(20)
            ->withQueryString();

        $userId = $request->user()?->id;

        $userQuestionVote = $userId
            ? $question->likes()
            ->where('user_id', $userId)
            ->exists()
            : false;

        $userAnswerVotes = $userId
            ? $question->answers()
            ->whereHas('votes', fn($query) => $query->where('user_id', $userId))
            ->with('votes')
            ->get()
            ->flatMap(fn($answer) => $answer->votes)
            ->pluck('value', 'answer_id')
            : collect();

        return Inertia::render('questions/Show', [
            'question' => $question,
            'answers' => $answers,
            'userQuestionVote' => $userQuestionVote,
            'userAnswerVotes' => $userAnswerVotes,
            'canAnswer' => $request->user() !== null,
            'canEdit' => $userId !== null
                && $question->user_id === $userId,
        ]);
    }

    public function edit(Request $request, Post $question): Response
    {
        abort_unless(
            $question->type === 'question'
                && $question->user_id === $request->user()->id,
            403
        );

        return Inertia::render('questions/Edit', [
            'question' => $question->load('tags:id,name,slug'),
            'tags' => Tag::query()
                ->orderBy('name')
                ->get(['id', 'name', 'slug']),
        ]);
    }

    public function update(Request $request, Post $question): RedirectResponse
    {
        abort_unless(
            $question->type === 'question'
                && $question->user_id === $request->user()->id,
            403
        );

        $validated = $request->validate([
            'title' => ['required', 'string', 'min:10', 'max:200'],
            'body' => ['required', 'string', 'min:20', 'max:20000'],
            'tags' => ['required', 'array', 'min:1', 'max:5'],
            'tags.*' => ['required', 'integer', 'distinct', 'exists:tags,id'],
        ]);

        DB::transaction(function () use ($question, $validated) {
            $question->update([
                'title' => $validated['title'],
                'body' => $validated['body'],
            ]);

            $question->tags()->sync($validated['tags']);
        });

        return redirect()
            ->route('questions.show', $question)
            ->with('success', 'Question modifiée avec succès.');
    }

    public function destroy(Request $request, Post $question): RedirectResponse
    {
        abort_unless(
            $question->type === 'question'
                && $question->user_id === $request->user()->id,
            403
        );

        $question->delete();

        return redirect()
            ->route('questions.index')
            ->with('success', 'Question supprimée.');
    }
}
