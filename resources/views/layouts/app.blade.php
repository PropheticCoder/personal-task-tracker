<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' — ' : '' }}{{ config('app.name', 'WorkTracker') }}</title>
    <link rel="manifest" href="/manifest.json">

    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    @livewireStyles

    <style>
        body { background-color: #f8f9fa; }

        /* Status colours */
        .badge-inbox      { background-color: #6c757d; color: #fff; }
        .badge-todo       { background-color: #ffc107; color: #212529; }
        .badge-in-progress{ background-color: #0d6efd; color: #fff; }
        .badge-blocked    { background-color: #dc3545; color: #fff; }
        .badge-done       { background-color: #198754; color: #fff; }
        .badge-archived   { background-color: #adb5bd; color: #212529; }

        /* Priority dots */
        .dot-high   { color: #dc3545; }
        .dot-med    { color: #fd7e14; }
        .dot-low    { color: #6c757d; }

        /* Overdue highlight */
        .overdue { border-left: 3px solid #dc3545 !important; }

        /* Desktop sidebar */
        @media (min-width: 992px) {
            .sidebar { position: sticky; top: 56px; height: calc(100vh - 56px); overflow-y: auto; }
        }

        /* Mobile bottom nav clearance */
        @media (max-width: 991.98px) {
            .main-content { padding-bottom: 70px; }
        }
    </style>
</head>
<body>

    {{-- ── Top Navbar ── --}}
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
        <div class="container-fluid px-3">
            <a class="navbar-brand fw-semibold" href="/">
                <i class="bi bi-check2-square me-1 text-primary"></i> WorkTracker
            </a>

            {{-- Desktop nav links --}}
            <div class="d-none d-lg-flex gap-3 ms-4 me-auto">
                <a href="/"         class="nav-link py-0 {{ request()->is('/') ? 'fw-medium text-dark' : 'text-secondary' }}">Dashboard</a>
                <a href="/handoffs" class="nav-link py-0 {{ request()->is('handoffs') ? 'fw-medium text-dark' : 'text-secondary' }}">Handoffs</a>
                <a href="/update"   class="nav-link py-0 {{ request()->is('update') ? 'fw-medium text-dark' : 'text-secondary' }}">Daily Update</a>
                <a href="/tools"    class="nav-link py-0 {{ request()->is('tools*') ? 'fw-medium text-dark' : 'text-secondary' }}">Tools</a>
            </div>
        </div>
    </nav>

    {{-- ── Page Content ── --}}
    <main class="main-content">
        {{ $slot }}
    </main>

    {{-- ── Mobile Bottom Tab Bar ── --}}
    <nav class="d-lg-none fixed-bottom bg-white border-top">
        <div class="d-flex">
            <a href="/" class="flex-fill text-center py-2 text-decoration-none {{ request()->is('/') ? 'text-primary' : 'text-secondary' }}">
                <i class="bi bi-house d-block fs-5"></i>
                <span style="font-size:10px">Tasks</span>
            </a>
            <a href="/handoffs" class="flex-fill text-center py-2 text-decoration-none {{ request()->is('handoffs') ? 'text-primary' : 'text-secondary' }}">
                <i class="bi bi-arrow-left-right d-block fs-5"></i>
                <span style="font-size:10px">Handoffs</span>
            </a>
            <a href="/update" class="flex-fill text-center py-2 text-decoration-none {{ request()->is('update') ? 'text-primary' : 'text-secondary' }}">
                <i class="bi bi-file-text d-block fs-5"></i>
                <span style="font-size:10px">Update</span>
            </a>
            <a href="/tools" class="flex-fill text-center py-2 text-decoration-none {{ request()->is('tools*') ? 'text-primary' : 'text-secondary' }}">
                <i class="bi bi-tools d-block fs-5"></i>
                <span style="font-size:10px">Tools</span>
            </a>
        </div>
    </nav>

    {{-- Bootstrap 5 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @livewireScripts
</body>
</html>
