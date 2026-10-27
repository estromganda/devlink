<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\ProjectTask;
use App\Models\ProjectTaskActivity;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ProjectTaskController extends Controller
{
    /**
     * Display the tasks of a project.
     */
    public function index(Request $request, Project $project): Response
    {
        $this->authorizeProjectAccess($request, $project);
        $user = $request->user();
        $isOwner = (int) $project->owner_id === (int) $user->id;

        $isMember = $project->members()
            ->where('user_id', $user->id)
            ->exists();

        $tasks = $project->tasks()
            ->with([
                'creator:id,name',
                'assignee:id,name',
            ]);

        if (! $isOwner && ! $isMember) {
            $tasks->where('assigned_to', $user->id);
        }

        if ($request->filled('status')) {
            $tasks->where('status', $request->input('status'));
        }

        if ($request->filled('priority')) {
            $tasks->where('priority', $request->input('priority'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');

            $tasks->where(function ($subquery) use ($search) {
                $subquery
                    ->where('title', 'ilike', "%{$search}%")
                    ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        $tasks =  $tasks->orderByRaw("
                CASE priority
                    WHEN 'urgent' THEN 1
                    WHEN 'high' THEN 2
                    WHEN 'normal' THEN 3
                    WHEN 'low' THEN 4
                    ELSE 5
                END
            ")
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $teamMembers = User::query()
            ->whereIn(
                'id',
                ProjectMember::query()
                    ->where('project_id', $project->id)
                    ->select('user_id')
            )
            ->orWhere('id', $project->owner_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        // Activités
        $activityQuery = ProjectTaskActivity::query()
            ->with(['user:id,name', 'task:id,title,project_id,assigned_to'])
            ->whereHas('task', function ($query) use ($project) {
                $query->where('project_id', $project->id);
            });

        // Un utilisateur qui n'est ni propriétaire ni membre
        // ne voit que l'activité de ses propres tâches assignées.
        if (! $isOwner && ! $isMember) {
            $activityQuery->whereHas('task', function ($query) use ($user) {
                $query->where('assigned_to', $user->id);
            });
        }

        $activities = $activityQuery
            ->latest()
            ->limit(30)
            ->get()
            ->map(fn(ProjectTaskActivity $activity) => [
                'id' => $activity->id,
                'action' => $activity->action,
                'description' => $activity->description,
                'old_values' => $activity->old_values,
                'new_values' => $activity->new_values,
                'created_at' => $activity->created_at?->toIso8601String(),
                'user' => $activity->user ? [
                    'id' => $activity->user->id,
                    'name' => $activity->user->name,
                ] : null,
                'task' => $activity->task ? [
                    'id' => $activity->task->id,
                    'title' => $activity->task->title,
                ] : null,
            ])
            ->values();

        return Inertia::render('projects/Tasks', [
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
                'slug' => $project->slug,
                'owner_id' => $project->owner_id,
            ],
            'tasks' => $tasks,
            'teamMembers' => $teamMembers,
            'canManageTasks' => $this->canManageTasks($request, $project),
            'filters' => $request->only(['status', 'priority', 'search']),
            'activities' => $activities
        ]);
    }

    /**
     * Create a task.
     */
    public function store(Request $request, Project $project): RedirectResponse
    {
        $this->authorizeProjectAccess($request, $project);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:20000'],
            'status' => [
                'sometimes',
                Rule::in(['todo', 'in_progress', 'in_review', 'done']),
            ],
            'priority' => [
                'sometimes',
                Rule::in(['low', 'normal', 'high', 'urgent']),
            ],
            'assigned_to' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id'),
            ],
            'due_date' => ['nullable', 'date'],
        ]);

        $this->ensureAssigneeBelongsToProject(
            $project,
            $validated['assigned_to'] ?? null
        );

        $status = $validated['status'] ?? 'todo';

        $task = $project->tasks()->create([
            ...$validated,
            'created_by' => $request->user()->id,
            'status' => $status,
            'priority' => $validated['priority'] ?? 'normal',
            'completed_at' => $status === 'done' ? now() : null,
        ]);

        $this->logTaskActivity(
            $task,
            (int) $request->user()->id,
            'created',
            [],
            [
                'title' => $task->title,
                'status' => $task->status,
                'priority' => $task->priority,
                'assigned_to' => $task->assigned_to,
            ],
            'a créé la tâche « ' . $task->title . ' ».'
        );

        return back()->with('success', 'La tâche a été créée.');
    }

    /**
     * Update a task.
     */
    public function update(Request $request, Project $project, ProjectTask $task): RedirectResponse
    {
        $this->authorizeTaskAccess($request, $project, $task);

        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:200'],
            'description' => ['sometimes', 'nullable', 'string', 'max:20000'],
            'status' => [
                'sometimes',
                Rule::in(['todo', 'in_progress', 'in_review', 'done']),
            ],
            'priority' => [
                'sometimes',
                Rule::in(['low', 'normal', 'high', 'urgent']),
            ],
            'assigned_to' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists('users', 'id'),
            ],
            'due_date' => ['sometimes', 'nullable', 'date'],
        ]);

        if (array_key_exists('assigned_to', $validated)) {
            $this->ensureAssigneeBelongsToProject(
                $project,
                $validated['assigned_to']
            );
        }

        if (array_key_exists('status', $validated)) {
            $validated['completed_at'] =
                $validated['status'] === 'done'
                ? ($task->completed_at ?? now())
                : null;
        }


        $task->completed_at = $validated['status'] === 'done'
            ? now()
            : null;

        $oldValues = $task->only([
            'title',
            'description',
            'status',
            'priority',
            'assigned_to',
            'due_date',
        ]);
        $task->update($validated);
        $changedOldValues = [];
        $changedNewValues = [];

        foreach ($oldValues as $field => $oldValue) {
            $newValue = $newValues[$field] ?? null;

            // Comparaison des dates et des valeurs scalaires.
            if ((string) ($oldValue ?? '') !== (string) ($newValue ?? '')) {
                $changedOldValues[$field] = $oldValue;
                $changedNewValues[$field] = $newValue;
            }
        }

        if ($changedNewValues !== []) {
            $actions = [];

            if (array_key_exists('status', $changedNewValues)) {
                $actions[] = 'status_changed';
            }

            if (array_key_exists('priority', $changedNewValues)) {
                $actions[] = 'priority_changed';
            }

            if (array_key_exists('assigned_to', $changedNewValues)) {
                $actions[] = 'assignee_changed';
            }

            if (
                array_key_exists('title', $changedNewValues)
                || array_key_exists('description', $changedNewValues)
            ) {
                $actions[] = 'details_updated';
            }

            $this->logTaskActivity(
                $task,
                (int) $request->user()->id,
                implode(',', $actions) ?: 'updated',
                $changedOldValues,
                $changedNewValues,
                'a modifié la tâche « ' . $task->title . ' ».'
            );
        }

        return back()->with('success', 'La tâche a été mise à jour.');
    }

    /**
     * Delete a task.
     */
    public function destroy(
        Request $request,
        Project $project,
        ProjectTask $task
    ): RedirectResponse {
        $this->authorizeProjectAccess($request, $project);

        abort_unless(
            $task->project_id === $project->id,
            404
        );

        abort_unless(
            $project->owner_id === $request->user()->id,
            403,
            'Seul le propriétaire peut supprimer une tâche.'
        );

        $task->delete();

        return back()->with('success', 'La tâche a été supprimée.');
    }

    /**
     * Verify access to the project.
     */
    private function authorizeProjectAccess(
        Request $request,
        Project $project
    ): void {
        $user = $request->user();

        $isOwner = $project->owner_id === $user->id;

        $isMember = $project->members()
            ->where('user_id', $user->id)
            ->exists();

        $hasAssignedTasks = $project->tasks()
            ->where('assigned_to', $user->id)
            ->exists();

        abort_unless($isOwner || $isMember || $hasAssignedTasks, 403);
    }

    /**
     * Verify access to a task and its project.
     */
    private function authorizeTaskAccess(
        Request $request,
        Project $project,
        ProjectTask $task
    ): void {
        $this->authorizeProjectAccess($request, $project);

        abort_unless($task->project_id === $project->id, 404);

        $user = $request->user();

        $isOwner = $project->owner_id === $user->id;
        $isCreator = $task->created_by === $user->id;
        $isAssignee = $task->assigned_to === $user->id;

        abort_unless($isOwner || $isCreator || $isAssignee, 403);
    }

    /**
     * Verify that the assignee belongs to the project team.
     */
    private function ensureAssigneeBelongsToProject(
        Project $project,
        ?int $userId
    ): void {
        if ($userId === null) {
            return;
        }

        $isOwner = $project->owner_id === $userId;

        $isMember = $project->members()
            ->where('user_id', $userId)
            ->exists();

        abort_unless(
            $isOwner || $isMember,
            422,
            'La personne assignée doit appartenir à l’équipe du projet.'
        );
    }

    /**
     * Determine whether the current user can manage tasks.
     */
    private function canManageTasks(Request $request, Project $project): bool
    {
        $user = $request->user();

        if ($project->owner_id === $user->id) {
            return true;
        }

        return $project->members()
            ->where('user_id', $user->id)
            ->whereIn('role', ['manager', 'maintainer'])
            ->exists();
    }

    private function logTaskActivity(
        ProjectTask $task,
        ?int $userId,
        string $action,
        array $oldValues = [],
        array $newValues = [],
        ?string $description = null
    ): void {
        ProjectTaskActivity::create([
            'project_task_id' => $task->id,
            'user_id' => $userId,
            'action' => $action,
            'old_values' => $oldValues ?: null,
            'new_values' => $newValues ?: null,
            'description' => $description,
        ]);
    }
}
