<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DeveloperProfileController extends Controller
{
    /**
     * Afficher le profil du développeur connecté.
     */
    public function edit(Request $request): Response
    {
        $profile = $request->user()->profile;

        return Inertia::render('DeveloperProfile', [
            'profile' => $profile ? [
                'headline' => $profile->headline,
                'bio' => $profile->bio,
                'location' => $profile->location,
                'website' => $profile->website,
                'github_url' => $profile->github_url,
                'linkedin_url' => $profile->linkedin_url,
            ] : null,
        ]);
    }

    /**
     * Enregistrer les informations du profil développeur.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'headline' => ['nullable', 'string', 'max:150'],
            'bio' => ['nullable', 'string', 'max:5000'],
            'location' => ['nullable', 'string', 'max:150'],
            'website' => ['nullable', 'url', 'max:255'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
        ]);

        $request->user()
            ->profile()
            ->updateOrCreate([], $validated);

        return to_route('developer-profile.edit')
            ->with('success', 'Profil développeur enregistré avec succès.');
    }
}
