<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        if (! config('app.allow_registration')) {
            return view('auth.register'); // view shows closed state
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        if (! config('app.allow_registration')) {
            abort(403, 'Registration is currently closed.');
        }

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Auto-create a Personal workspace for new users
        $workspace = Workspace::create([
            'name'  => 'Personal',
            'color' => '#7C79FF',
        ]);
        $user->workspaces()->attach($workspace->id);
        $user->update(['default_workspace_id' => $workspace->id]);

        Auth::login($user);

        session(['workspace_id' => $workspace->id]);

        return redirect('/');
    }
}
