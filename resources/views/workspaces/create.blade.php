<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Workspace — WorkTracker</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,600&family=Plus+Jakarta+Sans:wght@400;500&family=Azeret+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-base:#13141A;--bg-raised:#1C1E27;--bg-elevated:#232632;
            --border-faint:rgba(255,255,255,.08);--border-base:rgba(255,255,255,.14);
            --text-1:#EDEEF5;--text-2:#9294AF;--text-3:#5C5F7A;
            --accent:#7C79FF;--accent-bg:rgba(124,121,255,.13);
            --font-display:'Bricolage Grotesque',sans-serif;
            --font-body:'Plus Jakarta Sans',sans-serif;
            --font-mono:'Azeret Mono',monospace;
        }
        *,*::before,*::after{box-sizing:border-box}
        html,body{height:100%;margin:0}
        body{background:var(--bg-base);color:var(--text-1);font-family:var(--font-body);
             display:flex;align-items:center;justify-content:center;min-height:100vh;
             -webkit-font-smoothing:antialiased}
        body::before{content:'';position:fixed;inset:0;
            background-image:radial-gradient(circle,rgba(255,255,255,.038) 1px,transparent 1px);
            background-size:28px 28px;pointer-events:none}
        .ws-card{position:relative;z-index:1;width:100%;max-width:480px;
            background:var(--bg-raised);border:1px solid var(--border-faint);
            border-radius:16px;padding:40px 40px;
            box-shadow:0 24px 64px rgba(0,0,0,.5);
            animation:up .5s cubic-bezier(.16,1,.3,1) both}
        @keyframes up{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
        .ws-title{font-family:var(--font-display);font-size:22px;font-weight:600;
            letter-spacing:-.03em;margin:0 0 6px}
        .ws-sub{font-family:var(--font-mono);font-size:11px;color:var(--text-3);margin:0 0 28px}
        label.sm{font-family:var(--font-mono);font-size:10px;font-weight:500;
            color:var(--text-2);text-transform:uppercase;letter-spacing:.1em;
            display:block;margin-bottom:7px}
        .form-control,.form-select{
            background:var(--bg-elevated)!important;border:1px solid var(--border-base)!important;
            color:var(--text-1)!important;font-family:var(--font-body);font-size:14px}
        .form-control:focus,.form-select:focus{
            border-color:var(--accent)!important;
            box-shadow:0 0 0 3px var(--accent-bg)!important}
        .form-control::placeholder{color:var(--text-3)!important}
        .ws-btn{width:100%;background:var(--accent);border:none;border-radius:8px;
            color:#fff;font-family:var(--font-mono);font-size:13px;font-weight:500;
            letter-spacing:.04em;padding:11px;cursor:pointer;transition:opacity .15s;margin-top:4px}
        .ws-btn:hover{opacity:.88}
        .color-swatch{width:28px;height:28px;border-radius:6px;cursor:pointer;
            display:flex;align-items:center;justify-content:center;transition:transform .15s}
        .color-swatch:hover{transform:scale(1.1)}
        .color-swatch input{display:none}
    </style>
</head>
<body>
<div class="ws-card">
    <h1 class="ws-title">Create a workspace</h1>
    <p class="ws-sub">Workspaces keep your projects and tasks separate — e.g. Work, Personal, Freelance</p>

    @if($errors->any())
    <div style="background:rgba(255,107,123,.1);border:1px solid rgba(255,107,123,.25);border-radius:8px;color:#FF6B7B;font-size:13px;padding:10px 14px;margin-bottom:20px">
        <i class="bi bi-exclamation-circle me-2"></i>{{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('workspaces.store') }}">
        @csrf

        <div style="margin-bottom:18px">
            <label class="sm">Workspace name</label>
            <input type="text" name="name" class="form-control"
                   value="{{ old('name') }}"
                   placeholder="e.g. Work, Personal, Freelance" autofocus required />
        </div>

        <div style="margin-bottom:18px">
            <label class="sm">Description <span style="font-size:9px;color:var(--text-3)">(optional)</span></label>
            <input type="text" name="description" class="form-control"
                   value="{{ old('description') }}"
                   placeholder="What's this workspace for?" />
        </div>

        <div style="margin-bottom:28px">
            <label class="sm">Colour</label>
            <div style="display:flex;gap:8px;flex-wrap:wrap">
                @foreach(['#7C79FF','#2DD4AA','#FF6B7B','#F5B53F','#4FC4FF','#A855F7','#6c757d'] as $c)
                @php $sel = old('color','#7C79FF') === $c; @endphp
                <label class="color-swatch" style="background:{{ $c }};{{ $sel ? 'outline:3px solid #fff;box-shadow:0 0 0 4px '.$c : '' }}"
                       onchange="updateSwatch(this.querySelector('input'))">
                    <input type="radio" name="color" value="{{ $c }}" {{ $sel ? 'checked' : '' }} />
                    @if($sel)<i class="bi bi-check text-white" style="font-size:14px"></i>@endif
                </label>
                @endforeach
            </div>
        </div>

        <button type="submit" class="ws-btn">Create Workspace →</button>
    </form>

    @if(auth()->user()->workspaces()->exists())
    <p style="text-align:center;font-size:13px;color:var(--text-2);margin-top:18px;margin-bottom:0">
        <a href="{{ route('dashboard') }}" style="color:var(--accent);text-decoration:none">← Back to dashboard</a>
    </p>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function updateSwatch(input) {
    document.querySelectorAll('.color-swatch').forEach(sw => {
        const r = sw.querySelector('input');
        sw.style.outline   = r.checked ? '3px solid #fff' : 'none';
        sw.style.boxShadow = r.checked ? '0 0 0 4px ' + r.value : 'none';
        sw.innerHTML = r.checked
            ? `<input type="radio" name="color" value="${r.value}" checked /><i class="bi bi-check text-white" style="font-size:14px"></i>`
            : `<input type="radio" name="color" value="${r.value}" />`;
    });
}
</script>
</body>
</html>
