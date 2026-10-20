<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    /**
     * Fil d'actualité public.
     */
    public function index(Request $request): Response
    {
        $posts = Post::query()
            ->where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('community_id')
                    ->orWhereHas('community', function ($communityQuery) {
                        $communityQuery
                            ->where('is_active', true)
                            ->where('is_private', false);
                    });
            })
            ->with([
                'user:id,name',
                'community:id,name,slug',
            ])
            ->withCount(['comments', 'likes'])
            ->latest('published_at')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $currentUser = $request->user();

        $posts->getCollection()->transform(function (Post $post) use ($currentUser) {
            $post->setAttribute(
                'liked_by_me',
                $currentUser
                    ? $post->likes()
                    ->where('user_id', $currentUser->id)
                    ->exists()
                    : false
            );

            return $post;
        });

        return Inertia::render('posts/Index', [
            'posts' => $posts,
        ]);
    }

    /**
     * Formulaire de création.
     */
    public function create(Request $request): Response
    {
        $communities = $request->user()
            ->communities()
            ->where('communities.is_active', true)
            ->where('communities.is_private', false)
            ->orderBy('communities.name')
            ->get(['communities.id', 'communities.name']);

        return Inertia::render('posts/Create', [
            'communities' => $communities,
        ]);
    }

    /**
     * Enregistrer une publication.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:200'],
            'body' => ['required', 'string', 'min:1', 'max:20000'],
            'community_id' => ['nullable', 'integer', 'exists:communities,id'],
        ]);

        $community = null;

        if (! empty($validated['community_id'])) {
            $community = Community::query()
                ->whereKey($validated['community_id'])
                ->where('is_active', true)
                ->where('is_private', false)
                ->firstOrFail();

            $isMember = $community->users()
                ->where('users.id', $request->user()->id)
                ->exists();

            if (! $isMember) {
                abort(403, 'Tu dois rejoindre cette communauté avant d’y publier.');
            }
        }

        $post = $request->user()->posts()->create([
            'community_id' => $community?->id,
            'type' => 'text',
            'title' => filled($validated['title'] ?? null)
                ? trim($validated['title'])
                : null,
            'body' => trim($validated['body']),
            'status' => 'published',
            'published_at' => now(),
        ]);

        return redirect()
            ->route('posts.show', $post)
            ->with('success', 'Ta publication a été créée.');
    }

    /**
     * Consulter une publication.
     */
    public function show(Request $request, Post $post): Response
    {
        abort_unless($post->status === 'published', 404);

        if ($post->community_id !== null) {
            abort_unless(
                $post->community()
                    ->where('is_active', true)
                    ->where('is_private', false)
                    ->exists(),
                404
            );
        }

        $post->load([
            'user:id,name',
            'community:id,name,slug',
            'comments' => fn($query) => $query
                ->with('user:id,name')
                ->latest(),
        ]);

        $post->loadCount(['comments', 'likes']);

        $post->setAttribute(
            'liked_by_me',
            $request->user()
                ? $post->likes()
                ->where('user_id', $request->user()->id)
                ->exists()
                : false
        );

        return Inertia::render('posts/Show', [
            'post' => $post,
        ]);
    }
}
