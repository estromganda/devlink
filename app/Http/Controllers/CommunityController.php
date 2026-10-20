<?php

namespace App\Http\Controllers;

use App\Models\Community;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;

class CommunityController extends Controller
{
    /**
     * Afficher l'annuaire public des communautés.
     */
    public function index(): Response
    {
        $communities = Community::query()
            ->with('creator:id,name')
            ->withCount('members')
            ->where('is_active', true)
            ->where('is_private', false)
            ->orderByDesc('created_at')
            ->paginate(12)
            ->through(fn(Community $community) => [
                'id' => $community->id,
                'name' => $community->name,
                'slug' => $community->slug,
                'description' => $community->description,
                'avatar_path' => $community->avatar_path,
                'cover_path' => $community->cover_path,
                'created_at' => $community->created_at,

                'creator' => [
                    'id' => $community->creator->id,
                    'name' => $community->creator->name,
                ],

                'members_count' => $community->members_count,
            ]);

        return Inertia::render('communities/Index', [
            'communities' => $communities,
        ]);
    }

    /**
     * Afficher les détails d'une communauté publique.
     */
    public function show(Request $request, string $slug): Response
    {
        $community = Community::query()
            ->with([
                'creator:id,name',
                'users' => fn($query) => $query
                    ->select('users.id', 'users.name')
                    ->orderBy('users.name'),
            ])
            ->withCount('members')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->where('is_private', false)
            ->firstOrFail();


        $isMember = $request->user()
            ? $community->users()
            ->where('users.id', $request->user()->id)
            ->exists()
            : false;

        return Inertia::render('communities/Show', [
            'community' => [
                'id' => $community->id,
                'creator_id' => $community->creator_id,
                'name' => $community->name,
                'slug' => $community->slug,
                'description' => $community->description,
                'avatar_path' => $community->avatar_path,
                'cover_path' => $community->cover_path,
                'created_at' => $community->created_at,
                'creator' => [
                    'id' => $community->creator->id,
                    'name' => $community->creator->name,
                ],
                'members_count' => $community->members_count,
                'members' => $community->users
                    ->map(fn($user) => [
                        'id' => $user->id,
                        'name' => $user->name,
                    ])
                    ->values(),
            ],
            'isMember' => $isMember
        ]);
    }

    /**
     * Afficher le formulaire de création d'une communauté.
     */
    public function create(): Response
    {
        return Inertia::render('communities/Create');
    }

    /**
     * Enregistrer une nouvelle communauté.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'description' => ['nullable', 'string', 'max:5000'],
            'is_private' => ['required', 'boolean'],
        ]);

        $baseSlug = Str::slug($validated['name']);

        if ($baseSlug === '') {
            return back()
                ->withErrors([
                    'name' => 'Le nom doit contenir des caractères permettant de créer un identifiant.',
                ])
                ->withInput();
        }

        $slug = $baseSlug;
        $suffix = 2;

        while (Community::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        $community = DB::transaction(function () use ($validated, $slug, $request) {
            $community = $request->user()
                ->createdCommunities()
                ->create([
                    'name' => $validated['name'],
                    'slug' => $slug,
                    'description' => $validated['description'] ?? null,
                    'is_private' => $validated['is_private'],
                    'is_active' => true,
                ]);

            $community->users()->attach($request->user()->id, [
                'role' => 'admin',
                'joined_at' => now(),
            ]);
            return $community;
        });

        return redirect()
            ->route('communities.show', ['slug' => $community->slug])
            ->with('success', 'Votre communauté a été créée avec succès.');
    }

    public function join(Request $request, string $slug): RedirectResponse
    {
        $community = Community::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->where('is_private', false)
            ->firstOrFail();

        $user = $request->user();

        DB::transaction(function () use ($community, $user) {
            $alreadyMember = $community->users()
                ->where('users.id', $user->id)
                ->exists();

            if (! $alreadyMember) {
                $community->users()->attach($user->id, [
                    'role' => 'member',
                    'joined_at' => now(),
                ]);
            }
        });

        return redirect()
            ->route('communities.show', $community->slug)
            ->with('success', 'Tu as rejoint la communauté avec succès.');
    }

    public function leave(Request $request, string $slug): RedirectResponse
    {
        $community = Community::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->where('is_private', false)
            ->firstOrFail();

        $user = $request->user();

        if ((int) $community->creator_id === (int) $user->id) {
            return back()->with(
                'error',
                'Le créateur ne peut pas quitter sa communauté. Transfère d’abord la propriété à un autre membre.'
            );
        }

        $community->users()->detach($user->id);

        return redirect()
            ->route('communities.show', $community->slug)
            ->with('success', 'Tu as quitté la communauté.');
    }
}
