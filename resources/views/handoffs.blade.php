<x-layouts.app>
<div class="container-lg py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h5 fw-semibold mb-1" style="font-family:var(--font-display)">Handoffs</h1>
            <p style="font-family:var(--font-mono);font-size:11px;color:var(--text-3);margin:0">
                Open exchanges between you and others, across all projects
            </p>
        </div>
        <button class="wt-btn wt-btn-accent" style="padding:7px 16px" data-bs-toggle="modal" data-bs-target="#addDepModal">
            <i class="bi bi-plus-lg" style="font-size:11px"></i> New Handoff
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 small mb-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($waitingOn->isEmpty() && $iOwe->isEmpty())
        <div class="card border-0 text-center py-5" style="background:var(--bg-raised);border:1px solid var(--border-faint)!important">
            <div style="font-size:32px;margin-bottom:12px">🤝</div>
            <p class="fw-medium mb-1" style="color:var(--text-1)">No open handoffs</p>
            <p style="font-family:var(--font-mono);font-size:11px;color:var(--text-3);margin-bottom:20px">
                When you're waiting on someone, or someone is waiting on you, it appears here.
            </p>
            <div>
                <button class="wt-btn wt-btn-accent" style="padding:7px 16px" data-bs-toggle="modal" data-bs-target="#addDepModal">
                    Add first handoff
                </button>
            </div>
        </div>
    @else
    <div class="row g-4">

        {{-- I'm Waiting On --}}
        <div class="col-md-6">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span style="width:8px;height:8px;border-radius:50%;background:var(--warning);animation:pip-pulse 2s ease-in-out infinite"></span>
                <span style="font-family:var(--font-mono);font-size:10px;font-weight:500;text-transform:uppercase;letter-spacing:.12em;color:var(--warning)">
                    Waiting On Others
                </span>
                <span style="font-family:var(--font-mono);font-size:9px;color:var(--text-3);margin-left:auto">
                    {{ $waitingOn->flatten()->count() }} open
                </span>
            </div>

            @if($waitingOn->isEmpty())
                <div class="card border-0 text-center py-4" style="background:var(--bg-raised);border:1px solid var(--border-faint)!important">
                    <p style="font-family:var(--font-mono);font-size:11px;color:var(--text-3);margin:0">Nothing blocked right now.</p>
                </div>
            @else
            <div class="d-flex flex-column gap-3">
                @foreach($waitingOn as $projectId => $deps)
                @php $project = $deps->first()->task?->project ?? $deps->first()->project; @endphp
                <div class="card border-0" style="background:var(--bg-raised);border:1px solid var(--border-faint)!important">
                    {{-- Project header --}}
                    <div style="padding:8px 14px;border-bottom:1px solid var(--border-faint);display:flex;align-items:center;gap:7px">
                        <span style="width:7px;height:7px;border-radius:2px;background:{{ $project?->color ?? '#7c7c98' }};flex-shrink:0"></span>
                        <span style="font-family:var(--font-mono);font-size:9.5px;font-weight:500;color:var(--text-3);text-transform:uppercase;letter-spacing:.08em">
                            {{ $project?->name ?? 'No project' }}
                        </span>
                    </div>

                    <div class="d-flex flex-column">
                        @foreach($deps as $dep)
                        @php $overdue = $dep->isOverdue(); @endphp
                        <div class="d-flex align-items-start gap-3 p-3" style="border-bottom:1px solid var(--border-faint);{{ $overdue ? 'background:rgba(255,107,123,.04)' : '' }}">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span style="font-size:13px;font-weight:600;color:var(--warning)">{{ $dep->person->name }}</span>
                                    @if($overdue)
                                        <span style="font-family:var(--font-mono);font-size:9px;color:var(--danger);background:var(--danger-bg);padding:1px 6px;border-radius:3px">OVERDUE</span>
                                    @elseif($dep->needed_by)
                                        <span style="font-family:var(--font-mono);font-size:9px;color:var(--text-3)">by {{ $dep->needed_by->format('d M') }}</span>
                                    @endif
                                </div>
                                <p style="font-size:12px;color:var(--text-2);margin:0 0 4px">{{ $dep->description }}</p>
                                @if($dep->task)
                                    <a href="{{ route('tasks.show', $dep->task) }}" style="font-family:var(--font-mono);font-size:10px;color:var(--text-3);text-decoration:none">
                                        <i class="bi bi-check2-square me-1"></i>{{ $dep->task->title }}
                                    </a>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('dependencies.resolve', $dep) }}" class="flex-shrink-0">
                                @csrf
                                <button type="submit" class="wt-btn" style="padding:4px 10px;font-size:10px;color:var(--success);border-color:rgba(45,212,170,.3)">
                                    Got it ✓
                                </button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- They're Waiting On Me --}}
        <div class="col-md-6">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span style="width:8px;height:8px;border-radius:50%;background:var(--success)"></span>
                <span style="font-family:var(--font-mono);font-size:10px;font-weight:500;text-transform:uppercase;letter-spacing:.12em;color:var(--success)">
                    They Need From Me
                </span>
                <span style="font-family:var(--font-mono);font-size:9px;color:var(--text-3);margin-left:auto">
                    {{ $iOwe->flatten()->count() }} open
                </span>
            </div>

            @if($iOwe->isEmpty())
                <div class="card border-0 text-center py-4" style="background:var(--bg-raised);border:1px solid var(--border-faint)!important">
                    <p style="font-family:var(--font-mono);font-size:11px;color:var(--text-3);margin:0">No outstanding commitments.</p>
                </div>
            @else
            <div class="d-flex flex-column gap-3">
                @foreach($iOwe as $projectId => $deps)
                @php $project = $deps->first()->task?->project ?? $deps->first()->project; @endphp
                <div class="card border-0" style="background:var(--bg-raised);border:1px solid var(--border-faint)!important">
                    <div style="padding:8px 14px;border-bottom:1px solid var(--border-faint);display:flex;align-items:center;gap:7px">
                        <span style="width:7px;height:7px;border-radius:2px;background:{{ $project?->color ?? '#7c7c98' }};flex-shrink:0"></span>
                        <span style="font-family:var(--font-mono);font-size:9.5px;font-weight:500;color:var(--text-3);text-transform:uppercase;letter-spacing:.08em">
                            {{ $project?->name ?? 'No project' }}
                        </span>
                    </div>

                    <div class="d-flex flex-column">
                        @foreach($deps as $dep)
                        @php $overdue = $dep->isOverdue(); @endphp
                        <div class="d-flex align-items-start gap-3 p-3" style="border-bottom:1px solid var(--border-faint);{{ $overdue ? 'background:rgba(255,107,123,.04)' : '' }}">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span style="font-size:13px;font-weight:600;color:var(--success)">{{ $dep->person->name }}</span>
                                    @if($overdue)
                                        <span style="font-family:var(--font-mono);font-size:9px;color:var(--danger);background:var(--danger-bg);padding:1px 6px;border-radius:3px">OVERDUE</span>
                                    @elseif($dep->needed_by)
                                        <span style="font-family:var(--font-mono);font-size:9px;color:var(--text-3)">by {{ $dep->needed_by->format('d M') }}</span>
                                    @endif
                                </div>
                                <p style="font-size:12px;color:var(--text-2);margin:0 0 4px">{{ $dep->description }}</p>
                                @if($dep->task)
                                    <a href="{{ route('tasks.show', $dep->task) }}" style="font-family:var(--font-mono);font-size:10px;color:var(--text-3);text-decoration:none">
                                        <i class="bi bi-check2-square me-1"></i>{{ $dep->task->title }}
                                    </a>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('dependencies.resolve', $dep) }}" class="flex-shrink-0">
                                @csrf
                                <button type="submit" class="wt-btn" style="padding:4px 10px;font-size:10px;color:var(--success);border-color:rgba(45,212,170,.3)">
                                    Done ✓
                                </button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>
    @endif

