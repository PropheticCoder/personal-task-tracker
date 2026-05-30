<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DependencyController;
use App\Http\Controllers\HandoffController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\UpdateController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

/* ── Auth (public) ────────────────────────────────────── */
Route::middleware('guest')->group(function () {
    Route::get('/login',    [LoginController::class,    'showLoginForm'])->name('login');
    Route::post('/login',   [LoginController::class,    'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register',[RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/* ── Workspaces (auth, no workspace middleware — bootstrapping) ── */
Route::middleware('auth')->group(function () {
    Route::get('/workspaces/create',              [WorkspaceController::class, 'create'])->name('workspaces.create');
    Route::post('/workspaces',                    [WorkspaceController::class, 'store'])->name('workspaces.store');
    Route::post('/workspaces/{workspace}/switch', [WorkspaceController::class, 'switch'])->name('workspaces.switch');
    Route::post('/workspaces/{workspace}/default',[WorkspaceController::class, 'setDefault'])->name('workspaces.default');
    Route::patch('/workspaces/{workspace}',       [WorkspaceController::class, 'update'])->name('workspaces.update');
});

/* ── App (auth required) ──────────────────────────────── */
Route::middleware(['auth', 'workspace'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Handoffs
    Route::get('/handoffs',  [HandoffController::class, 'index'])->name('handoffs.index');
    Route::post('/handoffs', [HandoffController::class, 'store'])->name('handoffs.store');

    // Daily Update
    Route::get('/update', [UpdateController::class, 'index'])->name('update.index');

    // Projects
    Route::get('/projects/create',        [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects',              [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}',     [ProjectController::class, 'show'])->name('projects.show');
    Route::patch('/projects/{project}',   [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}',  [ProjectController::class, 'destroy'])->name('projects.destroy');

    // Tasks
    Route::post('/tasks',                        [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}',                  [TaskController::class, 'show'])->name('tasks.show');
    Route::patch('/tasks/{task}',                [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}',               [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::post('/tasks/{task}/notes',           [TaskController::class, 'addNote'])->name('tasks.notes');
    Route::delete('/activities/{activity}',      [TaskController::class, 'destroyActivity'])->name('activities.destroy');
    Route::post('/tasks/{task}/resolve-blocker', [TaskController::class, 'resolveBlocker'])->name('tasks.resolve-blocker');

    // Dependencies
    Route::post('/dependencies',                   [DependencyController::class, 'store'])->name('dependencies.store');
    Route::post('/dependencies/{dependency}/resolve', [DependencyController::class, 'resolve'])->name('dependencies.resolve');

    // Clients & People
    Route::get('/clients',              [ClientController::class, 'index'])->name('clients.index');
    Route::post('/clients',             [ClientController::class, 'store'])->name('clients.store');
    Route::get('/clients/{client}',     [ClientController::class, 'show'])->name('clients.show');
    Route::patch('/clients/{client}',   [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{client}',  [ClientController::class, 'destroy'])->name('clients.destroy');
    Route::post('/people',              [ClientController::class, 'storePerson'])->name('people.store');
    Route::delete('/people/{person}',   [ClientController::class, 'destroyPerson'])->name('people.destroy');

    // Meetings
    Route::get('/projects/{project}/meetings/create', [MeetingController::class, 'create'])->name('meetings.create');
    Route::post('/projects/{project}/meetings',       [MeetingController::class, 'store'])->name('meetings.store');
    Route::get('/meetings/{meeting}',                 [MeetingController::class, 'show'])->name('meetings.show');
    Route::patch('/meetings/{meeting}',               [MeetingController::class, 'update'])->name('meetings.update');
    Route::post('/meetings/{meeting}/tasks',          [MeetingController::class, 'storeTask'])->name('meetings.tasks.store');

    // Tools
    Route::get('/tools',                    fn() => view('tools.index'))->name('tools.index');
    Route::get('/tools/{key}',              fn($k) => view('tools.run', ['key' => $k]))->name('tools.run');
    Route::post('/tools/{key}/run',         fn($k) => back())->name('tools.execute');
    Route::get('/runs/{run}/artifacts/{i}', fn($r,$i) => abort(404))->name('runs.artifact');
    Route::post('/runs/{run}/clone',        fn($r) => back())->name('runs.clone');

    // Profile
    Route::get('/profile',          [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile',        [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password',[ProfileController::class, 'updatePassword'])->name('profile.password');

    // Timesheets
    Route::get('/timesheets',                     [TimesheetController::class, 'index'])->name('timesheets.index');
    Route::get('/timesheets/create',              [TimesheetController::class, 'create'])->name('timesheets.create');
    Route::post('/timesheets',                    [TimesheetController::class, 'store'])->name('timesheets.store');
    Route::get('/timesheets/{timesheet}',         [TimesheetController::class, 'show'])->name('timesheets.show');
    Route::post('/timesheets/{timesheet}/submit', [TimesheetController::class, 'submit'])->name('timesheets.submit');

    // PWA offline sync
    Route::post('/api/tasks/sync', fn() => response()->json(['ok' => true]))->name('tasks.sync');
});
