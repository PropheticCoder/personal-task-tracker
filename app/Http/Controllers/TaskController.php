<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id'  => 'required|exists:projects,id',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority'    => 'nullable|in:low,med,high',
            'needed_by'   => 'nullable|date',
            'status'      => 'nullable|in:todo,working_on,waiting_on,done,archived',
        ]);

        $task = Task::create($data);

        // Auto-create a workspace-scoped timesheet for this task
        \App\Models\Timesheet::create([
            'workspace_id' => $task->project->workspace_id,
            'task_id'      => $task->id,
            'type'         => 'task',
            'title'        => $task->title,
            'period_start' => now()->toDateString(),
            'period_end'   => now()->toDateString(),
        ]);

        Activity::create([
            'task_id'    => $task->id,
            'project_id' => $task->project_id,
            'type'       => 'task_added',
            'new_status' => $task->status,
            'created_at' => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['id' => $task->id, 'ok' => true]);
        }

        return back()->with('success', 'Task added.');
    }

    public function show(Task $task)
    {
        $task->load(['project.client', 'blocker.person', 'commitments.person', 'activities']);
        return view('tasks.show', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'title'       => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'nullable|in:todo,working_on,waiting_on,done,archived',
            'priority'    => 'nullable|in:low,med,high',
            'needed_by'   => 'nullable|date',
            'source'      => 'nullable|string|max:255',
            'hours'       => 'nullable|numeric|min:0.25|max:24',
        ]);

        $oldStatus = $task->status;
        $task->update($data);

        if (isset($data['status']) && $data['status'] !== $oldStatus) {
            $timesheetId = null;
            if (!empty($data['hours'])) {
                $ts = $task->timesheet ?? \App\Models\Timesheet::create([
                    'task_id'      => $task->id,
                    'type'         => 'task',
                    'title'        => $task->title,
                    'period_start' => now()->toDateString(),
                    'period_end'   => now()->toDateString(),
                ]);
                $ts->update(['period_end' => now()->toDateString()]);
                $timesheetId = $ts->id;
            }

            Activity::create([
                'task_id'      => $task->id,
                'project_id'   => $task->project_id,
                'type'         => 'status_change',
                'old_status'   => $oldStatus,
                'new_status'   => $data['status'],
                'hours'        => $data['hours'] ?? null,
                'timesheet_id' => $timesheetId,
                'created_at'   => now(),
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Task updated.');
    }

    public function destroy(Task $task)
    {
        $projectId = $task->project_id;
        $task->delete();
        return redirect()->route('projects.show', $projectId)->with('success', 'Task deleted.');
    }

    public function destroyActivity(\App\Models\Activity $activity)
    {
        // Only allow deleting note-type activities
        abort_if($activity->type !== 'note', 403);
        $taskId = $activity->task_id;
        $activity->delete();

        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Note deleted.');
    }

    public function resolveBlocker(Task $task)
    {
        $blocker = $task->blocker;
        if ($blocker) {
            $blocker->update(['status' => 'resolved', 'resolved_at' => now()]);
            Activity::create([
                'task_id'       => $task->id,
                'project_id'    => $task->project_id,
                'dependency_id' => $blocker->id,
                'type'          => 'dependency_resolved',
                'body'          => "Blocker resolved: {$blocker->description}",
                'created_at'    => now(),
            ]);
        }
        $task->update(['status' => 'working_on']);
        return response()->json(['ok' => true]);
    }

    public function addNote(Request $request, Task $task)
    {
        $data = $request->validate([
            'body'  => 'required|string',
            'hours' => 'nullable|numeric|min:0.25|max:24',
        ]);

        $timesheetId = null;
        if (!empty($data['hours'])) {
            $ts = $task->timesheet ?? \App\Models\Timesheet::create([
                'workspace_id' => $task->project->workspace_id,
                'task_id'      => $task->id,
                'type'         => 'task',
                'title'        => $task->title,
                'period_start' => now()->toDateString(),
                'period_end'   => now()->toDateString(),
            ]);
            // Keep task timesheet period_end current
            $ts->update(['period_end' => now()->toDateString()]);
            $timesheetId = $ts->id;
        }

        Activity::create([
            'task_id'      => $task->id,
            'project_id'   => $task->project_id,
            'type'         => 'note',
            'body'         => $data['body'],
            'hours'        => $data['hours'] ?? null,
            'timesheet_id' => $timesheetId,
            'created_at'   => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Note added.');
    }
}