</div>

{{-- Add Handoff Modal --}}
<div class="modal fade" id="addDepModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('handoffs.store') }}">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-semibold">New Handoff</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body d-flex flex-column gap-3">

                    <div>
                        <label class="wt-mono-label">Direction</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="direction" value="waiting_on" id="dir-waiting" checked>
                                <label class="form-check-label small" for="dir-waiting">I'm waiting on someone</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="direction" value="i_owe" id="dir-owe">
                                <label class="form-check-label small" for="dir-owe">Someone is waiting on me</label>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="wt-mono-label">Person</label>
                        <input type="text" name="person_name" class="form-control"
                               placeholder="Name" list="handoff-people" required />
                        <datalist id="handoff-people">
                            @foreach($projects->flatMap(fn($p) => $p->tasks) as $task)
                            @endforeach
                        </datalist>
                    </div>

                    <div>
                        <label class="wt-mono-label">What's needed</label>
                        <input type="text" name="description" class="form-control"
                               placeholder="e.g. API keys, sign-off on copy, PR review" required />
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-7">
                            <label class="wt-mono-label">Linked task <span style="color:var(--text-3)">(optional)</span></label>
                            <select name="task_id" class="form-select form-select-sm" id="handoff-task-select">
                                <option value="">— none —</option>
                                @foreach($projects as $project)
                                    @if($project->tasks->isNotEmpty())
                                    <optgroup label="{{ $project->name }}">
                                        @foreach($project->tasks->whereIn('status', ['working_on','waiting_on','todo']) as $task)
                                            <option value="{{ $task->id }}">{{ $task->title }}</option>
                                        @endforeach
                                    </optgroup>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-5">
                            <label class="wt-mono-label">Needed by <span style="color:var(--text-3)">(optional)</span></label>
                            <input type="date" name="needed_by" class="form-control form-control-sm" />
                        </div>
                    </div>

                    <div id="project-field">
                        <label class="wt-mono-label">Project <span style="color:var(--text-3)">(if no task linked)</span></label>
                        <select name="project_id" class="form-select form-select-sm">
                            <option value="">— none —</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="wt-btn wt-btn-accent" style="padding:6px 20px">Add Handoff</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Hide project field when a task is selected (project is inferred from task)
document.getElementById('handoff-task-select')?.addEventListener('change', function() {
    document.getElementById('project-field').style.display = this.value ? 'none' : '';
});
</script>

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', () => {
        new bootstrap.Modal(document.getElementById('addDepModal')).show();
    });
</script>
@endif
</x-layouts.app>
