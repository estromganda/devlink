<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GithubProvider;
use Laravel\Socialite\Two\GoogleProvider;
use Throwable;

class SocialAuthController extends Controller
{
    /**
     * Redirige vers le fournisseur OAuth.
     */
    public function redirect(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, ['google', 'github'], true), 404);

        if (Auth::check()) {
            return redirect()->route('home');
        }

        try {
            return $this->socialDriver($provider)
                ->redirect();
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('login')
                ->with('error', 'Impossible de démarrer la connexion sociale.');
        }
    }

    /**
     * Traite le retour du fournisseur OAuth.
     */
    public function callback(Request $request, string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, ['google', 'github'], true), 404);

        if ($request->filled('error')) {
            return redirect()
                ->route('login')
                ->with('error', 'La connexion sociale a été annulée ou refusée.');
        }

        try {
            $socialUser = $this->socialDriver($provider)->user();

            if (! $socialUser->getId()) {
                return $this->loginError(
                    'Le fournisseur n’a pas retourné d’identifiant valide.'
                );
            }

            /*
             * Une identité déjà enregistrée permet de retrouver
             * le compte DevColab même si l'e-mail a changé.
             */
            $socialAccount = SocialAccount::query()
                ->where('provider', $provider)
                ->where('provider_user_id', (string) $socialUser->getId())
                ->first();

            if ($socialAccount) {
                Auth::login($socialAccount->user);
                $request->session()->regenerate();

                return redirect()->intended(route('home'));
            }

            $email = $this->verifiedEmail($provider, $socialUser);

            if (! $email) {
                return $this->loginError(
                    'Impossible de récupérer une adresse e-mail vérifiée. '
                        . 'Vérifie les permissions de ton compte fournisseur et réessaie.'
                );
            }

            /*
            * Si un compte DevColab possède déjà cette adresse e-mail,
            * authentifier directement l'utilisateur sans créer de SocialAccount.
            */
            $existingUser = User::query()
                ->where('email', $email)
                ->first();

            if ($existingUser) {
                Auth::login($existingUser);
                $request->session()->regenerate();
                return redirect()->intended(route('home'));
            }

            $user = DB::transaction(function () use ($provider, $socialUser, $email) {
                $user = User::create([
                    'name' => $socialUser->getName()
                        ?: $socialUser->getNickname()
                        ?: ucfirst($provider) . ' user',
                    'email' => $email,
                    'password' => Hash::make(Str::random(64)),
                    'email_verified_at' => now(),
                ]);

                SocialAccount::create([
                    'user_id' => $user->id,
                    'provider' => $provider,
                    'provider_user_id' => (string) $socialUser->getId(),
                    'provider_email' => $email,
                    'provider_name' => $socialUser->getName(),
                    'provider_avatar' => $socialUser->getAvatar(),
                ]);

                return $user;
            });

            Auth::login($user);
            $request->session()->regenerate();

            return redirect()
                ->route('home')
                ->with('success', 'Bienvenue sur DevColab !');
        } catch (Throwable $exception) {
            report($exception);

            return $this->loginError(
                'La connexion a échoué. Réessaie dans quelques instants.'
            );
        }
    }

    /**
     * Construit le client Socialite avec la configuration dédiée
     * à l'authentification sociale.
     */
    private function socialDriver(string $provider)
    {
        $class = match ($provider) {
            'google' => GoogleProvider::class,
            'github' => GithubProvider::class,
            default => abort(404),
        };

        $config = config("services.{$provider}_auth");

        if (
            empty($config['client_id'])
            || empty($config['client_secret'])
            || empty($config['redirect'])
        ) {
            throw new \RuntimeException(
                "La configuration OAuth du fournisseur {$provider} est incomplète."
            );
        }

        $driver = Socialite::buildProvider($class, $config);

        if ($provider === 'github') {
            $driver->scopes(['read:user', 'user:email']);
        }

        return $driver;
    }

    /**
     * Ne retourne qu'une adresse dont la vérification est confirmée.
     */
    private function verifiedEmail(string $provider, object $socialUser): ?string
    {
        if ($provider === 'google') {
            if (($socialUser->user['email_verified'] ?? false) !== true) {
                return null;
            }

            return $socialUser->getEmail();
        }

        /*
         * GitHub : rechercher une adresse principale vérifiée.
         * Le scope user:email est nécessaire pour cette API.
         */
        $response = Http::withToken($socialUser->token)
            ->acceptJson()
            ->withHeaders([
                'X-GitHub-Api-Version' => '2022-11-28',
            ])
            ->timeout(10)
            ->get('https://api.github.com/user/emails');

        if (! $response->successful()) {
            return null;
        }

        $emails = $response->json();

        if (! is_array($emails)) {
            return null;
        }

        foreach ($emails as $entry) {
            if (
                is_array($entry)
                && ($entry['primary'] ?? false) === true
                && ($entry['verified'] ?? false) === true
                && filter_var($entry['email'] ?? null, FILTER_VALIDATE_EMAIL)
            ) {
                return $entry['email'];
            }
        }

        return null;
    }

    private function loginError(string $message): RedirectResponse
    {
        return redirect()
            ->route('login')
            ->with('error', $message);
    }
}
