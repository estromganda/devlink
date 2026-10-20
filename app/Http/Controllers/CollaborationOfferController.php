<?php

namespace App\Http\Controllers;

use App\Models\CollaborationOffer;
use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CollaborationOfferController extends Controller
{
    /**
     * Envoyer une demande de collaboration.
     */
    public function store(Request $request, string $slug): RedirectResponse
    {
        $project = Project::query()
            ->where('slug', $slug)
            ->whereNull('deleted_at')
            ->firstOrFail();

        abort_unless($project->visibility === 'public', 404);

        abort_unless(
            $project->looking_for_collaborators,
            403,
            'Ce projet ne recherche pas actuellement de collaborateurs.'
        );

        abort_unless(
            $project->owner_id !== $request->user()->id,
            403,
            'Vous ne pouvez pas envoyer une demande à votre propre projet.'
        );

        $alreadyMember = $project->members()
            ->where('user_id', $request->user()->id)
            ->exists();

        abort_if(
            $alreadyMember,
            403,
            'Vous êtes déjà membre de ce projet.'
        );

        $validated = $request->validate([
            'message' => ['required', 'string', 'min:10', 'max:3000'],
            'proposed_role' => ['nullable', 'string', 'max:100'],
        ]);

        $existingOffer = CollaborationOffer::query()
            ->where('project_id', $project->id)
            ->where('user_id', $request->user()->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->exists();

        if ($existingOffer) {
            return back()->withErrors([
                'message' => 'Vous avez déjà une demande en attente ou êtes déjà accepté dans ce projet.',
            ]);
        }

        CollaborationOffer::create([
            'project_id' => $project->id,
            'user_id' => $request->user()->id,
            'message' => $validated['message'],
            'proposed_role' => $validated['proposed_role'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Votre demande de collaboration a été envoyée.');
    }

    /**
     * Voir les demandes envoyées par le développeur connecté.
     */
    public function mine(Request $request): Response
    {
        $offers = CollaborationOffer::query()
            ->where('user_id', $request->user()->id)
            ->with([
                'project:id,owner_id,name,slug,short_description,status,visibility',
                'project.owner:id,name',
                'reviewer:id,name',
            ])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('collaborations/Mine', [
            'offers' => $offers,
        ]);
    }

    /**
     * Voir les demandes reçues pour un projet.
     * Seul le propriétaire du projet peut consulter cette page.
     */
    public function received(string $slug): Response
    {
        $project = Project::query()
            ->where('slug', $slug)
            ->whereNull('deleted_at')
            ->firstOrFail();

        $this->authorizeOwner($project);

        $offers = CollaborationOffer::query()
            ->where('project_id', $project->id)
            ->with([
                'user:id,name',
                'user.profile',
                'reviewer:id,name',
            ])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('projects/Requests', [
            'project' => $project,
            'offers' => $offers,
        ]);
    }

    /**
     * Accepter ou refuser une demande.
     */
    public function update(
        Request $request,
        CollaborationOffer $offer
    ): RedirectResponse {
        $offer->load('project');

        $this->authorizeOwner($offer->project);

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in(['accepted', 'rejected']),
            ],
        ]);

        if ($offer->status !== 'pending') {
            return back()->withErrors([
                'status' => 'Cette demande a déjà été traitée.',
            ]);
        }

        DB::transaction(function () use ($offer, $validated, $request) {
            $offer->refresh();

            if ($offer->status !== 'pending') {
                abort(409, 'Cette demande a déjà été traitée.');
            }

            $offer->update([
                'status' => $validated['status'],
                'reviewed_at' => now(),
                'reviewed_by' => $request->user()->id,
            ]);

            if ($validated['status'] === 'accepted') {
                ProjectMember::firstOrCreate(
                    [
                        'project_id' => $offer->project_id,
                        'user_id' => $offer->user_id,
                    ],
                    [
                        'role' => $offer->proposed_role ?: 'contributor',
                        'joined_at' => now(),
                    ]
                );
            }
        });

        $message = $validated['status'] === 'accepted'
            ? 'La demande a été acceptée. Le développeur a été ajouté au projet.'
            : 'La demande a été refusée.';

        return back()->with('success', $message);
    }

    /**
     * Retirer une demande envoyée.
     */
    public function withdraw(
        Request $request,
        CollaborationOffer $offer
    ): RedirectResponse {
        abort_unless(
            $offer->user_id === $request->user()->id,
            403
        );

        if ($offer->status !== 'pending') {
            return back()->withErrors([
                'status' => 'Seules les demandes en attente peuvent être retirées.',
            ]);
        }

        $offer->update([
            'status' => 'withdrawn',
        ]);

        return back()->with('success', 'Votre demande a été retirée.');
    }

    /**
     * Vérifier que l'utilisateur connecté est propriétaire du projet.
     */
    private function authorizeOwner(Project $project): void
    {
        abort_unless(
            $project->owner_id === auth()->id(),
            403,
            'Seul le propriétaire du projet peut effectuer cette action.'
        );
    }
}
