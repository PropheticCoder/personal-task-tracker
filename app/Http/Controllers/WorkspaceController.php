<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    public function create()
    {
        return view('workspaces.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'color'       => 'nullable|string|max:20',
            'description' => 'nullable|string|max:255',
        ]);

        $workspace = Workspace::create($data);

        $user = auth()->user();
        $user->workspaces()->attach($workspace->id);

        // Set as default if it's their first
        if (!$user->default_workspace_id) {
            $user->update(['default_workspace_id' => $workspace->id]);
        }

        session(['workspace_id' => $workspace->id]);

        return redirect()->route('dashboard')->with('success', "Switched to {$workspace->name}.");
    }

    public function switch(Workspace $workspace)
    {
        $user = auth()->user();

        // Ensure user belongs to this workspace
        if (!$user->workspaces()->where('workspace_id', $workspace->id)->exists()) {
            abort(403);
        }

        session(['workspace_id' => $workspace->id]);

        return redirect()->route('dashboard');
    }

    public function setDefault(Workspace $workspace)
    {
        $user = auth()->user();

        if (!$user->workspaces()->where('workspace_id', $workspace->id)->exists()) {
            abort(403);
        }

        $user->update(['default_workspace_id' => $workspace->id]);

        return back()->with('success', "{$workspace->name} set as default workspace.");
    }

    public function update(Request $request, Workspace $workspace)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'color'       => 'nullable|string|max:20',
            'description' => 'nullable|string|max:255',
        ]);

        $workspace->update($data);

        return back()->with('success', 'Workspace updated.');
    }
}
