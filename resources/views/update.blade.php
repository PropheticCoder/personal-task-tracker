<x-layouts.app>
@php
$sinceLabels = ['yesterday' => 'Since yesterday 5pm', 'today' => 'Since midnight', 'week' => 'This week'];
$windows = ['yesterday' => 'Yesterday', 'today' => 'Today', 'week' => 'This week'];
@endphp

<div class="wt-layout d-none d-lg-flex" style="overflow:hidden">

    {{-- Sidebar: controls --}}
    <aside class="wt-sidebar" style="width:240px;min-width:240px">
        <div class="wt-sb-block">
            <span class="wt-mono-label">Daily Update</span>
            <p style="font-size:12px;color:var(--text-2);line-height:1.6;margin:0 0 14px">
                Auto-assembled from your activity. Edit, then copy.
            </p>
            <button onclick="copyAll()" class="wt-btn wt-btn-accent" style="width:100%;justify-content:center;border:none;margin-bottom:8px">
                <i class="bi bi-clipboard" style="font-size:12px"></i> Copy Update
            </button>
        </div>

        <div class="wt-sb-block">
            <span class="wt-mono-label">Time Window</span>
            <div style="display:flex;flex-direction:column;gap:4px">
                @foreach($windows as $key => $label)
                <a href="{{ route('update.index', ['since' => $key]) }}"
                   style="font-family:var(--font-mono);font-size:11px;padding:5px 8px;border-radius:5px;text-decoration:none;transition:background .15s;
                          color:{{ $since === $key ? 'var(--accent)' : 'var(--text-2)' }};
                          background:{{ $since === $key ? 'var(--accent-bg)' : 'transparent' }}">
                    {{ $label }}
                </a>
                @endforeach
            </div>
            <p style="font-family:var(--font-mono);font-size:9.5px;color:var(--text-3);margin:10px 0 0">
                {{ $sinceLabels[$since] }}
            </p>
        </div>

        <div class="wt-sb-block">
            <span class="wt-mono-label">Summary</span>
            @php
                $totalDone    = $projectUpdates->sum(fn($p) => $p['done']->count());
                $totalActive  = $projectUpdates->sum(fn($p) => $p['inProgress']->count());
                $totalBlocked = $projectUpdates->sum(fn($p) => $p['waitingOn']->count());
                $totalOwed    = $projectUpdates->sum(fn($p) => $p['commitments']->count());
            @endphp
            <dl style="font-family:var(--font-mono);font-size:10px;margin:0">
                <div style="display:flex;justify-content:space-between;margin-bottom:5px">
                    <dt style="color:var(--text-3)">Completed</dt>
                    <dd style="margin:0;color:var(--success);font-weight:600">{{ $totalDone }}</dd>
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:5px">
                    <dt style="color:var(--text-3)">In progress</dt>
                    <dd style="margin:0;color:var(--info)">{{ $totalActive }}</dd>
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:5px">
                    <dt style="color:var(--text-3)">Blocked</dt>
                    <dd style="margin:0;color:var(--warning)">{{ $totalBlocked }}</dd>
                </div>
                <div style="display:flex;justify-content:space-between">
                    <dt style="color:var(--text-3)">Owed</dt>
                    <dd style="margin:0;color:var(--text-2)">{{ $totalOwed }}</dd>
                </div>
            </dl>
        </div>
    </aside>

    {{-- Main content --}}
    <main class="wt-main">

        <div style="margin-bottom:20px">
            <h1 style="font-family:var(--font-display);font-size:20px;font-weight:600;letter-spacing:-0.03em;margin:0 0 3px">
                Daily Update
            </h1>
            <p style="font-family:var(--font-mono);font-size:11px;color:var(--text-3);margin:0">
                {{ now()->format('l, d F Y') }} · {{ $sinceLabels[$since] }}
            </p>
        </div>

        {{-- Tabs --}}
        <div style="display:flex;gap:2px;margin-bottom:20px;border-bottom:1px solid var(--border-faint);padding-bottom:0">
            <button onclick="switchTab('standup')" id="tab-standup"
                style="font-family:var(--font-mono);font-size:11px;padding:7px 14px;background:none;border:none;cursor:pointer;border-bottom:2px solid var(--accent);color:var(--text-1);margin-bottom:-1px;transition:color .15s">
                Team Update
            </button>
            <button onclick="switchTab('done')" id="tab-done"
                style="font-family:var(--font-mono);font-size:11px;padding:7px 14px;background:none;border:none;cursor:pointer;border-bottom:2px solid transparent;color:var(--text-3);margin-bottom:-1px;transition:color .15s">
                What I've Done
            </button>
        </div>

        @if($projectUpdates->isEmpty())
        <div class="wt-card text-center" style="padding:48px 24px">
            <div style="font-size:28px;margin-bottom:12px">📋</div>
            <p style="font-family:var(--font-mono);font-size:11px;color:var(--text-3);margin:0">
                No activity found for this window. Try a wider time range.
            </p>
        </div>
        @endif

        {{-- Tab: Team Update (standup) --}}
        <div id="panel-standup">
            <div style="display:flex;flex-direction:column;gap:14px">
                @foreach($projectUpdates as $idx => $u)
                @php $p = $u['project']; @endphp
                <div class="wt-card">
                    <div style="height:2px;background:{{ $p->color ?? '#7c7c98' }}"></div>
                    <div style="padding:12px 18px 10px;border-bottom:1px solid var(--border-faint);display:flex;align-items:center;gap:10px">
                        <span style="font-family:var(--font-display);font-size:15px;font-weight:600;letter-spacing:-0.02em;color:var(--text-1)">
                            {{ $p->name }}
                        </span>
                        @if($p->client)
                            <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-3)">{{ $p->client->name }}</span>
                        @endif
                        <button onclick="copyProject({{ $idx }})" class="wt-btn" style="margin-left:auto;padding:3px 10px;font-size:10px">
                            <i class="bi bi-clipboard" style="font-size:10px"></i> Copy
                        </button>
                    </div>
                    <div style="padding:14px 18px">
                        <textarea class="wt-update-textarea" id="textarea-{{ $idx }}"
                            style="width:100%;background:transparent;border:none;outline:none;resize:none;color:var(--text-1);font-family:var(--font-body);font-size:13px;line-height:1.8;min-height:60px"
                            oninput="autoResize(this)"
                        >{{ $u['standup'] }}</textarea>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Tab: What I've Done --}}
        <div id="panel-done" style="display:none">
            <div style="display:flex;flex-direction:column;gap:14px">
                @foreach($projectUpdates as $u)
                @php
                    $p = $u['project'];
                    $hasDone = $u['done']->isNotEmpty() || $u['activityLog']->isNotEmpty();
                @endphp
                @if($hasDone)
                <div class="wt-card">
                    <div style="height:2px;background:{{ $p->color ?? '#7c7c98' }}"></div>
                    <div style="padding:12px 18px 10px;border-bottom:1px solid var(--border-faint);display:flex;align-items:center;gap:10px">
                        <span style="font-family:var(--font-display);font-size:15px;font-weight:600;letter-spacing:-0.02em;color:var(--text-1)">
                            {{ $p->name }}
                        </span>
                        @if($p->client)
                            <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-3)">{{ $p->client->name }}</span>
                        @endif
                    </div>
                    <div style="padding:14px 18px;display:flex;flex-direction:column;gap:12px">

                        @if($u['done']->isNotEmpty())
                        <div>
                            <div style="font-family:var(--font-mono);font-size:9px;font-weight:500;text-transform:uppercase;letter-spacing:.1em;color:var(--success);margin-bottom:8px">
                                ✅ Completed
                            </div>
                            @foreach($u['done'] as $task)
                            <div style="display:flex;align-items:center;gap:8px;padding:5px 0;border-bottom:1px solid var(--border-faint)">
                                <i class="bi bi-check-circle-fill" style="color:var(--success);font-size:13px;flex-shrink:0"></i>
                                <a href="{{ route('tasks.show', $task) }}" style="font-size:13px;color:var(--text-1);text-decoration:none;flex:1">
                                    {{ $task->title }}
                                </a>
                                <span style="font-family:var(--font-mono);font-size:9px;color:var(--text-3)">
                                    {{ $task->updated_at->format('d M, H:i') }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        @if($u['activityLog']->isNotEmpty())
                        @php $actId = 'act-' . $loop->index; @endphp
                        <div>
                            <div style="display:flex;align-items:center;justify-content:space-between;cursor:pointer;user-select:none;margin-bottom:8px"
                                 data-bs-toggle="collapse" data-bs-target="#{{ $actId }}">
                                <span style="font-family:var(--font-mono);font-size:9px;font-weight:500;text-transform:uppercase;letter-spacing:.1em;color:var(--info)">
                                    📝 Activity Notes
                                    <span style="color:var(--text-3);margin-left:4px">{{ $u['activityLog']->count() }}</span>
                                </span>
                                <i class="bi bi-chevron-down" style="font-size:10px;color:var(--text-3);transition:transform .2s" id="chev-{{ $actId }}"></i>
                            </div>
                            <div class="collapse show" id="{{ $actId }}">
                            @foreach($u['activityLog'] as $activity)
                            <div style="padding:6px 0;border-bottom:1px solid var(--border-faint)">
                                <div style="display:flex;align-items:flex-start;gap:8px">
                                    <i class="bi bi-chat-left-text" style="color:var(--text-3);font-size:12px;margin-top:2px;flex-shrink:0"></i>
                                    <div style="flex:1">
                                        <div style="font-size:13px;color:var(--text-1);font-style:italic;margin-bottom:2px">
                                            "{{ $activity->body }}"
                                        </div>
                                        @if($activity->task)
                                        <div style="font-family:var(--font-mono);font-size:10px;color:var(--text-3)">
                                            {{ $activity->task->title }}
                                        </div>
                                        @endif
                                    </div>
                                    <div style="text-align:right;flex-shrink:0">
                                        @if($activity->hours)
                                        <div style="font-family:var(--font-display);font-size:14px;font-weight:700;color:var(--accent)">{{ $activity->hours }}h</div>
                                        @endif
                                        <div style="font-family:var(--font-mono);font-size:9px;color:var(--text-3)">{{ $activity->created_at->format('H:i') }}</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            </div>{{-- /.collapse --}}
                        </div>
                        @endif

                    </div>
                </div>
                @endif
                @endforeach

                @if($projectUpdates->every(fn($u) => $u['done']->isEmpty() && $u['activityLog']->isEmpty()))
                <div class="wt-card text-center" style="padding:48px 24px">
                    <p style="font-family:var(--font-mono);font-size:11px;color:var(--text-3);margin:0">
                        No completed tasks or activity notes in this window.
                    </p>
                </div>
                @endif

            </div>
        </div>

    </main>
</div>

{{-- Mobile --}}
<div class="d-lg-none" style="padding:16px 16px 80px;position:relative;z-index:1">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
        <h1 style="font-family:var(--font-display);font-size:18px;font-weight:600;letter-spacing:-0.03em;margin:0">Update</h1>
        <button onclick="copyAll()" class="wt-btn wt-btn-accent" style="padding:5px 12px;font-size:10px">Copy</button>
    </div>

    {{-- Time window filter --}}
    <div style="display:flex;gap:8px;margin-bottom:16px">
        @foreach($windows as $key => $label)
        <a href="{{ route('update.index', ['since' => $key]) }}"
           style="font-family:var(--font-mono);font-size:11px;padding:9px 14px;border-radius:7px;text-decoration:none;
                  display:inline-flex;align-items:center;min-height:42px;
                  border:1px solid {{ $since === $key ? 'var(--accent)' : 'var(--border-base)' }};
                  background:{{ $since === $key ? 'var(--accent-bg)' : 'var(--bg-elevated)' }};
                  color:{{ $since === $key ? 'var(--accent)' : 'var(--text-2)' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- Content tabs --}}
    <div style="display:flex;gap:2px;margin-bottom:16px;border-bottom:1px solid var(--border-faint);padding-bottom:0">
        <button onclick="switchMobTab('standup')" id="mob-tab-standup"
            style="font-family:var(--font-mono);font-size:11px;padding:7px 14px;background:none;border:none;cursor:pointer;border-bottom:2px solid var(--accent);color:var(--text-1);margin-bottom:-1px;transition:color .15s">
            Team Update
        </button>
        <button onclick="switchMobTab('done')" id="mob-tab-done"
            style="font-family:var(--font-mono);font-size:11px;padding:7px 14px;background:none;border:none;cursor:pointer;border-bottom:2px solid transparent;color:var(--text-3);margin-bottom:-1px;transition:color .15s">
            What I've Done
        </button>
    </div>

    {{-- Team Update panel --}}
    <div id="mob-panel-standup">
        @foreach($projectUpdates as $idx => $u)
        @php $p = $u['project']; @endphp
        <div class="wt-card" style="margin-bottom:12px">
            <div style="height:2px;background:{{ $p->color ?? '#7c7c98' }}"></div>
            <div style="padding:12px 14px 10px;border-bottom:1px solid var(--border-faint)">
                <span style="font-family:var(--font-display);font-size:14px;font-weight:600;color:var(--text-1)">{{ $p->name }}</span>
                @if($p->client)<span style="font-family:var(--font-mono);font-size:9px;color:var(--text-3);margin-left:8px">{{ $p->client->name }}</span>@endif
            </div>
            <div style="padding:12px 14px">
                <textarea class="wt-update-textarea"
                    style="width:100%;background:transparent;border:none;outline:none;resize:none;color:var(--text-1);font-family:var(--font-body);font-size:12px;line-height:1.7;min-height:60px"
                    oninput="autoResize(this)"
                >{{ $u['standup'] }}</textarea>
            </div>
        </div>
        @endforeach
    </div>

    {{-- What I've Done panel --}}
    <div id="mob-panel-done" style="display:none">
        @forelse($projectUpdates as $u)
        @php
            $p = $u['project'];
            $hasDone = $u['done']->isNotEmpty() || $u['activityLog']->isNotEmpty();
        @endphp
        @if($hasDone)
        <div class="wt-card" style="margin-bottom:12px">
            <div style="height:2px;background:{{ $p->color ?? '#7c7c98' }}"></div>
            <div style="padding:12px 14px 10px;border-bottom:1px solid var(--border-faint)">
                <span style="font-family:var(--font-display);font-size:14px;font-weight:600;color:var(--text-1)">{{ $p->name }}</span>
                @if($p->client)<span style="font-family:var(--font-mono);font-size:9px;color:var(--text-3);margin-left:8px">{{ $p->client->name }}</span>@endif
            </div>
            <div style="padding:12px 14px;display:flex;flex-direction:column;gap:10px">

                @if($u['done']->isNotEmpty())
                <div>
                    <div style="font-family:var(--font-mono);font-size:9px;font-weight:500;text-transform:uppercase;letter-spacing:.1em;color:var(--success);margin-bottom:8px">✅ Completed</div>
                    @foreach($u['done'] as $task)
                    <div style="display:flex;align-items:center;gap:8px;padding:5px 0;border-bottom:1px solid var(--border-faint)">
                        <i class="bi bi-check-circle-fill" style="color:var(--success);font-size:13px;flex-shrink:0"></i>
                        <a href="{{ route('tasks.show', $task) }}" style="font-size:12px;color:var(--text-1);text-decoration:none;flex:1">{{ $task->title }}</a>
                    </div>
                    @endforeach
                </div>
                @endif

                @if($u['activityLog']->isNotEmpty())
                <div>
                    <div style="font-family:var(--font-mono);font-size:9px;font-weight:500;text-transform:uppercase;letter-spacing:.1em;color:var(--info);margin-bottom:8px">
                        📝 Activity Notes <span style="color:var(--text-3)">{{ $u['activityLog']->count() }}</span>
                    </div>
                    @foreach($u['activityLog'] as $activity)
                    <div style="padding:5px 0;border-bottom:1px solid var(--border-faint)">
                        <div style="display:flex;align-items:flex-start;gap:8px">
                            <i class="bi bi-chat-left-text" style="color:var(--text-3);font-size:11px;margin-top:2px;flex-shrink:0"></i>
                            <div style="flex:1">
                                <div style="font-size:12px;color:var(--text-1);font-style:italic">"{{ $activity->body }}"</div>
                                @if($activity->task)
                                <div style="font-family:var(--font-mono);font-size:10px;color:var(--text-3)">{{ $activity->task->title }}</div>
                                @endif
                            </div>
                            @if($activity->hours)
                            <div style="font-family:var(--font-display);font-size:13px;font-weight:700;color:var(--accent);flex-shrink:0">{{ $activity->hours }}h</div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

            </div>
        </div>
        @endif
        @empty
        <div class="wt-card" style="padding:32px 16px;text-align:center">
            <p style="font-family:var(--font-mono);font-size:11px;color:var(--text-3);margin:0">No completed tasks or activity in this window.</p>
        </div>
        @endforelse
    </div>
</div>

<script>
function autoResize(el) {
    el.style.height = 'auto';
    el.style.height = el.scrollHeight + 'px';
}
document.querySelectorAll('.wt-update-textarea').forEach(autoResize);

function switchTab(tab) {
    ['standup','done'].forEach(t => {
        document.getElementById('panel-' + t).style.display = t === tab ? '' : 'none';
        const btn = document.getElementById('tab-' + t);
        btn.style.borderBottomColor = t === tab ? 'var(--accent)' : 'transparent';
        btn.style.color = t === tab ? 'var(--text-1)' : 'var(--text-3)';
    });
}

function switchMobTab(tab) {
    ['standup','done'].forEach(t => {
        document.getElementById('mob-panel-' + t).style.display = t === tab ? '' : 'none';
        const btn = document.getElementById('mob-tab-' + t);
        btn.style.borderBottomColor = t === tab ? 'var(--accent)' : 'transparent';
        btn.style.color = t === tab ? 'var(--text-1)' : 'var(--text-3)';
    });
}

function copyProject(idx) {
    const text = document.getElementById('textarea-' + idx)?.value;
    if (!text) return;
    navigator.clipboard.writeText(text).then(() => {
        const btn = event.target.closest('button');
        btn.innerHTML = '<i class="bi bi-check" style="font-size:10px"></i> Copied';
        setTimeout(() => { btn.innerHTML = '<i class="bi bi-clipboard" style="font-size:10px"></i> Copy'; }, 1500);
    });
}

function copyAll() {
    const texts = Array.from(document.querySelectorAll('.wt-update-textarea'))
        .map(t => t.value.trim()).filter(Boolean)
        .join('\n\n───────────────────\n\n');
    navigator.clipboard.writeText(texts).then(() => {
        const btns = document.querySelectorAll('[onclick="copyAll()"]');
        btns.forEach(b => { b.textContent = '✓ Copied'; setTimeout(() => b.innerHTML = '<i class="bi bi-clipboard" style="font-size:12px"></i> Copy Update', 1500); });
    });
}

// Rotate chevrons on activity note collapse sections
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[id^="act-"]').forEach(el => {
        const chevId = 'chev-' + el.id;
        el.addEventListener('hide.bs.collapse', () => {
            document.getElementById(chevId).style.transform = 'rotate(-90deg)';
        });
        el.addEventListener('show.bs.collapse', () => {
            document.getElementById(chevId).style.transform = 'rotate(0deg)';
        });
    });
});
</script>
</x-layouts.app>
