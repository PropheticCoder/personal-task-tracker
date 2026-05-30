<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Carbon;

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

                // JS column logic:
                //   status='working'  → Working On column (no blocker dep)
                //   status='working'  + blocked_by_person → Waiting On column
                //   waiting_on status → always Waiting On column
                $jsStatus = $t->status === 'waiting_on' ? 'working' : 'working';

                // For waiting_on status tasks, use the blocker dep if present,
                // otherwise the task title context is enough
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
                    'commitment_dep_id' => $commitment?->id,
                ];
            })->values();

        return view('dashboard', compact('clients', 'projects', 'tasks'));
    }
}
