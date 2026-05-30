<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account — TaskTracker</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,600&family=Plus+Jakarta+Sans:wght@400;500&family=Azeret+Mono:wght@400&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-base:     #13141A; --bg-raised:   #1C1E27; --bg-elevated: #232632;
            --border-faint:rgba(255,255,255,0.08); --border-base: rgba(255,255,255,0.14);
            --text-1:      #EDEEF5; --text-2:      #9294AF; --text-3:      #5C5F7A;
            --accent:      #7C79FF; --accent-bg:   rgba(124,121,255,0.13); --accent-glow: rgba(124,121,255,0.28);
            --danger:      #FF6B7B; --warning:     #F5B53F;
            --font-display:'Bricolage Grotesque',sans-serif; --font-body:'Plus Jakarta Sans',sans-serif; --font-mono:'Azeret Mono',monospace;
        }
        *, *::before, *::after { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; }
        body { background:var(--bg-base);color:var(--text-1);font-family:var(--font-body);display:flex;align-items:center;justify-content:center;min-height:100vh;-webkit-font-smoothing:antialiased; }
        body::before { content:'';position:fixed;inset:0;background-image:radial-gradient(circle,rgba(255,255,255,.026) 1px,transparent 1px);background-size:24px 24px;pointer-events:none; }
        body::after { content:'';position:fixed;top:-120px;left:50%;transform:translateX(-50%);width:480px;height:280px;background:radial-gradient(ellipse,rgba(107,104,255,.07) 0%,transparent 68%);pointer-events:none; }
        .auth-card { position:relative;z-index:1;width:100%;max-width:480px;background:var(--bg-raised);border:1px solid var(--border-faint);border-radius:16px;padding:40px 36px;box-shadow:0 24px 64px rgba(0,0,0,.5);animation:card-up .5s cubic-bezier(.16,1,.3,1) both; }
        @keyframes card-up { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
        .auth-logo { display:block;height:36px;width:auto;margin:0 auto 28px; }
        .auth-title { font-family:var(--font-display);font-size:22px;font-weight:600;letter-spacing:-0.03em;text-align:center;margin:0 0 6px; }
        .auth-sub { font-family:var(--font-mono);font-size:11px;color:var(--text-3);text-align:center;margin:0 0 28px; }
        .auth-label { display:block;font-family:var(--font-mono);font-size:10px;font-weight:500;color:var(--text-2);text-transform:uppercase;letter-spacing:.1em;margin-bottom:7px; }
        .auth-input { width:100%;background:var(--bg-elevated);border:1px solid var(--border-base);border-radius:8px;color:var(--text-1);font-family:var(--font-body);font-size:14px;padding:10px 14px;outline:none;transition:border-color .2s,box-shadow .2s; }
        .auth-input:focus { border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-bg); }
        .auth-input::placeholder { color:var(--text-3); }
        .auth-btn { width:100%;background:var(--accent);border:none;border-radius:8px;color:#fff;font-family:var(--font-mono);font-size:13px;font-weight:500;letter-spacing:.04em;padding:11px;cursor:pointer;transition:opacity .15s,box-shadow .15s;margin-top:6px; }
        .auth-btn:hover { opacity:.88;box-shadow:0 4px 20px var(--accent-glow); }
        .auth-link { text-align:center;font-size:13px;color:var(--text-2);margin-top:20px; }
        .auth-link a { color:var(--accent);text-decoration:none;font-weight:500; }
        .auth-link a:hover { text-decoration:underline; }
        .auth-error { background:rgba(255,92,108,.1);border:1px solid rgba(255,92,108,.25);border-radius:8px;color:var(--danger);font-size:13px;padding:10px 14px;margin-bottom:20px; }

        /* Closed state */
        .auth-closed { text-align:center; }
        .auth-closed-icon { font-size:40px;color:var(--text-3);display:block;margin-bottom:16px; }
        .auth-closed-title { font-family:var(--font-display);font-size:20px;font-weight:600;color:var(--text-2);margin-bottom:8px; }
        .auth-closed-msg { font-size:13px;color:var(--text-3);line-height:1.7; }
    </style>
</head>
<body>
    <div class="auth-card">
        <img src="/logo.png" alt="TaskTracker" class="auth-logo" />

        @if(!config('app.allow_registration'))
        {{-- Registration closed --}}
        <div class="auth-closed">
            <i class="bi bi-lock auth-closed-icon"></i>
            <p class="auth-closed-title">Registration is closed</p>
            <p class="auth-closed-msg">This workspace isn't accepting new accounts right now. If you have an existing account, you can still sign in.</p>
            <a href="/login" style="display:inline-block;margin-top:20px;background:var(--bg-elevated);border:1px solid var(--border-base);border-radius:8px;color:var(--text-1);font-family:var(--font-mono);font-size:12px;padding:9px 20px;text-decoration:none;transition:border-color .15s" onmouseover="this.style.borderColor='var(--accent)'" onmouseout="this.style.borderColor='var(--border-base)'">
                ← Sign In
            </a>
        </div>

        @else
        {{-- Registration open --}}
        <h1 class="auth-title">Create account</h1>
        <p class="auth-sub">Your workspace, your tasks</p>

        @if ($errors->any())
        <div class="auth-error">
            <i class="bi bi-exclamation-circle me-2"></i>{{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="/register">
            @csrf
            <div style="margin-bottom:16px">
                <label class="auth-label">Your name</label>
                <input type="text" name="name" class="auth-input" placeholder="Npfariseni Maphiri" value="{{ old('name') }}" autocomplete="name" required />
            </div>
            <div style="margin-bottom:16px">
                <label class="auth-label">Email</label>
                <input type="email" name="email" class="auth-input" placeholder="you@example.com" value="{{ old('email') }}" autocomplete="email" required />
            </div>
            <div style="margin-bottom:16px">
                <label class="auth-label">Password</label>
                <input type="password" name="password" class="auth-input" placeholder="Min. 8 characters" autocomplete="new-password" required />
            </div>
            <div style="margin-bottom:24px">
                <label class="auth-label">Confirm password</label>
                <input type="password" name="password_confirmation" class="auth-input" placeholder="Repeat password" autocomplete="new-password" required />
            </div>
            <button type="submit" class="auth-btn">Create Account →</button>
        </form>

        <p class="auth-link">Already have an account? <a href="/login">Sign in</a></p>
        @endif
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
