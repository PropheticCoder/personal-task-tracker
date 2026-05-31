<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' — ' : '' }}{{ config('app.name', 'WorkTracker') }}</title>
    <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/icons/icon-180x180.png">
    <link rel="apple-touch-icon" sizes="167x167" href="/icons/icon-167x167.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/icons/icon-152x152.png">
    <link rel="manifest" href="/manifest.json">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,500;12..96,600;12..96,700&family=Plus+Jakarta+Sans:wght@400;500;600&family=Azeret+Mono:wght@400;500&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    @livewireStyles

    <style>
        /* ─── Tokens ─────────────────────────────────────────────── */
        :root {
            /* Surfaces — warm charcoal with clear elevation steps */
            --bg-base:        #13141A;
            --bg-raised:      #1C1E27;
            --bg-elevated:    #232632;
            --bg-hover:       #2A2D3C;

            /* Borders — actually visible */
            --border-faint:   rgba(255,255,255,0.08);
            --border-base:    rgba(255,255,255,0.14);
            --border-strong:  rgba(255,255,255,0.26);

            /* Text — clear hierarchy, nothing ghostly */
            --text-1:         #EDEEF5;
            --text-2:         #9294AF;
            --text-3:         #5C5F7A;

            /* Accent — brighter indigo with warmth */
            --accent:         #7C79FF;
            --accent-bg:      rgba(124,121,255,0.13);
            --accent-glow:    rgba(124,121,255,0.28);

            /* Status — richer, more saturated */
            --success:        #2DD4AA;
            --success-bg:     rgba(45,212,170,0.12);
            --warning:        #F5B53F;
            --warning-bg:     rgba(245,181,63,0.12);
            --danger:         #FF6B7B;
            --danger-bg:      rgba(255,107,123,0.12);
            --info:           #4FC4FF;
            --info-bg:        rgba(79,196,255,0.12);

            --font-display:   'Bricolage Grotesque', sans-serif;
            --font-body:      'Plus Jakarta Sans', sans-serif;
            --font-mono:      'Azeret Mono', monospace;
        }

        /* ─── Base ─────────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; }

        html, body { height: 100%; }

        html {
            overflow-x: hidden;
            max-width: 100%;
        }

        body {
            background: var(--bg-base);
            color: var(--text-1);
            font-family: var(--font-body);
            font-size: 14px;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
            max-width: 100%;
        }

        /* Subtle dot grid */
        body::before {
            content: '';
            position: fixed; inset: 0;
            background-image: radial-gradient(circle, rgba(255,255,255,0.038) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
            z-index: 0;
        }

        /* Ambient top glow — warmer, more visible */
        body::after {
            content: '';
            position: fixed;
            top: -120px; left: 50%;
            transform: translateX(-50%);
            width: 680px; height: 380px;
            background: radial-gradient(ellipse, rgba(124,121,255,0.09) 0%, transparent 65%);
            pointer-events: none;
            z-index: 0;
        }

        /* ─── Nav ─────────────────────────────────────────────── */
        .wt-nav {
            position: sticky; top: 0; z-index: 200;
            height: 50px;
            display: flex; align-items: center;
            padding: 0 20px;
            background: rgba(19,20,26,0.92);
            border-bottom: 1px solid var(--border-base);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        .wt-brand {
            text-decoration: none;
            display: flex; align-items: center;
            flex-shrink: 0;
        }

        .wt-brand img {
            height: 32px;
            width: auto;
            display: block;
        }

        .wt-nav-links { display: flex; align-items: center; gap: 1px; margin-left: 20px; flex: 1; }

        .wt-nav-link {
            font-size: 13px; font-weight: 500;
            color: var(--text-2);
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 6px;
            transition: color .15s, background .15s;
        }

        .wt-nav-link:hover  { color: var(--text-1); background: rgba(255,255,255,0.05); }
        .wt-nav-link.active { color: var(--text-1); background: rgba(255,255,255,0.07); }

        /* ─── Layout Shell ─────────────────────────────────────── */
        .wt-layout {
            position: relative; z-index: 1;
            display: flex;
            height: calc(100vh - 50px);
            overflow: hidden;
        }

        /* ─── Sidebar ─────────────────────────────────────────── */
        .wt-sidebar {
            width: 232px; min-width: 232px;
            background: var(--bg-raised);
            border-right: 1px solid var(--border-faint);
            display: flex; flex-direction: column;
            overflow-y: auto;
        }

        .wt-sb-block {
            padding: 16px;
            border-bottom: 1px solid var(--border-faint);
        }

        .wt-mono-label {
            display: block;
            font-family: var(--font-mono);
            font-size: 9.5px; font-weight: 500;
            color: var(--text-3);
            text-transform: uppercase;
            letter-spacing: 0.12em;
            margin-bottom: 10px;
        }

        /* Quick-capture */
        .wt-capture {
            border: 1px solid var(--border-base);
            border-radius: 10px;
            background: var(--bg-elevated);
            overflow: hidden;
            transition: border-color .2s, box-shadow .2s;
        }

        .wt-capture:focus-within {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-bg);
        }

        .wt-capture input {
            width: 100%; background: transparent; border: 0; outline: none;
            color: var(--text-1);
            font-family: var(--font-body); font-size: 13px;
            padding: 9px 12px 5px;
        }

        .wt-capture input::placeholder { color: var(--text-3); }

        .wt-capture select {
            width: 100%; background: transparent; border: 0;
            border-top: 1px solid var(--border-faint);
            outline: none; cursor: pointer;
            color: var(--text-2);
            font-family: var(--font-mono); font-size: 10px;
            padding: 5px 12px;
            -webkit-appearance: none; appearance: none;
        }

        .wt-capture select option { background: var(--bg-elevated); }

        .wt-capture-footer {
            display: flex; align-items: center; justify-content: space-between;
            padding: 5px 12px 9px;
        }

        .wt-capture-hint { font-family: var(--font-mono); font-size: 9.5px; color: var(--text-3); }

        .wt-capture-btn {
            font-family: var(--font-mono); font-size: 10px;
            background: var(--accent); color: #fff;
            border: 0; border-radius: 5px;
            padding: 3px 10px; cursor: pointer;
            transition: opacity .15s;
        }

        .wt-capture-btn:hover { opacity: 0.8; }

        /* Handoff counter tiles */
        .wt-hof-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }

        .wt-hof-tile {
            display: block; text-decoration: none;
            background: var(--bg-elevated);
            border: 1px solid var(--border-faint);
            border-radius: 10px;
            padding: 11px 12px;
            transition: border-color .15s, background .15s;
        }

        .wt-hof-tile:hover { background: var(--bg-hover); border-color: var(--border-base); }

        .wt-hof-n {
            font-family: var(--font-display);
            font-size: 24px; font-weight: 700;
            line-height: 1; margin-bottom: 4px;
        }

        .wt-hof-lbl {
            font-family: var(--font-mono);
            font-size: 9px; text-transform: uppercase;
            letter-spacing: .1em; color: var(--text-3);
        }

        /* Sidebar links */
        .wt-sb-nav { padding: 8px; }

        .wt-sb-link {
            display: flex; align-items: center; gap: 9px;
            padding: 7px 9px;
            border-radius: 6px;
            text-decoration: none;
            color: var(--text-2);
            font-size: 13px; font-weight: 500;
            transition: color .15s, background .15s;
            margin-bottom: 1px;
        }

        .wt-sb-link:hover  { color: var(--text-1); background: rgba(255,255,255,0.04); }
        .wt-sb-link.active { color: var(--text-1); background: var(--accent-bg); }
        .wt-sb-link.active i { color: var(--accent); }
        .wt-sb-link i { font-size: 15px; width: 16px; text-align: center; flex-shrink: 0; }

        /* ─── Main ─────────────────────────────────────────────── */
        .wt-main { flex: 1; overflow-y: auto; padding: 26px 28px; position: relative; z-index: 1; }

        /* ─── Project Cards ────────────────────────────────────── */
        .wt-card {
            background: var(--bg-raised);
            border: 1px solid var(--border-faint);
            border-radius: 12px;
            overflow: hidden;
            transition: border-color .25s, box-shadow .25s;
            animation: card-in .5s cubic-bezier(.16,1,.3,1) both;
        }

        .wt-card:hover { border-color: var(--border-strong); box-shadow: 0 8px 32px rgba(0,0,0,.35), 0 0 0 1px rgba(124,121,255,0.06); }

        @keyframes card-in {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .wt-card:nth-child(1) { animation-delay: .04s; }
        .wt-card:nth-child(2) { animation-delay: .09s; }
        .wt-card:nth-child(3) { animation-delay: .14s; }
        .wt-card:nth-child(4) { animation-delay: .19s; }
        .wt-card:nth-child(5) { animation-delay: .24s; }

        .wt-card-strip { height: 2px; width: 100%; }

        .wt-card-head {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border-faint);
        }

        .wt-card-title-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 8px; }

        .wt-project-link {
            font-family: var(--font-display);
            font-size: 15px; font-weight: 600;
            color: var(--text-1); text-decoration: none;
            letter-spacing: -0.02em; line-height: 1.25;
        }

        .wt-project-link:hover { color: var(--accent); }

        .wt-card-meta { display: flex; align-items: center; gap: 6px; margin-top: 5px; }

        .wt-client-pill {
            font-family: var(--font-mono); font-size: 10px;
            color: var(--text-3);
            display: flex; align-items: center; gap: 3px;
        }

        .wt-status-tag {
            font-family: var(--font-mono); font-size: 9.5px;
            padding: 2px 8px; border-radius: 20px; border: 1px solid;
        }

        .wt-status-tag.active { color: var(--success); border-color: rgba(46,196,160,.28); background: var(--success-bg); }
        .wt-status-tag.paused { color: var(--warning); border-color: rgba(244,167,41,.28); background: var(--warning-bg); }

        /* Sections */
        .wt-cs { padding: 11px 16px; border-bottom: 1px solid var(--border-faint); }
        .wt-cs:last-child { border-bottom: 0; }

        .wt-cs-label {
            font-family: var(--font-mono);
            font-size: 9px; font-weight: 500;
            text-transform: uppercase; letter-spacing: .14em;
            display: flex; align-items: center; gap: 5px;
            margin-bottom: 8px;
        }

        .wt-cs-pip { width: 4px; height: 4px; border-radius: 1px; flex-shrink: 0; }

        .now-label  { color: var(--info); }
        .now-label  .wt-cs-pip { background: var(--info); }
        .blk-label  { color: var(--danger); }
        .blk-label  .wt-cs-pip { background: var(--danger); animation: pip-pulse 2s ease-in-out infinite; }
        .next-label { color: var(--success); }
        .next-label .wt-cs-pip { background: var(--success); }

        @keyframes pip-pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(255,92,108,.5); }
            50%       { box-shadow: 0 0 0 4px rgba(255,92,108,0); }
        }

        .wt-task-row { display: flex; align-items: center; gap: 8px; margin-bottom: 5px; }
        .wt-task-row:last-child { margin-bottom: 0; }

        .wt-task-link {
            flex: 1; min-width: 0;
            font-size: 13px; color: var(--text-1);
            text-decoration: none;
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }

        .wt-task-link:hover { color: var(--accent); }

        .wt-prio {
            font-family: var(--font-mono); font-size: 9px;
            padding: 1.5px 5px; border-radius: 3px; flex-shrink: 0;
        }

        .wt-prio.high { color: var(--danger);  background: var(--danger-bg); }
        .wt-prio.med  { color: var(--warning); background: var(--warning-bg); }
        .wt-prio.low  { color: var(--text-3);  background: rgba(255,255,255,.04); }

        .wt-blocker-row { display: flex; align-items: flex-start; gap: 7px; font-size: 12px; color: var(--text-2); margin-bottom: 5px; line-height: 1.4; }
        .wt-blocker-row:last-child { margin-bottom: 0; }

        .wt-pulse-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: var(--danger); flex-shrink: 0; margin-top: 4px;
            animation: pulse-dot 2s ease-in-out infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(255,92,108,.5); }
            50%       { opacity: .6; box-shadow: 0 0 0 5px rgba(255,92,108,0); }
        }

        .wt-od { font-family: var(--font-mono); font-size: 9px; color: var(--danger); background: var(--danger-bg); padding: 1px 5px; border-radius: 3px; margin-left: 4px; }
        .is-overdue { color: var(--danger); }

        /* Card footer */
        .wt-card-foot {
            padding: 9px 16px;
            background: rgba(0,0,0,.10);
            border-top: 1px solid var(--border-faint);
            display: flex; align-items: center; gap: 12px;
            font-family: var(--font-mono); font-size: 9.5px; color: var(--text-3);
        }

        .wt-foot-stat { display: flex; align-items: center; gap: 3px; }
        .wt-foot-stat.d { color: var(--danger); }

        .wt-foot-link { margin-left: auto; text-decoration: none; color: var(--text-3); font-family: var(--font-mono); font-size: 9.5px; transition: color .15s; }
        .wt-foot-link:hover { color: var(--accent); }

        /* ─── Buttons ─────────────────────────────────────────── */
        .wt-btn {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 6px 14px; border-radius: 6px;
            border: 1px solid var(--border-base);
            background: var(--bg-elevated); color: var(--text-2);
            font-family: var(--font-mono); font-size: 11px;
            text-decoration: none; cursor: pointer;
            transition: color .15s, border-color .15s, background .15s;
            white-space: nowrap;
        }

        .wt-btn:hover { color: var(--text-1); border-color: var(--border-strong); background: var(--bg-hover); }

        .wt-btn-accent { background: var(--accent); border-color: transparent; color: #fff; }
        .wt-btn-accent:hover { background: #5a57e8; color: #fff; border-color: transparent; }

        /* ─── Mobile bottom tab ────────────────────────────────── */
        .wt-mob-nav {
            position: fixed; bottom: 0; left: 0; right: 0;
            background: rgba(19,20,26,.97);
            border-top: 1px solid var(--border-faint);
            backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
            display: flex; z-index: 400;
            /* Always on top — above FABs (250), page content, Bootstrap modals (1055) use higher */
        }

        .wt-mob-tab {
            flex: 1; display: flex; flex-direction: column;
            align-items: center; gap: 3px;
            padding: 10px 4px 8px; text-decoration: none;
            color: var(--text-3);
            font-family: var(--font-mono); font-size: 9px;
            transition: color .15s;
        }

        .wt-mob-tab i { font-size: 18px; }
        .wt-mob-tab.active { color: var(--accent); }

        /* ─── Bootstrap dark overrides ────────────────────────── */
        /* Force Bootstrap card/form/modal/table to use our tokens */
        [data-bs-theme="dark"] {
            --bs-body-bg:           var(--bg-base);
            --bs-body-color:        var(--text-1);
            --bs-secondary-color:   var(--text-2);
            --bs-border-color:      var(--border-base);

            --bs-card-bg:           var(--bg-raised);
            --bs-card-border-color: var(--border-faint);
            --bs-card-cap-bg:       var(--bg-elevated);

            --bs-modal-bg:          var(--bg-elevated);
            --bs-modal-border-color:var(--border-base);
            --bs-modal-header-border-color: var(--border-faint);
            --bs-modal-footer-border-color: var(--border-faint);

            --bs-input-bg:          var(--bg-elevated);
            --bs-form-control-bg:   var(--bg-elevated);
            --bs-form-select-bg:    var(--bg-elevated);

            --bs-table-bg:          transparent;
            --bs-table-striped-bg:  rgba(255,255,255,.03);
            --bs-table-hover-bg:    rgba(255,255,255,.05);
            --bs-table-border-color:var(--border-faint);

            --bs-list-group-bg:     var(--bg-raised);
            --bs-list-group-border-color: var(--border-faint);
            --bs-list-group-action-hover-bg: var(--bg-hover);
            --bs-list-group-action-active-bg: var(--bg-active);

            --bs-dropdown-bg:       var(--bg-elevated);
            --bs-dropdown-border-color: var(--border-base);
            --bs-dropdown-link-hover-bg: var(--bg-hover);
            --bs-dropdown-link-active-bg: var(--accent-bg);
        }

        /* ── Nuke all Bootstrap light utilities in dark theme ── */
        [data-bs-theme="dark"] .bg-white  { background-color: var(--bg-elevated) !important; }
        [data-bs-theme="dark"] .bg-light  { background-color: var(--bg-elevated) !important; color: var(--text-2) !important; }
        [data-bs-theme="dark"] .text-dark { color: var(--text-1) !important; }
        [data-bs-theme="dark"] .navbar-light.bg-white { background-color: var(--bg-raised) !important; }
        [data-bs-theme="dark"] .border-end   { border-color: var(--border-faint) !important; }
        [data-bs-theme="dark"] .border-bottom { border-color: var(--border-faint) !important; }

        /* Card header/footer consistent with our design */
        .card-header, .card-footer {
            background-color: var(--bg-elevated) !important;
            border-color: var(--border-faint) !important;
        }

        /* Form controls */
        .form-control, .form-select, .input-group-text {
            background-color: var(--bg-elevated) !important;
            border-color: var(--border-base) !important;
            color: var(--text-1) !important;
        }

        .form-control:focus, .form-select:focus {
            background-color: var(--bg-elevated) !important;
            border-color: var(--accent) !important;
            box-shadow: 0 0 0 3px var(--accent-bg) !important;
            color: var(--text-1) !important;
        }

        .form-control::placeholder { color: var(--text-3) !important; }

        /* Navbar within pages */
        .navbar { border-color: var(--border-faint) !important; }

        /* Buttons — map Bootstrap variants to our tokens */
        .btn-primary   { background-color: var(--accent) !important; border-color: var(--accent) !important; }
        .btn-primary:hover { background-color: #5a57e8 !important; border-color: #5a57e8 !important; }
        .btn-outline-primary { color: var(--accent) !important; border-color: var(--accent) !important; }
        .btn-outline-primary:hover { background-color: var(--accent-bg) !important; color: var(--accent) !important; }

        .btn-outline-secondary { border-color: var(--border-base) !important; color: var(--text-2) !important; }
        .btn-outline-secondary:hover { background-color: var(--bg-hover) !important; color: var(--text-1) !important; border-color: var(--border-strong) !important; }

        .btn-outline-success { color: var(--success) !important; border-color: rgba(46,196,160,.35) !important; }
        .btn-outline-success:hover { background-color: var(--success-bg) !important; }

        .btn-outline-danger { color: var(--danger) !important; border-color: rgba(255,92,108,.35) !important; }
        .btn-outline-danger:hover { background-color: var(--danger-bg) !important; }

        /* Badge resets to use subtler colours */
        .badge.bg-light { background-color: var(--bg-elevated) !important; color: var(--text-2) !important; border-color: var(--border-faint) !important; }

        /* Nav tabs */
        .nav-tabs { border-color: var(--border-faint) !important; }
        .nav-tabs .nav-link { color: var(--text-2); font-family: var(--font-body); font-size: 13px; }
        .nav-tabs .nav-link:hover { color: var(--text-1); border-color: transparent; }
        .nav-tabs .nav-link.active { color: var(--text-1); background: transparent; border-color: var(--border-faint) var(--border-faint) var(--bg-raised); }

        /* Dropdown */
        .dropdown-item { color: var(--text-1) !important; font-size: 13px; }
        .dropdown-item:hover, .dropdown-item:focus { background-color: var(--bg-hover) !important; }
        .dropdown-divider { border-color: var(--border-faint) !important; }

        /* Modal sizing — Bootstrap defaults feel too narrow */
        .modal-dialog          { --bs-modal-width: 560px; }
        .modal-dialog.modal-sm { --bs-modal-width: 440px; }

        /* Modal backdrop */
        .modal-backdrop { background-color: rgba(0,0,0,.55); }

        /* Table */
        .table { --bs-table-color: var(--text-1); }
        .table thead th { font-family: var(--font-mono); font-size: 10px; text-transform: uppercase; letter-spacing: .1em; color: var(--text-3); font-weight: 500; border-color: var(--border-faint) !important; }
        .table td, .table th { border-color: var(--border-faint) !important; vertical-align: middle; }
        .table-light { --bs-table-bg: var(--bg-elevated); }

        /* ─── Scrollbars ──────────────────────────────────────── */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,.12); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,.22); }

        /* ─── Responsive ──────────────────────────────────────── */
        /* Add-task form in project tabs bar */
        .wt-add-task-form { min-width: 260px; }
        .wt-add-task-form .form-control { flex: 1; min-width: 0; }

        @media (max-width: 991.98px) {
            /* Allow body to grow beyond viewport height so pages scroll freely */
            html { height: auto; }
            body { height: auto; min-height: 100vh; padding-bottom: 72px; }
            .wt-add-task-form { width: 100%; min-width: 0; }
            /* Project tabs bar sticks below the top nav on mobile */
            .proj-tabs-bar { top: 50px !important; }
            .wt-sidebar { display: none !important; }
            .wt-main { padding: 16px 16px 84px; }
            .wt-layout { height: auto; overflow: visible; }

            /* Tighter page padding on mobile */
            .container-lg, .container { padding-left: 14px !important; padding-right: 14px !important; }

            /* Better touch targets */
            .btn { min-height: 38px; }
            .btn-sm { min-height: 32px; }
            .dropdown-item { padding: 10px 16px; }

            /* Modals full-width on small screens */
            .modal-dialog, .modal-dialog.modal-sm { max-width: calc(100vw - 24px) !important; margin: 12px auto; }
        }

        @media (min-width: 992px) { .wt-mob-nav { display: none !important; } }

        /* Mobile hamburger button */
        .wt-burger {
            display: none; align-items: center; justify-content: center;
            width: 36px; height: 36px; border-radius: 8px;
            background: var(--bg-elevated); border: 1px solid var(--border-base);
            color: var(--text-2); cursor: pointer; transition: all .15s;
        }
        .wt-burger:hover { color: var(--text-1); border-color: var(--border-strong); }
        @media (max-width: 991.98px) { .wt-burger { display: flex; } }

        /* Mobile off-canvas menu */
        .wt-mob-menu {
            position: fixed; inset: 0; z-index: 9999;
            display: none;
        }
        .wt-mob-menu.open { display: block; }
        .wt-mob-menu-backdrop {
            position: absolute; inset: 0;
            background: rgba(0,0,0,.6); backdrop-filter: blur(4px);
        }
        .wt-mob-menu-panel {
            position: absolute; top: 0; right: 0; bottom: 0;
            width: 280px; max-width: 85vw;
            background: var(--bg-raised);
            border-left: 1px solid var(--border-base);
            display: flex; flex-direction: column;
            overflow-y: auto;
            transform: translateX(100%);
            transition: transform .28s cubic-bezier(.16,1,.3,1);
        }
        .wt-mob-menu.open .wt-mob-menu-panel { transform: translateX(0); }
        .wt-mob-menu-link {
            display: flex; align-items: center; gap: 12px;
            padding: 13px 20px;
            font-size: 14px; font-weight: 500; color: var(--text-1);
            text-decoration: none; border-bottom: 1px solid var(--border-faint);
            transition: background .15s;
        }
        .wt-mob-menu-link:hover, .wt-mob-menu-link.active { background: var(--bg-elevated); color: var(--text-1); }
        .wt-mob-menu-link i { width: 20px; text-align: center; color: var(--text-3); font-size: 16px; }
        .wt-mob-menu-link.active i { color: var(--accent); }
    </style>
