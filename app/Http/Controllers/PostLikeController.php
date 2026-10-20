<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PostLikeController extends Controller
{
    /**
     * Ajouter un J'aime.
     */
    public function store(Request $request, Post $post): RedirectResponse
    {
        $this->ensurePostIsPublic($post);

        $post->likes()->firstOrCreate([
            'user_id' => $request->user()->id,
        ]);

        return back();
    }

    /**
     * Retirer un J'aime.
     */
    public function destroy(Request $request, Post $post): RedirectResponse
    {
        $this->ensurePostIsPublic($post);

        $post->likes()
            ->where('user_id', $request->user()->id)
            ->delete();

        return back();
    }

    /**
     * Vérifier que la publication peut être consultée publiquement.
     */
    private function ensurePostIsPublic(Post $post): void
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
    }
}
