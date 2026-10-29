<?php

namespace App\Http\Controllers;

use App\Models\Community;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
            ->firstOrFail();

        $isMember = $request->user()
            ? $community->users()
            ->where('users.id', $request->user()->id)
            ->exists()
            : false;

        if ($community->is_private && !$isMember) {
            abort(403, "Not found");
        }

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

            'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'cover' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
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

        // Enregistrer les images sur le disque public.
        $avatarPath = null;
        $coverPath = null;

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')
                ->store('communities/avatars', 'public');
        }

        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')
                ->store('communities/covers', 'public');
        }

        try {
            $community = DB::transaction(function () use (
                $validated,
                $slug,
                $request,
                $avatarPath,
                $coverPath
            ) {
                $community = $request->user()
                    ->createdCommunities()
                    ->create([
                        'name' => $validated['name'],
                        'slug' => $slug,
                        'description' => $validated['description'] ?? null,
                        'avatar_path' => $avatarPath,
                        'cover_path' => $coverPath,
                        'is_private' => $validated['is_private'],
                        'is_active' => true,
                    ]);

                // Ajouter automatiquement le créateur comme administrateur.
                $community->users()->attach($request->user()->id, [
                    'role' => 'admin',
                    'joined_at' => now(),
                ]);

                return $community;
            });
        } catch (\Throwable $exception) {
            // Nettoyer les fichiers si la création échoue.
            if ($avatarPath) {
                Storage::disk('public')->delete($avatarPath);
            }

            if ($coverPath) {
                Storage::disk('public')->delete($coverPath);
            }

            throw $exception;
        }

        return redirect()
            ->route('communities.show', ['slug' => $community->slug])
            ->with('success', 'Votre communauté a été créée avec succès.');
    }

    /**
     * Afficher le formulaire de modification d'une communauté.
     */
    public function edit(Request $request, string $slug): Response
    {
        $community = Community::where('slug', $slug)->firstOrFail();

        abort_unless(
            (int) $community->creator_id === (int) $request->user()->id,
            403,
            'Tu n’es pas autorisé à modifier cette communauté.'
        );

        return Inertia::render('communities/Edit', [
            'community' => [
                'id' => $community->id,
                'name' => $community->name,
                'slug' => $community->slug,
                'description' => $community->description,
                'avatar_path' => $community->avatar_path,
                'cover_path' => $community->cover_path,
                'is_private' => $community->is_private,
            ],
        ]);
    }

    /**
     * Enregistrer les modifications d'une communauté.
     */
    public function update(Request $request, string $slug): RedirectResponse
    {
        $community = Community::where('slug', $slug)->firstOrFail();

        abort_unless(
            (int) $community->creator_id === (int) $request->user()->id,
            403,
            'Tu n’es pas autorisé à modifier cette communauté.'
        );

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'description' => ['nullable', 'string', 'max:5000'],
            'is_private' => ['required', 'boolean'],

            'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'cover' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
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

        while (
            Community::where('slug', $slug)
            ->where('id', '!=', $community->id)
            ->exists()
        ) {
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        $oldAvatar = $community->avatar_path;
        $oldCover = $community->cover_path;

        $avatarPath = $oldAvatar;
        $coverPath = $oldCover;

        DB::transaction(function () use (
            $request,
            $community,
            $validated,
            $slug,
            &$avatarPath,
            &$coverPath
        ) {
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')
                    ->store('communities/avatars', 'public');
            }

            if ($request->hasFile('cover')) {
                $coverPath = $request->file('cover')
                    ->store('communities/covers', 'public');
            }

            $community->update([
                'name' => $validated['name'],
                'slug' => $slug,
                'description' => $validated['description'] ?? null,
                'is_private' => $validated['is_private'],
                'avatar_path' => $avatarPath,
                'cover_path' => $coverPath,
            ]);
        });

        // Supprimer les anciennes images uniquement après l'enregistrement.
        if (
            $request->hasFile('avatar')
            && $oldAvatar
            && $oldAvatar !== $avatarPath
        ) {
            Storage::disk('public')->delete($oldAvatar);
        }

        if (
            $request->hasFile('cover')
            && $oldCover
            && $oldCover !== $coverPath
        ) {
            Storage::disk('public')->delete($oldCover);
        }

        return redirect()
            ->route('communities.show', ['slug' => $community->slug])
            ->with('success', 'La communauté a été modifiée avec succès.');
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
