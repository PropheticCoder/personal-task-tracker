<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\Task;

class DashboardController extends Controller
{
    public function index()
    {
        $workspace = app('current_workspace');

        // Clients + contacts for the "who" dropdowns — workspace scoped
        $clients = Client::with('people')->where('workspace_id', $workspace->id)->get()->map(fn($c) => [
            'id'     => $c->id,
            'name'   => $c->name,
            'people' => $c->people->map(fn($p) => [
                'id'   => $p->id,
                'name' => $p->name,
                'role' => $p->role,
            ])->values(),
        ]);

        // Add an "Internal" group for contacts with no client
        $internalPeople = \App\Models\Person::whereNull('client_id')->get()
            ->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'role' => $p->role])
            ->values();

        if ($internalPeople->isNotEmpty()) {
            $clients->push(['id' => null, 'name' => 'Internal', 'people' => $internalPeople]);
        }

        // Active/paused projects for sidebar + filter strip — scoped to workspace
        $projects = Project::with('client')
            ->where('workspace_id', $workspace->id)
            ->whereIn('status', ['active', 'paused'])
            ->orderBy('name')
            ->get()
            ->map(fn($p) => [
                'id'     => $p->id,
                'name'   => $p->name,
                'client' => $p->client?->name,
                'color'  => $p->color ?? '#7c7c98',
                'status' => $p->status,
            ]);

        // working_on + waiting_on tasks — scoped to workspace via project
        $projectIds = $projects->pluck('id');
        $tasks = Task::with(['project', 'blocker.person', 'commitments.person'])
            ->whereIn('project_id', $projectIds)
            ->whereIn('status', ['working_on', 'waiting_on'])
            ->get()
            ->map(function ($t) {
                $blocker    = $t->blocker;
                $commitment = $t->commitments->first();

                return [
                    'id'         => $t->id,
                    'title'      => $t->title,
                    'project_id' => $t->project_id,
                    'priority'   => $t->priority,
                    'status'     => 'working',

                    'blocked_by_person' => $t->status === 'waiting_on'
                        ? ($blocker?->person?->name ?? '—')
                        : $blocker?->person?->name,
                    'blocked_by_what'   => $blocker?->description,
                    'blocked_overdue'   => $blocker ? $blocker->isOverdue() : false,
                    'blocker_dep_id'    => $blocker?->id,

                    'needed_by_person'  => $commitment?->person?->name,
                    'needed_by_date'    => $commitment?->needed_by?->format('Y-m-d'),
                    'needed_by_what'    => $commitment?->description,
                    'commitment_dep_id' => $commitment?->id,
                ];
            })->values();

        // Recent meetings — last 5 across all workspace projects
        $recentMeetings = \App\Models\Meeting::whereIn('project_id', $projectIds)
            ->with('project')
            ->withCount('tasks')
            ->orderByDesc('held_at')
            ->limit(5)
            ->get()
            ->map(fn($m) => [
                'id'         => $m->id,
                'title'      => $m->title,
                'held_at'    => $m->held_at->format('d M Y'),
                'held_at_rel'=> $m->held_at->diffForHumans(),
                'project'    => $m->project?->name,
                'project_id' => $m->project_id,
                'task_count' => $m->tasks_count,
                'url'        => route('meetings.show', $m->id),
            ])->values();

        // todo tasks — for "Next Up" section on dashboard
        $todoTasks = Task::whereIn('project_id', $projectIds)
            ->where('status', 'todo')
            ->orderByRaw("FIELD(priority,'high','med','low',NULL)")
            ->orderBy('needed_by')
            ->get()
            ->map(fn($t) => [
                'id'         => $t->id,
                'title'      => $t->title,
                'project_id' => $t->project_id,
                'priority'   => $t->priority,
                'needed_by'  => $t->needed_by?->format('Y-m-d'),
            ])->values();

        return view('dashboard', compact('clients', 'projects', 'tasks', 'todoTasks', 'recentMeetings'));
    }
}
