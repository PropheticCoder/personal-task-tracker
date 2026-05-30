<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class SetWorkspace
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return $next($request);
        }

        // Resolve workspace: session → user default → first available
        $wsId      = session('workspace_id') ?? $user->default_workspace_id;
        $workspace = $wsId
            ? $user->workspaces()->find($wsId)
            : $user->workspaces()->first();

        // No workspaces yet → redirect to create (skip if already going there)
        if (!$workspace) {
            if ($request->routeIs('workspaces.*') || $request->routeIs('logout')) {
                return $next($request);
            }
            return redirect()->route('workspaces.create');
        }

        // Keep session in sync
        session(['workspace_id' => $workspace->id]);

        // Make available everywhere
        app()->instance('current_workspace', $workspace);
        View::share('currentWorkspace', $workspace);
        View::share('allWorkspaces', $user->workspaces()->orderBy('name')->get());

        return $next($request);
    }
}
