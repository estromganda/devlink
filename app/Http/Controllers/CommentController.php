<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Post $post): RedirectResponse
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

        $validated = $request->validate([
            'body' => ['required', 'string', 'min:1', 'max:5000'],
        ]);

        $post->comments()->create([
            'user_id' => $request->user()->id,
            'body' => trim($validated['body']),
        ]);

        return redirect()
            ->route('posts.show', $post)
            ->with('success', 'Ton commentaire a été ajouté.');
    }
}
