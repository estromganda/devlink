<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ProjectTeamController extends Controller
{
    /**
     * Display a project's team.
     */
    public function index(Project $project): Response
    {
        $user = request()->user();

        $isOwner = $project->owner_id === $user->id;

        $isMember = $project->members()
            ->where('user_id', $user->id)
            ->exists();

        abort_unless(
            $isOwner || $isMember || $project->visibility === 'public',
            403
        );

        $members = $project->members()
            ->with(['user.profile'])
            ->orderBy('joined_at')
            ->get()
            ->map(fn(ProjectMember $member) => [
                'id' => $member->user->id,
                'member_id' => $member->id,
                'name' => $member->user->name,
                'email' => $isOwner ? $member->user->email : null,
                'profile' => $member->user->profile,
                'role' => $member->role,
                'joined_at' => $member->joined_at,
            ]);
        $project->loadCount('members');
        return Inertia::render('projects/Team', [
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
                'slug' => $project->slug,
                'owner_id' => $project->owner_id,
            ],
            'members' => $members,
            'isOwner' => $isOwner,
            'roles' => [
                ['value' => 'contributor', 'label' => 'Contributeur'],
                ['value' => 'maintainer', 'label' => 'Mainteneur'],
                ['value' => 'manager', 'label' => 'Responsable'],
            ],
        ]);
    }

    /**
     * Add a user to the project team.
     */
    public function store(Request $request, Project $project): RedirectResponse
    {
        $this->authorizeOwner($request, $project);

        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'role' => [
                'required',
                Rule::in(['contributor', 'maintainer', 'manager']),
            ],
        ]);

        $member = User::where('email', $validated['email'])->firstOrFail();

        if ($member->id === $project->owner_id) {
            return back()->withErrors([
                'email' => 'Le propriétaire du projet ne peut pas être ajouté comme membre.',
            ]);
        }

        $alreadyMember = $project->members()
            ->where('user_id', $member->id)
            ->exists();

        if ($alreadyMember) {
            return back()->withErrors([
                'email' => 'Ce développeur fait déjà partie de l’équipe.',
            ]);
        }

        DB::transaction(function () use ($project, $member, $validated) {
            ProjectMember::create([
                'project_id' => $project->id,
                'user_id' => $member->id,
                'role' => $validated['role'],
                'joined_at' => now(),
            ]);
        });

        return back()->with('success', 'Le membre a été ajouté à l’équipe.');
    }

    /**
     * Update a team member's role.
     */
    public function update(
        Request $request,
        Project $project,
        ProjectMember $member
    ): RedirectResponse {
        $this->authorizeOwner($request, $project);
        $this->ensureMemberBelongsToProject($project, $member);

        $validated = $request->validate([
            'role' => [
                'required',
                Rule::in(['contributor', 'maintainer', 'manager']),
            ],
        ]);

        $member->update([
            'role' => $validated['role'],
        ]);

        return back()->with('success', 'Le rôle du membre a été modifié.');
    }

    /**
     * Remove a member from the project team.
     */
    public function destroy(
        Request $request,
        Project $project,
        ProjectMember $member
    ): RedirectResponse {
        $this->authorizeOwner($request, $project);
        $this->ensureMemberBelongsToProject($project, $member);

        $member->delete();

        return back()->with('success', 'Le membre a été retiré de l’équipe.');
    }

    /**
     * Only the project owner can manage the team.
     */
    private function authorizeOwner(Request $request, Project $project): void
    {
        abort_unless(
            $request->user()
                && $project->owner_id === $request->user()->id,
            403,
            'Seul le propriétaire du projet peut gérer son équipe.'
        );
    }

    /**
     * Ensure that the requested member belongs to this project.
     */
    private function ensureMemberBelongsToProject(
        Project $project,
        ProjectMember $member
    ): void {
        abort_unless(
            $member->project_id === $project->id,
            404
        );
    }
}
