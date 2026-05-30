<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Client;
use App\Models\Project;
use App\Models\Timesheet;
use Illuminate\Http\Request;

class TimesheetController extends Controller
{
    private function workspace()
    {
        return app('current_workspace');
    }

    public function index()
    {
        $timesheets = Timesheet::with(['client', 'activities'])
            ->where('workspace_id', $this->workspace()->id)
            ->orderByDesc('period_end')
            ->get();

        return view('timesheets.index', compact('timesheets'));
    }

    public function create()
    {
        $clients = Client::where('workspace_id', $this->workspace()->id)->orderBy('name')->get();

        $wsId = $this->workspace()->id;

        // Task timesheets — scoped to workspace via project
        $taskTimesheets = Timesheet::with(['task.project'])
            ->where('type', 'task')
            ->whereHas('task.project', fn($q) => $q->where('workspace_id', $wsId))
            ->whereHas('activities', fn($q) => $q->whereNotNull('hours'))
            ->get()
            ->groupBy(fn($ts) => $ts->task?->project_id);

        // Loose activities — scoped to workspace projects
        $looseActivities = Activity::with(['task', 'project'])
            ->whereNotNull('hours')
            ->whereNull('timesheet_id')
            ->whereHas('project', fn($q) => $q->where('workspace_id', $wsId))
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('project_id');

        return view('timesheets.create', compact('clients', 'taskTimesheets', 'looseActivities'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'           => 'required|string|max:255',
            'period_start'    => 'required|date',
            'period_end'      => 'required|date|after_or_equal:period_start',
            'client_id'       => 'nullable|exists:clients,id',
            'notes'           => 'nullable|string',
            // task timesheet IDs to pull all their activities into this combined sheet
            'timesheet_ids'   => 'nullable|array',
            'timesheet_ids.*' => 'exists:timesheets,id',
            // individual activity IDs (loose activities)
            'activity_ids'    => 'nullable|array',
            'activity_ids.*'  => 'exists:activities,id',
        ]);

        $combined = Timesheet::create([
            'workspace_id' => $this->workspace()->id,
            'type'         => 'combined',
            'title'        => $data['title'],
            'period_start' => $data['period_start'],
            'period_end'   => $data['period_end'],
            'client_id'    => $data['client_id'] ?? null,
            'notes'        => $data['notes'] ?? null,
        ]);

        // Pull activities from selected task timesheets into this combined sheet
        if (!empty($data['timesheet_ids'])) {
            Activity::whereIn('timesheet_id', $data['timesheet_ids'])
                ->whereNotNull('hours')
                ->update(['timesheet_id' => $combined->id]);
        }

        // Also add any loose activities selected directly
        if (!empty($data['activity_ids'])) {
            Activity::whereIn('id', $data['activity_ids'])
                ->update(['timesheet_id' => $combined->id]);
        }

        return redirect()->route('timesheets.show', $combined)
            ->with('success', 'Timesheet created.');
    }

    public function show(Timesheet $timesheet)
    {
        abort_if($timesheet->workspace_id !== $this->workspace()->id, 403);

        $timesheet->load([
            'client',
            'activities.task',
            'activities.project',
        ]);

        $byProject = $timesheet->activities
            ->whereNotNull('hours')
            ->groupBy('project_id')
            ->map(fn($entries) => [
                'project' => $entries->first()->project,
                'hours'   => round($entries->sum('hours'), 2),
                'entries' => $entries->sortByDesc('created_at')->values(),
            ])->values();

        $totalHours = round($timesheet->activities->sum('hours'), 2);

        return view('timesheets.show', compact('timesheet', 'byProject', 'totalHours'));
    }

    public function submit(Timesheet $timesheet)
    {
        $timesheet->update(['status' => 'submitted']);
        return back()->with('success', 'Timesheet submitted.');
    }
}
