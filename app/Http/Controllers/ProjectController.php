<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function create()
    {
        $clients = Client::where('workspace_id', app('current_workspace')->id)->orderBy('name')->get();
        return view('projects.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'client_id'   => 'nullable|exists:clients,id',
            'description' => 'nullable|string',
            'color'       => 'nullable|string|max:20',
            'status'      => 'nullable|in:active,paused,completed,archived',
        ]);

        $data['workspace_id'] = app('current_workspace')->id;
        $project = Project::create($data);

        return redirect()->route('projects.show', $project);
    }

    public function show(Project $project)
    {
        $project->load([
            'client.people',
            'tasks' => fn($q) => $q->orderByRaw("FIELD(status,'working_on','waiting_on','todo','done','archived')"),
            'meetings' => fn($q) => $q->orderByDesc('held_at')->withCount('tasks'),
        ]);

        $clients = Client::where('workspace_id', app('current_workspace')->id)->orderBy('name')->get();

        return view('projects.show', compact('project', 'clients'));
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('dashboard')->with('success', 'Project deleted.');
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'client_id'   => 'nullable|exists:clients,id',
            'description' => 'nullable|string',
            'color'       => 'nullable|string|max:20',
            'status'      => 'nullable|in:active,paused,completed,archived',
        ]);

        $project->update($data);

        return back()->with('success', 'Project updated.');
    }
}
