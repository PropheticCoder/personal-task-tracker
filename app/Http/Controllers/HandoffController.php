<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Dependency;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class HandoffController extends Controller
{
    public function index()
    {
        $workspace = app('current_workspace');

        // All open deps whose task/project belongs to this workspace
        $deps = Dependency::with(['person', 'task.project', 'project'])
            ->where('status', 'open')
            ->where(function ($q) use ($workspace) {
                $q->whereHas('task.project', fn($q) => $q->where('workspace_id', $workspace->id))
                  ->orWhereHas('project', fn($q) => $q->where('workspace_id', $workspace->id));
            })
            ->orderByRaw("CASE WHEN needed_by IS NOT NULL AND needed_by < NOW() THEN 0 ELSE 1 END")
            ->orderBy('needed_by')
            ->get();

        $waitingOn = $deps->where('direction', 'waiting_on')
            ->groupBy(fn($d) => $d->task?->project_id ?? $d->project_id);

        $iOwe = $deps->where('direction', 'i_owe')
            ->groupBy(fn($d) => $d->task?->project_id ?? $d->project_id);

        // Projects for the "add dep" modal
        $projects = Project::with('tasks')
            ->where('workspace_id', $workspace->id)
            ->whereIn('status', ['active', 'paused'])
            ->orderBy('name')->get();

        return view('handoffs', compact('waitingOn', 'iOwe', 'projects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'direction'   => 'required|in:waiting_on,i_owe',
            'person_name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'task_id'     => 'nullable|exists:tasks,id',
            'project_id'  => 'nullable|exists:projects,id',
            'needed_by'   => 'nullable|date',
        ]);

        $person = \App\Models\Person::firstOrCreate(['name' => $data['person_name']]);

        $dep = Dependency::create([
            'direction'   => $data['direction'],
            'person_id'   => $person->id,
            'task_id'     => $data['task_id'] ?? null,
            'project_id'  => $data['project_id'] ?? null,
            'description' => $data['description'],
            'needed_by'   => $data['needed_by'] ?? null,
            'status'      => 'open',
        ]);

        if ($data['direction'] === 'waiting_on' && !empty($data['task_id'])) {
            Task::find($data['task_id'])->update(['status' => 'waiting_on']);
        }

        Activity::create([
            'task_id'       => $dep->task_id,
            'project_id'    => $dep->project_id ?? Task::find($dep->task_id)?->project_id,
            'dependency_id' => $dep->id,
            'type'          => 'dependency_opened',
            'body'          => $data['direction'] === 'waiting_on'
                ? "Waiting on {$person->name}: {$dep->description}"
                : "Committed to {$person->name}: {$dep->description}",
            'created_at'    => now(),
        ]);

        return back()->with('success', 'Dependency added.');
    }
}
