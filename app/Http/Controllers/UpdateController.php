<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    public function index(Request $request)
    {
        $workspace = app('current_workspace');

        $since = $request->get('since', 'yesterday');
        $from  = match($since) {
            'today' => now()->startOfDay(),
            'week'  => now()->startOfWeek(),
            default => now()->subDay()->setTime(17, 0),
        };

        $projects = Project::with(['client', 'tasks', 'dependencies'])
            ->where('workspace_id', $workspace->id)
            ->whereIn('status', ['active', 'paused'])
            ->orderBy('name')
            ->get();

        $projectUpdates = $projects->map(function ($project) use ($from) {

            $done = $project->tasks()
                ->where('status', 'done')
                ->where('updated_at', '>=', $from)
                ->orderBy('updated_at', 'desc')
                ->get();

            $inProgress = $project->tasks()
                ->where('status', 'working_on')
                ->get();

            $waitingOn = $project->tasks()
                ->where('status', 'waiting_on')
                ->with('blocker.person')
                ->get();

            $commitments = $project->dependencies()
                ->where('direction', 'i_owe')
                ->where('status', 'open')
                ->with('person')
                ->get();

            $next = $project->tasks()
                ->where('status', 'todo')
                ->orderByRaw("CASE priority WHEN 'high' THEN 1 WHEN 'med' THEN 2 WHEN 'low' THEN 3 ELSE 4 END")
                ->orderBy('needed_by')
                ->take(3)
                ->get();

            // Activity log for "What I've Done" tab
            $activityLog = $project->activities()
                ->where('created_at', '>=', $from)
                ->whereNotNull('body')
                ->with('task')
                ->orderByDesc('created_at')
                ->get();

            return compact('project', 'done', 'inProgress', 'waitingOn', 'commitments', 'next', 'activityLog');
        })->filter(fn($p) =>
            $p['done']->isNotEmpty() ||
            $p['inProgress']->isNotEmpty() ||
            $p['waitingOn']->isNotEmpty() ||
            $p['commitments']->isNotEmpty() ||
            $p['next']->isNotEmpty() ||
            $p['activityLog']->isNotEmpty()
        )->values();

        // Pre-build standup text for each project
        $projectUpdates = $projectUpdates->map(function ($u) {
            $u['standup'] = $this->buildStandup($u);
            return $u;
        });

        return view('update', compact('projectUpdates', 'since', 'from'));
    }

    private function buildStandup(array $u): string
    {
        $lines = [];

        if ($u['done']->isNotEmpty()) {
            $lines[] = '✅ Completed:';
            foreach ($u['done'] as $t) $lines[] = '   • ' . $t->title;
        }
        if ($u['inProgress']->isNotEmpty()) {
            if ($lines) $lines[] = '';
            $lines[] = '🔄 In Progress:';
            foreach ($u['inProgress'] as $t) {
                $note = $t->activities()->where('type', 'note')->latest('created_at')->value('body');
                $lines[] = '   • ' . $t->title . ($note ? ' — ' . $note : '');
            }
        }
        if ($u['waitingOn']->isNotEmpty()) {
            if ($lines) $lines[] = '';
            $lines[] = '⏳ Blocked / Waiting:';
            foreach ($u['waitingOn'] as $t) {
                $who  = $t->blocker?->person?->name ?? 'someone';
                $what = $t->blocker?->description ?? '';
                $lines[] = '   • ' . $t->title . ' — waiting on ' . $who . ($what ? ': ' . $what : '');
            }
        }
        if ($u['commitments']->isNotEmpty()) {
            if ($lines) $lines[] = '';
            $lines[] = '📋 Commitments:';
            foreach ($u['commitments'] as $dep) {
                $due = $dep->needed_by ? ' by ' . $dep->needed_by->format('d M') : '';
                $lines[] = '   • ' . $dep->description . ' → ' . $dep->person->name . $due;
            }
        }
        if ($u['next']->isNotEmpty()) {
            if ($lines) $lines[] = '';
            $lines[] = '→ Next:';
            foreach ($u['next'] as $t) {
                $prio  = $t->priority ? ' (' . strtoupper($t->priority) . ')' : '';
                $lines[] = '   • ' . $t->title . $prio;
            }
        }

        return implode("\n", $lines);
    }
}
