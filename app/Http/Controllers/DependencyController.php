<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Dependency;
use App\Models\Person;
use App\Models\Task;
use Illuminate\Http\Request;

class DependencyController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'direction'   => 'required|in:waiting_on,i_owe',
            'task_id'     => 'nullable|exists:tasks,id',
            'project_id'  => 'nullable|exists:projects,id',
            'person_name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'needed_by'   => 'nullable|date',
        ]);

        $person = Person::firstOrCreate(['name' => $data['person_name']]);

        $dep = Dependency::create([
            'direction'   => $data['direction'],
            'task_id'     => $data['task_id'] ?? null,
            'project_id'  => $data['project_id'] ?? null,
            'person_id'   => $person->id,
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

        return response()->json(['dep_id' => $dep->id, 'ok' => true]);
    }

    public function resolve(Dependency $dependency)
    {
        $dependency->update(['status' => 'resolved', 'resolved_at' => now()]);

        if ($dependency->direction === 'waiting_on' && $dependency->task_id) {
            $dependency->task->update(['status' => 'working_on']);
        }

        Activity::create([
            'task_id'       => $dependency->task_id,
            'project_id'    => $dependency->project_id ?? $dependency->task?->project_id,
            'dependency_id' => $dependency->id,
            'type'          => 'dependency_resolved',
            'body'          => "Resolved: {$dependency->description}",
            'created_at'    => now(),
        ]);

        return response()->json(['ok' => true]);
    }
}
