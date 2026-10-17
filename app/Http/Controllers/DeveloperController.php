<?php

namespace App\Http\Controllers;

use App\Models\Technology;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DeveloperController extends Controller
{
    /**
     * Afficher l'annuaire public des développeurs.
     */
    /**
     * Afficher l'annuaire public des développeurs.
     */
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'technology' => [
                'nullable',
                'integer',
                'exists:technologies,id',
            ],
        ]);

        $search = trim($filters['search'] ?? '');
        $technologyId = $filters['technology'] ?? null;

        $developers = User::query()
            ->with([
                'profile',
                'technologies',
            ])
            ->whereHas('profile')

            // Recherche par nom de développeur
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'ilike', "%{$search}%");
            })

            // Filtrage par technologie
            ->when($technologyId !== null, function ($query) use ($technologyId) {
                $query->whereHas('technologies', function ($technologyQuery) use ($technologyId) {
                    $technologyQuery->where(
                        'technologies.id',
                        $technologyId
                    );
                });
            })

            ->orderBy('name')
            ->paginate(12)
            ->withQueryString()
            ->through(fn(User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'headline' => $user->profile->headline,
                'location' => $user->profile->location,

                'technologies_count' => $user->technologies->count(),

                'technologies' => $user->technologies
                    ->sortByDesc(
                        fn($technology) => (bool) $technology->pivot->is_primary
                    )
                    ->take(4)
                    ->values()
                    ->map(fn($technology) => [
                        'id' => $technology->id,
                        'name' => $technology->name,
                        'is_primary' => (bool) $technology->pivot->is_primary,
                    ]),
            ]);

        $technologies = Technology::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('developers/Index', [
            'developers' => $developers,

            'filters' => [
                'search' => $search,
                'technology' => $technologyId,
            ],

            'technologies' => $technologies,
        ]);
    }

    /**
     * Afficher publiquement le profil d'un développeur.
     */
    public function show(User $user): Response
    {
        $user->load([
            'profile',
            'technologies',
        ]);

        $profile = $user->profile;

        abort_if($profile === null, 404);

        $technologies = $user->technologies
            ->map(fn($technology) => [
                'id' => $technology->id,
                'name' => $technology->name,
                'category' => $technology->category,
                'skill_level' => $technology->pivot->skill_level,
                'years_experience' => $technology->pivot->years_experience,
                'is_primary' => (bool) $technology->pivot->is_primary,
            ])
            ->sortByDesc('is_primary')
            ->values();

        return Inertia::render('developers/Show', [
            'developer' => [
                'id' => $user->id,
                'name' => $user->name,
                'headline' => $profile->headline,
                'bio' => $profile->bio,
                'location' => $profile->location,
                'website' => $profile->website,
                'github_url' => $profile->github_url,
                'linkedin_url' => $profile->linkedin_url,
            ],
            'technologies' => $technologies,
        ]);
    }
}
