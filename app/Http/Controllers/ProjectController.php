<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    /**
     * Catalogue public des projets.
     */
    public function index(Request $request): Response
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $search = trim($validated['search'] ?? '');

        $projects = Project::query()
            ->where('visibility', 'public')
            ->with('owner:id,name')
            ->withCount('members')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subquery) use ($search) {
                    $subquery
                        ->where('name', 'ilike', "%{$search}%")
                        ->orWhere(
                            'short_description',
                            'ilike',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'description',
                            'ilike',
                            "%{$search}%"
                        );
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('projects/Index', [
            'projects' => $projects,
            'filters' => ['search' => $search],
        ]);
    }

    /**
     * Formulaire de création.
     */
    public function create(): Response
    {
        return Inertia::render('projects/Create');
    }

    /**
     * Enregistrer un projet.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'short_description' => ['nullable', 'string', 'max:1000'],
            'description' => ['required', 'string', 'max:20000'],
            'status' => [
                'required',
                Rule::in([
                    'planning',
                    'in_progress',
                    'completed',
                    'paused',
                ]),
            ],
            'visibility' => [
                'required',
                Rule::in(['public', 'private']),
            ],
            'repository_url' => [
                'nullable',
                'url',
                'max:255',
            ],
            'demo_url' => [
                'nullable',
                'url',
                'max:255',
            ],
            'cover' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
            'looking_for_collaborators' => ['sometimes', 'boolean'],
        ]);

        $slug = $this->uniqueSlug($validated['name']);

        $coverPath = null;

        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')
                ->store('projects/covers', 'public');
        }

        try {
            $project = DB::transaction(function () use (
                $request,
                $validated,
                $slug,
                $coverPath
            ) {
                $project = Project::create([
                    'owner_id' => $request->user()->id,
                    'name' => $validated['name'],
                    'slug' => $slug,
                    'short_description' => $validated['short_description'] ?? null,
                    'description' => $validated['description'],
                    'status' => $validated['status'],
                    'visibility' => $validated['visibility'],
                    'repository_url' => $validated['repository_url'] ?? null,
                    'demo_url' => $validated['demo_url'] ?? null,
                    'cover_path' => $coverPath,
                    'looking_for_collaborators' => $request->boolean(
                        'looking_for_collaborators'
                    ),
                ]);

                $project->members()->create([
                    'user_id' => $request->user()->id,
                    'role' => 'owner',
                    'joined_at' => now(),
                ]);

                return $project;
            });
        } catch (\Throwable $exception) {
            if ($coverPath) {
                Storage::disk('public')->delete($coverPath);
            }

            throw $exception;
        }

        return redirect()
            ->route('projects.show', $project->slug)
            ->with('success', 'Ton projet a été créé avec succès.');
    }

    /**
     * Afficher un projet public ou un projet privé autorisé.
     */
    public function show(Request $request, string $slug): Response
    {
        $project = Project::query()
            ->where('slug', $slug)
            ->with('owner:id,name')
            ->withCount('members')
            ->firstOrFail();

        $user = $request->user();

        $user = request()->user();

        $canApply = false;
        $existingOfferStatus = null;

        if ($user) {
            $isOwner = $project->owner_id === $user->id;

            $isMember = $project->members()
                ->where('user_id', $user->id)
                ->exists();

            $existingOffer = \App\Models\CollaborationOffer::query()
                ->where('project_id', $project->id)
                ->where('user_id', $user->id)
                ->whereIn('status', ['pending', 'accepted'])
                ->latest()
                ->first();

            $existingOfferStatus = $existingOffer?->status;

            $canApply =
                $project->visibility === 'public'
                && $project->looking_for_collaborators
                && ! $isOwner
                && ! $isMember
                && ! $existingOffer;
        }

        if ($project->visibility !== 'public') {
            abort_unless(
                $user && (
                    (int) $project->owner_id === (int) $user->id
                    || $project->members()
                    ->where('user_id', $user->id)
                    ->exists()
                ),
                404
            );
        }

        $repositories = ProjectRepository::query()
            ->where('project_id', $project->id)
            ->where('is_active', true)
            ->orderBy('created_at')
            ->get([
                'id',
                'provider',
                'full_name',
                'name',
                'description',
                'repository_url',
                'default_branch',
                'is_private',
                'connected_by',
                'last_synced_at',
                'created_at',
            ]);

        return Inertia::render('projects/Show', [
            'project' => $project,
            'canEdit' => $user
                && (int) $project->owner_id === (int) $user->id,
            'canApply' => $canApply,
            'existingOfferStatus' => $existingOfferStatus,
            'repositories' => $repositories,
        ]);
    }

    /**
     * Liste des projets appartenant à l'utilisateur connecté.
     */
    public function mine(Request $request): Response
    {
        $projects = Project::query()
            ->where('owner_id', $request->user()->id)
            ->with('owner:id,name')
            ->withCount('members')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('projects/Mine', [
            'projects' => $projects,
        ]);
    }

    /**
     * Formulaire de modification.
     */
    public function edit(Request $request, string $slug): Response
    {
        $project = Project::where('slug', $slug)->firstOrFail();

        $this->authorizeOwner($request, $project);

        return Inertia::render('projects/Edit', [
            'project' => $project,
        ]);
    }

    /**
     * Mettre à jour un projet.
     */
    public function update(Request $request, string $slug): RedirectResponse
    {
        $project = Project::where('slug', $slug)->firstOrFail();

        $this->authorizeOwner($request, $project);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'short_description' => ['nullable', 'string', 'max:1000'],
            'description' => ['required', 'string', 'max:20000'],
            'status' => [
                'required',
                Rule::in([
                    'planning',
                    'in_progress',
                    'completed',
                    'paused',
                ]),
            ],
            'visibility' => [
                'required',
                Rule::in(['public', 'private']),
            ],
            'repository_url' => ['nullable', 'url', 'max:255'],
            'demo_url' => ['nullable', 'url', 'max:255'],
            'cover' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
            'remove_cover' => ['sometimes', 'boolean'],
            'looking_for_collaborators' => ['sometimes', 'boolean'],
        ]);

        $newSlug = Str::slug($validated['name']);

        if ($newSlug === '') {
            $newSlug = 'projet';
        }

        $slugExists = Project::query()
            ->where('slug', $newSlug)
            ->where('id', '!=', $project->id)
            ->exists();

        if ($slugExists) {
            $newSlug .= '-' . $project->id;
        }

        $oldCover = $project->cover_path;
        $newCover = null;

        if ($request->hasFile('cover')) {
            $newCover = $request->file('cover')
                ->store('projects/covers', 'public');
        }

        $removeCover = $request->boolean('remove_cover');

        try {
            DB::transaction(function () use (
                $project,
                $validated,
                $newSlug,
                $newCover,
                $removeCover,
                $request
            ) {
                $project->update([
                    'name' => $validated['name'],
                    'slug' => $newSlug,
                    'short_description' => $validated['short_description'] ?? null,
                    'description' => $validated['description'],
                    'status' => $validated['status'],
                    'visibility' => $validated['visibility'],
                    'repository_url' => $validated['repository_url'] ?? null,
                    'demo_url' => $validated['demo_url'] ?? null,
                    'cover_path' => $newCover
                        ?? ($removeCover ? null : $project->cover_path),
                    'looking_for_collaborators' => $request->boolean(
                        'looking_for_collaborators'
                    ),
                ]);
            });
        } catch (\Throwable $exception) {
            if ($newCover) {
                Storage::disk('public')->delete($newCover);
            }

            throw $exception;
        }

        if ($oldCover && ($newCover || $removeCover)) {
            Storage::disk('public')->delete($oldCover);
        }

        return redirect()
            ->route('projects.show', $project->fresh()->slug)
            ->with('success', 'Ton projet a été mis à jour.');
    }

    /**
     * Supprimer logiquement un projet.
     */
    public function destroy(
        Request $request,
        string $slug
    ): RedirectResponse {
        $project = Project::where('slug', $slug)->firstOrFail();

        $this->authorizeOwner($request, $project);

        $project->delete();

        return redirect()
            ->route('projects.mine')
            ->with('success', 'Ton projet a été supprimé.');
    }

    /**
     * Vérifier que l'utilisateur connecté est propriétaire.
     */
    private function authorizeOwner(
        Request $request,
        Project $project
    ): void {
        abort_unless(
            (int) $project->owner_id === (int) $request->user()->id,
            403,
            'Tu ne peux pas modifier ce projet.'
        );
    }

    /**
     * Créer un slug unique.
     */
    private function uniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name);

        if ($baseSlug === '') {
            $baseSlug = 'projet';
        }

        $slug = $baseSlug;
        $counter = 2;

        while (Project::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function detachRepository(Request $request, string $slug, ProjectRepository $repository): RedirectResponse
    {
        $project = Project::query()
            ->where('slug', $slug)
            ->where('owner_id', $request->user()->id)
            ->firstOrFail();

        abort_unless(
            $repository->project_id === $project->id,
            404
        );

        $repository->delete();

        return to_route('projects.show', $project->slug)
            ->with('success', 'Le dépôt a été retiré du projet DevLink.');
    }
}
