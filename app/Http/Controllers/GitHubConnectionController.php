<?php

namespace App\Http\Controllers;

use App\Models\GitConnection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GitHubConnectionController extends Controller
{
    /**
     * Redirect the authenticated DevColab user to GitHub.
     */
    public function redirect(Request $request): RedirectResponse
    {
        $request->session()->put(
            'github_connection_return_url',
            url()->previous()
        );

        return Socialite::driver('github')
            ->scopes([
                'read:user',
                'user:email',
                'repo',
            ])
            ->redirect();
    }

    /**
     * Handle GitHub's OAuth callback.
     */
    public function callback(Request $request): RedirectResponse
    {
        if ($request->filled('error')) {
            return redirect('/dashboard')
                ->with('error', 'La connexion GitHub a été refusée ou annulée.');
        }

        try {
            $githubUser = Socialite::driver('github')->user();

            $providerUserId = (string) $githubUser->getId();
            $username = $githubUser->getNickname()
                ?: $githubUser->getName()
                ?: 'github-user';

            if ($providerUserId === '') {
                return redirect('/dashboard')
                    ->with('error', 'GitHub n’a pas retourné un identifiant valide.');
            }

            $expiresAt = $githubUser->expiresIn
                ? now()->addSeconds((int) $githubUser->expiresIn)
                : null;

            GitConnection::updateOrCreate(
                [
                    'user_id' => $request->user()->id,
                    'provider' => 'github',
                    'provider_user_id' => $providerUserId,
                ],
                [
                    'username' => $username,
                    'email' => $githubUser->getEmail(),
                    'avatar_url' => $githubUser->getAvatar(),
                    'access_token' => $githubUser->token,
                    'refresh_token' => $githubUser->refreshToken,
                    'token_expires_at' => $expiresAt,
                    'scopes' => $githubUser->approvedScopes ?? [],
                    'connected_at' => now(),
                ]
            );

            $returnUrl = $request->session()->pull(
                'github_connection_return_url',
                url('/dashboard')
            );

            // Only redirect to URLs belonging to this application.
            if (
                ! is_string($returnUrl)
                || ! str_starts_with($returnUrl, url('/'))
            ) {
                $returnUrl = url('/dashboard');
            }

            return redirect($returnUrl)
                ->with('success', 'Ton compte GitHub est connecté à DevColab.');
        } catch (Throwable $exception) {
            Log::error('GitHub OAuth connection failed.', [
                'user_id' => $request->user()?->id,
                'exception' => $exception::class,
            ]);

            return redirect('/dashboard')
                ->with(
                    'error',
                    'Impossible de connecter GitHub. Vérifie la configuration OAuth et réessaie.'
                );
        }
    }
}