</head>
<body>

    <nav class="wt-nav">
        <a href="/" class="wt-brand">
            <img src="/logo.png" alt="TaskTracker" />
        </a>
        <div class="wt-nav-links d-none d-lg-flex">
            <a href="/"           class="wt-nav-link {{ request()->is('/') ? 'active' : '' }}">Dashboard</a>
            <a href="/clients"    class="wt-nav-link {{ request()->is('clients*') ? 'active' : '' }}">Clients</a>
            <a href="/handoffs"   class="wt-nav-link {{ request()->is('handoffs*') ? 'active' : '' }}">Handoffs</a>
            <a href="/update"     class="wt-nav-link {{ request()->is('update*') ? 'active' : '' }}">Update</a>
            <a href="/timesheets" class="wt-nav-link {{ request()->is('timesheets*') ? 'active' : '' }}">Timesheets</a>
            <a href="/tools" class="wt-nav-link {{ request()->is('tools*') ? 'active' : '' }}"
               {{ app()->isProduction() ? 'style="opacity:.45" title="Coming soon"' : '' }}>
                Tools @if(app()->isProduction())<span style="font-size:9px;vertical-align:super">soon</span>@endif
            </a>
        </div>
        <div style="margin-left:auto;display:flex;align-items:center;gap:6px">
            @auth

            {{-- Workspace switcher (desktop only) --}}
            @isset($currentWorkspace)
            <div class="dropdown d-none d-lg-flex">
                <button class="dropdown-toggle" data-bs-toggle="dropdown"
                    style="background:var(--bg-elevated);border:1px solid var(--border-base);border-radius:7px;
                           color:var(--text-1);font-family:var(--font-mono);font-size:11px;
                           padding:5px 10px 5px 8px;cursor:pointer;display:flex;align-items:center;gap:6px;
                           transition:border-color .15s">
                    <span style="width:8px;height:8px;border-radius:2px;background:{{ $currentWorkspace->color }};flex-shrink:0"></span>
                    {{ $currentWorkspace->name }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="min-width:200px">
                    @foreach($allWorkspaces as $ws)
                    <li>
                        <form method="POST" action="{{ route('workspaces.switch', $ws) }}">
                            @csrf
                            <button type="submit" class="dropdown-item d-flex align-items-center gap-2">
                                <span style="width:8px;height:8px;border-radius:2px;background:{{ $ws->color }};flex-shrink:0"></span>
                                {{ $ws->name }}
                                @if($ws->id === $currentWorkspace->id)
                                    <i class="bi bi-check ms-auto" style="color:var(--accent)"></i>
                                @endif
                                @if($ws->id === auth()->user()->default_workspace_id)
                                    <span style="font-family:var(--font-mono);font-size:8px;color:var(--text-3);margin-left:auto">default</span>
                                @endif
                            </button>
                        </form>
                    </li>
                    @endforeach
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item small" href="{{ route('workspaces.create') }}">
                        <i class="bi bi-plus me-1"></i>New workspace
                    </a></li>
                </ul>
            </div>
            @endisset

            {{-- Profile link (desktop only) --}}
            <a href="{{ route('profile.show') }}" class="d-none d-lg-block"
               style="font-family:var(--font-mono);font-size:11px;color:var(--text-3);
                      text-decoration:none;padding:5px 8px;border-radius:6px;transition:all .15s"
               onmouseover="this.style.color='var(--text-1)';this.style.background='rgba(255,255,255,.05)'"
               onmouseout="this.style.color='var(--text-3)';this.style.background='transparent'">
                {{ auth()->user()->name }}
            </a>

            {{-- Sign out (desktop only) --}}
            <form method="POST" action="/logout" style="margin:0" class="d-none d-lg-block">
                @csrf
                <button type="submit"
                    style="background:none;border:1px solid var(--border-faint);border-radius:6px;
                           color:var(--text-3);font-family:var(--font-mono);font-size:10px;
                           padding:4px 10px;cursor:pointer;transition:all .15s"
                    onmouseover="this.style.color='var(--text-1)';this.style.borderColor='var(--border-base)'"
                    onmouseout="this.style.color='var(--text-3)';this.style.borderColor='var(--border-faint)'">
                    Sign out
                </button>
            </form>

            {{-- Burger button (mobile only) --}}
            <button class="wt-burger" onclick="document.getElementById('wt-mob-menu').classList.toggle('open')" aria-label="Menu">
                <i class="bi bi-list" style="font-size:20px"></i>
            </button>
            @endauth
        </div>
    </nav>

    {{-- Mobile off-canvas menu --}}
    @auth
    <div class="wt-mob-menu" id="wt-mob-menu">
        <div class="wt-mob-menu-backdrop" onclick="document.getElementById('wt-mob-menu').classList.remove('open')"></div>
        <div class="wt-mob-menu-panel">

            {{-- Workspace section --}}
            @isset($currentWorkspace)
            <div style="padding:16px 20px 12px;border-bottom:1px solid var(--border-faint)">
                <p style="font-family:var(--font-mono);font-size:9px;font-weight:500;text-transform:uppercase;letter-spacing:.12em;color:var(--text-3);margin-bottom:10px">Workspace</p>
                @foreach($allWorkspaces as $ws)
                <form method="POST" action="{{ route('workspaces.switch', $ws) }}" style="margin:0">
                    @csrf
                    <button type="submit" style="display:flex;align-items:center;gap:10px;width:100%;background:{{ $ws->id === $currentWorkspace->id ? 'var(--bg-hover)' : 'none' }};border:0;border-radius:6px;color:var(--text-1);padding:8px 10px;font-size:13px;text-align:left;cursor:pointer;margin-bottom:2px;transition:background .15s">
                        <span style="width:8px;height:8px;border-radius:2px;background:{{ $ws->color }};flex-shrink:0"></span>
                        {{ $ws->name }}
                        @if($ws->id === $currentWorkspace->id)
                            <i class="bi bi-check ms-auto" style="color:var(--accent)"></i>
                        @endif
                    </button>
                </form>
                @endforeach
                <a href="{{ route('workspaces.create') }}" style="display:flex;align-items:center;gap:10px;color:var(--text-3);font-size:12px;text-decoration:none;padding:7px 10px;margin-top:4px">
                    <i class="bi bi-plus"></i>New workspace
                </a>
            </div>
            @endisset

            {{-- Nav links --}}
            <a href="/projects/create" class="wt-mob-menu-link {{ request()->is('projects/create') ? 'active' : '' }}">
                <i class="bi bi-folder-plus"></i>New Project
            </a>
            <a href="/timesheets" class="wt-mob-menu-link {{ request()->is('timesheets*') ? 'active' : '' }}">
                <i class="bi bi-clock"></i>Timesheets
            </a>
            @if(!app()->isProduction())
            <a href="/tools" class="wt-mob-menu-link {{ request()->is('tools*') ? 'active' : '' }}">
                <i class="bi bi-tools"></i>Tools
            </a>
            @endif
            <a href="{{ route('profile.show') }}" class="wt-mob-menu-link {{ request()->is('profile*') ? 'active' : '' }}">
                <i class="bi bi-person"></i>{{ auth()->user()->name }}
            </a>

            {{-- Install App — shown by JS when browser fires beforeinstallprompt --}}
            <button onclick="wtInstall()" id="wt-install-btn" class="wt-mob-menu-link w-100"
                style="border:0;background:none;color:var(--accent);text-align:left;display:none">
                <i class="bi bi-download" style="color:var(--accent)"></i>Install App
            </button>

            {{-- iOS hint — shown by JS when on iOS Safari (no beforeinstallprompt) --}}
            <div id="wt-ios-hint" style="display:none;padding:12px 20px;font-family:var(--font-mono);font-size:11px;color:var(--text-2);border-bottom:1px solid var(--border-faint);line-height:1.6">
                <i class="bi bi-apple" style="margin-right:6px;color:var(--text-3)"></i>
                To install: tap <strong style="color:var(--text-1)">Share</strong>
                <i class="bi bi-box-arrow-up" style="font-size:11px;margin:0 2px"></i>
                then <strong style="color:var(--text-1)">Add to Home Screen</strong>
            </div>

            {{-- Update App --}}
            <button onclick="wtUpdate()" id="wt-update-btn" class="wt-mob-menu-link w-100"
                style="border:0;background:none;color:var(--text-2);text-align:left">
                <i class="bi bi-arrow-clockwise" id="wt-update-icon"></i>
                <span id="wt-update-label">Check for update</span>
            </button>

            <form method="POST" action="/logout" style="margin:0">
                @csrf
                <button type="submit" class="wt-mob-menu-link w-100" style="border:0;background:none;color:var(--danger);text-align:left">
                    <i class="bi bi-box-arrow-right" style="color:var(--danger)"></i>Sign out
                </button>
            </form>

        </div>
    </div>
    @endauth

    {{ $slot }}

    {{-- Install banner — sits above the bottom nav, shown by JS --}}
    <div id="wt-install-banner" class="d-lg-none"
        style="display:none;position:fixed;bottom:56px;left:0;right:0;z-index:390;
               background:var(--bg-elevated);border-top:1px solid var(--border-base);
               padding:12px 16px;align-items:center;gap:10px">
        <i class="bi bi-app" style="font-size:22px;color:var(--accent);flex-shrink:0"></i>
        <div style="flex:1;min-width:0">
            <div style="font-size:13px;font-weight:600;color:var(--text-1)">Add to Home Screen</div>
            <div style="font-family:var(--font-mono);font-size:10px;color:var(--text-3)">Install for quick access</div>
        </div>
        <button onclick="wtInstall()" style="background:var(--accent);border:none;border-radius:7px;color:#fff;font-family:var(--font-mono);font-size:11px;padding:7px 14px;cursor:pointer;flex-shrink:0">Install</button>
        <button onclick="wtDismissBanner()" style="background:none;border:none;color:var(--text-3);font-size:18px;cursor:pointer;padding:0 4px;flex-shrink:0;line-height:1">×</button>
    </div>

    <nav class="wt-mob-nav">
        <a href="/"         class="wt-mob-tab {{ request()->is('/') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2"></i>Dashboard
        </a>
        <a href="/handoffs" class="wt-mob-tab {{ request()->is('handoffs*') ? 'active' : '' }}">
            <i class="bi bi-arrow-left-right"></i>Handoffs
        </a>
        <a href="/update"   class="wt-mob-tab {{ request()->is('update*') ? 'active' : '' }}">
            <i class="bi bi-send"></i>Update
        </a>
        <a href="/clients"  class="wt-mob-tab {{ request()->is('clients*') ? 'active' : '' }}">
            <i class="bi bi-building"></i>Clients
        </a>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts
    <script>
        /* ── Service worker registration ── */
        let _swReg = null;
        let _reloadOnController = false;

        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(reg => {
                    _swReg = reg;
                    // If a new SW is already waiting (page was open across a deploy), offer update
                    if (reg.waiting) wtShowUpdateReady();
                    reg.addEventListener('updatefound', () => {
                        const sw = reg.installing;
                        sw.addEventListener('statechange', function() {
                            if (this.state === 'installed' && navigator.serviceWorker.controller) {
                                wtShowUpdateReady();
                            }
                        });
                    });
                })
                .catch(() => {});

            // When a new SW takes control, reload to pick up changes
            navigator.serviceWorker.addEventListener('controllerchange', () => {
                if (_reloadOnController) window.location.reload();
            });
        }

        function wtShowUpdateReady() {
            const lbl = document.getElementById('wt-update-label');
            const icon = document.getElementById('wt-update-icon');
            if (lbl) lbl.textContent = 'Update ready — tap to reload';
            if (icon) { icon.style.color = 'var(--accent)'; }
            const btn = document.getElementById('wt-update-btn');
            if (btn) btn.style.color = 'var(--accent)';
        }

        async function wtUpdate() {
            if (!('serviceWorker' in navigator)) return;
            const lbl   = document.getElementById('wt-update-label');
            const icon  = document.getElementById('wt-update-icon');
            if (lbl) lbl.textContent = 'Checking…';
            if (icon) icon.style.animation = 'spin .8s linear infinite';

            try {
                const reg = _swReg || await navigator.serviceWorker.getRegistration('/');
                if (!reg) { if (lbl) lbl.textContent = 'No SW found'; return; }

                if (reg.waiting) {
                    // Already has a new SW waiting — activate it
                    _reloadOnController = true;
                    reg.waiting.postMessage({ type: 'SKIP_WAITING' });
                    return;
                }

                await reg.update();

                // Give the browser 4 s to find + install the new SW
                let found = false;
                reg.addEventListener('updatefound', () => {
                    found = true;
                    const sw = reg.installing;
                    sw.addEventListener('statechange', function() {
                        if (this.state === 'installed') {
                            _reloadOnController = true;
                            this.postMessage({ type: 'SKIP_WAITING' });
                        }
                    });
                });

                setTimeout(() => {
                    if (!found && !_reloadOnController) {
                        if (lbl) lbl.textContent = 'Already up to date';
                        if (icon) icon.style.animation = '';
                        setTimeout(() => {
                            if (lbl) lbl.textContent = 'Check for update';
                            if (icon) { icon.style.color = ''; }
                            const btn = document.getElementById('wt-update-btn');
                            if (btn) btn.style.color = '';
                        }, 2500);
                    }
                }, 4000);

            } catch(e) {
                if (lbl) lbl.textContent = 'Check failed';
                if (icon) icon.style.animation = '';
                setTimeout(() => { if (lbl) lbl.textContent = 'Check for update'; }, 2000);
            }
        }

        /* ── PWA install prompt ── */
        let _installPrompt = null;

        window.addEventListener('beforeinstallprompt', e => {
            e.preventDefault();
            _installPrompt = e;
            // Show banner (if not previously dismissed this session)
            if (!sessionStorage.getItem('wt-banner-dismissed')) {
                const banner = document.getElementById('wt-install-banner');
                if (banner) banner.style.display = 'flex';
            }
            // Also show button in off-canvas menu
            const btn = document.getElementById('wt-install-btn');
            if (btn) btn.style.display = 'flex';
        });

        window.addEventListener('appinstalled', () => {
            _installPrompt = null;
            const banner = document.getElementById('wt-install-banner');
            if (banner) banner.style.display = 'none';
            const btn = document.getElementById('wt-install-btn');
            if (btn) btn.style.display = 'none';
        });

        function wtInstall() {
            if (!_installPrompt) return;
            _installPrompt.prompt();
            _installPrompt.userChoice.then(() => {
                _installPrompt = null;
                wtDismissBanner();
            });
        }

        function wtDismissBanner() {
            const banner = document.getElementById('wt-install-banner');
            if (banner) banner.style.display = 'none';
            sessionStorage.setItem('wt-banner-dismissed', '1');
        }

        // iOS Safari — no beforeinstallprompt
        (function() {
            const isIos = /iphone|ipad|ipod/i.test(navigator.userAgent);
            const isInStandalone = ('standalone' in navigator) && navigator.standalone;
            if (isIos && !isInStandalone) {
                const hint = document.getElementById('wt-ios-hint');
                if (hint) hint.style.display = 'block';
            }
        })();
    </script>
    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</body>
</html>
