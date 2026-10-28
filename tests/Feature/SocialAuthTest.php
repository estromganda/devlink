<?php

use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GithubProvider;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\AbstractProvider;
use Mockery;

beforeEach(function () {
    config([
        'services.google_auth.client_id' => 'test-google-client',
        'services.google_auth.client_secret' => 'test-google-secret',
        'services.google_auth.redirect' => 'http://localhost:8000/auth/google/callback',

        'services.github_auth.client_id' => 'test-github-client',
        'services.github_auth.client_secret' => 'test-github-secret',
        'services.github_auth.redirect' => 'http://localhost:8000/auth/github/login/callback',
    ]);
});

it('redirects to Google for social login', function () {
    $redirectResponse = redirect('https://accounts.google.com/o/oauth2/auth');

    $driver = Mockery::mock();
    $driver->shouldReceive('redirect')
        ->once()
        ->andReturn($redirectResponse);

    Socialite::shouldReceive('buildProvider')
        ->once()
        ->with(
            GoogleProvider::class,
            config('services.google_auth')
        )
        ->andReturn($driver);

    $this->get(route('auth.google.redirect'))
        ->assertRedirect('https://accounts.google.com/o/oauth2/auth');
});

it('redirects to GitHub for social login', function () {
    $redirectResponse = redirect('https://github.com/login/oauth/authorize');

    $driver = Mockery::mock();
    $driver->shouldReceive('scopes')
        ->once()
        ->with(['read:user', 'user:email'])
        ->andReturnSelf();

    $driver->shouldReceive('redirect')
        ->once()
        ->andReturn($redirectResponse);

    Socialite::shouldReceive('buildProvider')
        ->once()
        ->with(
            GithubProvider::class,
            config('services.github_auth')
        )
        ->andReturn($driver);

    $this->get(route('auth.github.login'))
        ->assertRedirect('https://github.com/login/oauth/authorize');
});

it('rejects an unsupported social provider', function () {
    $this->get('/auth/facebook/redirect')
        ->assertNotFound();
});

it('logs in an existing user through an already linked social account', function () {
    $user = User::factory()->create();

    SocialAccount::create([
        'user_id' => $user->id,
        'provider' => 'google',
        'provider_user_id' => 'google-user-123',
        'provider_email' => $user->email,
        'provider_name' => $user->name,
        'provider_avatar' => null,
    ]);

    $socialUser = Mockery::mock(
        \Laravel\Socialite\Contracts\User::class
    );

    $socialUser->shouldReceive('getId')
        ->andReturn('google-user-123');

    $driver = Mockery::mock();

    $driver->shouldReceive('user')
        ->once()
        ->andReturn($socialUser);

    Socialite::shouldReceive('buildProvider')
        ->once()
        ->with(
            \Laravel\Socialite\Two\GoogleProvider::class,
            config('services.google_auth')
        )
        ->andReturn($driver);

    $this->get(route('auth.google.callback'))
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
});

it('does not automatically link a social identity to an existing email account', function () {
    $user = User::factory()->create([
        'email' => 'existing@example.com',
    ]);

    $socialUser = (new \Laravel\Socialite\Two\User())
        ->setRaw([
            'id' => 'new-google-user-456',
            'email' => 'existing@example.com',
            'name' => 'Existing User',
            'email_verified' => true,
        ])
        ->map([
            'id' => 'new-google-user-456',
            'email' => 'existing@example.com',
            'name' => 'Existing User',
            'avatar' => null,
        ]);

    $driver = Mockery::mock();

    $driver->shouldReceive('user')
        ->once()
        ->andReturn($socialUser);

    Socialite::shouldReceive('buildProvider')
        ->once()
        ->with(
            \Laravel\Socialite\Two\GoogleProvider::class,
            config('services.google_auth')
        )
        ->andReturn($driver);

    $this->get(route('auth.google.callback'))
        ->assertRedirect(route('login'))
        ->assertSessionHas('error');

    $this->assertGuest();

    expect(
        SocialAccount::query()
            ->where('provider', 'google')
            ->where('provider_user_id', 'new-google-user-456')
            ->exists()
    )->toBeFalse();

    expect($user->fresh())->not->toBeNull();
});
