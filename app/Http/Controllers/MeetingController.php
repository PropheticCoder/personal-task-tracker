<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Meeting;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function create(Project $project)
    {
        $project->load('client.people');
        return view('meetings.create', compact('project'));
    }

    public function store(Request $request, Project $project)
    {
        $data = $request->validate([
            'title'   => 'required|string|max:255',
            'held_at' => 'required|date',
            'notes'   => 'nullable|string',
        ]);

        $meeting = $project->meetings()->create($data);

        Activity::create([
            'project_id' => $project->id,
            'meeting_id' => $meeting->id,
            'type'       => 'meeting_held',
            'body'       => $meeting->title,
            'created_at' => now(),
        ]);

        return redirect()->route('meetings.show', $meeting)->with('success', 'Meeting created.');
    }

    public function show(Meeting $meeting)
    {
        $meeting->load([
            'project.client',
            'attendees',
            'tasks' => fn($q) => $q->orderByRaw("FIELD(status,'working_on','waiting_on','todo','done','archived')"),
        ]);

        return view('meetings.show', compact('meeting'));
    }

    public function update(Request $request, Meeting $meeting)
    {
        $data = $request->validate([
            'title'   => 'required|string|max:255',
            'held_at' => 'required|date',
            'notes'   => 'nullable|string',
        ]);

        $meeting->update($data);

        return back()->with('success', 'Meeting updated.');
    }

    public function storeTask(Request $request, Meeting $meeting)
    {
        $data = $request->validate([
            'title'    => 'required|string|max:255',
            'priority' => 'nullable|in:low,med,high',
            'needed_by'=> 'nullable|date',
        ]);

        $task = Task::create([
            'project_id' => $meeting->project_id,
            'meeting_id' => $meeting->id,
            'title'      => $data['title'],
            'priority'   => $data['priority'] ?? null,
            'needed_by'  => $data['needed_by'] ?? null,
            'status'     => 'todo',
        ]);

        Activity::create([
            'task_id'    => $task->id,
            'project_id' => $meeting->project_id,
            'meeting_id' => $meeting->id,
            'type'       => 'task_added',
            'new_status' => 'todo',
            'created_at' => now(),
        ]);

        return back()->with('success', 'Action item added.');
    }
}
