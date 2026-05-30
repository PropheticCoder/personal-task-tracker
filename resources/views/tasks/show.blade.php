<x-layouts.app>
@php
    $statusLabels = [
        'todo'       => ['label' => 'To Do',       'color' => 'secondary'],
        'working_on' => ['label' => 'In Progress',  'color' => 'primary'],
        'waiting_on' => ['label' => 'Waiting On',   'color' => 'warning'],
        'done'       => ['label' => 'Done',          'color' => 'success'],
        'archived'   => ['label' => 'Archived',      'color' => 'secondary'],
    ];
    $prioColors = [
        'high' => 'bg-danger-subtle text-danger border border-danger-subtle',
        'med'  => 'bg-warning-subtle text-warning border border-warning-subtle',
        'low'  => 'bg-light text-muted border',
    ];
    $current = $statusLabels[$task->status] ?? ['label' => ucfirst($task->status), 'color' => 'secondary'];
@endphp

<div class="container-lg py-4">

    <a href="{{ url()->previous() === url()->current() ? route('dashboard') : url()->previous() }}"
       class="text-decoration-none text-muted small mb-3 d-inline-flex align-items-center gap-1">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 small mb-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4 mt-0">

        {{-- LEFT: Task detail + Activity --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start gap-2 mb-3">
                        <h1 class="h5 mb-0 fw-semibold flex-grow-1">{{ $task->title }}</h1>
                        <span class="badge bg-{{ $current['color'] }}-subtle text-{{ $current['color'] }} border border-{{ $current['color'] }}-subtle flex-shrink-0">
                            {{ $current['label'] }}
                        </span>
                        <button class="btn btn-link btn-sm p-0 text-muted flex-shrink-0" data-bs-toggle="modal" data-bs-target="#editTaskModal" title="Edit task">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @if($task->priority)
                            <span class="badge {{ $prioColors[$task->priority] }}">
                                <i class="bi bi-flag-fill me-1"></i>{{ strtoupper($task->priority) }}
                            </span>
                        @endif
                        @if($task->needed_by)
                            <span class="badge bg-light border">
                                <i class="bi bi-calendar me-1"></i>{{ $task->needed_by->format('d M Y') }}
                            </span>
                        @endif
                        @if($task->project)
                            <a href="{{ route('projects.show', $task->project) }}" class="badge bg-light border text-decoration-none">
                                <i class="bi bi-folder me-1"></i>{{ $task->project->name }}
                            </a>
                        @endif
                    </div>

                    @if($task->description)
                        <p class="small text-muted mb-3">{{ $task->description }}</p>
                    @endif

                    {{-- Status change actions --}}
                    <div class="d-flex flex-wrap gap-2 pt-3 border-top">
                        <small class="text-muted align-self-center me-1">Move to:</small>
                        @foreach([['todo','Todo','outline-secondary'],['working_on','In Progress','primary'],['waiting_on','Waiting On','warning'],['done','Done','success']] as [$s,$label,$btn])
                            @if($task->status !== $s)
                            <form method="POST" action="{{ route('tasks.update', $task) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="{{ $s }}">
                                <button type="submit" class="btn btn-{{ $btn }} btn-sm">{{ $label }}</button>
                            </form>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Activity timeline --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header border-bottom d-flex align-items-center justify-content-between"
                     style="cursor:pointer;user-select:none" data-bs-toggle="collapse" data-bs-target="#activityBody">
                    <p class="mb-0 fw-medium small">
                        Activity
                        @if($task->activities->isNotEmpty())
                            <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-3);margin-left:6px">
                                {{ $task->activities->count() }}
                            </span>
                        @endif
                    </p>
                    <i class="bi bi-chevron-down" style="font-size:11px;color:var(--text-3);transition:transform .2s" id="activity-chevron"></i>
                </div>
                <div class="collapse show" id="activityBody">
                <div class="card-body p-4">
                    @if($task->activities->isEmpty())
                        <p class="text-muted small mb-4">No activity yet.</p>
                    @else
                    <ul class="list-unstyled d-flex flex-column gap-4 mb-4">
                        @foreach($task->activities->sortByDesc('created_at') as $activity)
                        <li class="d-flex gap-3">
                            @if($activity->type === 'status_change')
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width:28px;height:28px">
                                    <i class="bi bi-arrow-right text-white" style="font-size:12px"></i>
                                </div>
                                <div>
                                    <p class="mb-0 small">
                                        Moved to <strong>{{ $statusLabels[$activity->new_status]['label'] ?? $activity->new_status }}</strong>
                                        @if($activity->old_status) from {{ $statusLabels[$activity->old_status]['label'] ?? $activity->old_status }} @endif
                                    </p>
                                    <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                </div>
                            @elseif($activity->type === 'note')
                                <div class="rounded-circle bg-secondary-subtle d-flex align-items-center justify-content-center flex-shrink-0" style="width:28px;height:28px">
                                    <i class="bi bi-chat-left-text text-secondary" style="font-size:12px"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-0 small fst-italic">"{{ $activity->body }}"</p>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                        @if($activity->hours)
                                            <small style="font-family:var(--font-mono);font-size:9px;color:var(--accent)">{{ $activity->hours }}h</small>
                                        @endif
                                        <form method="POST" action="{{ route('activities.destroy', $activity) }}" class="ms-1">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-link btn-sm p-0 text-muted"
                                                    style="font-size:11px"
                                                    onclick="return confirm('Delete this note?')"
                                                    title="Delete note">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @elseif($activity->type === 'task_added')
                                <div class="rounded-circle bg-success-subtle d-flex align-items-center justify-content-center flex-shrink-0" style="width:28px;height:28px">
                                    <i class="bi bi-plus text-success" style="font-size:12px"></i>
                                </div>
                                <div>
                                    <p class="mb-0 small">Task created</p>
                                    <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                </div>
                            @else
                                <div class="rounded-circle bg-warning-subtle d-flex align-items-center justify-content-center flex-shrink-0" style="width:28px;height:28px">
                                    <i class="bi bi-hourglass text-warning" style="font-size:12px"></i>
                                </div>
                                <div>
                                    <p class="mb-0 small">{{ $activity->body ?? ucfirst(str_replace('_', ' ', $activity->type)) }}</p>
                                    <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                </div>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                    @endif

                    {{-- Add note --}}
                    <div class="border-top pt-3">
                        <form method="POST" action="{{ route('tasks.notes', $task) }}">
                            @csrf
                            <div class="d-flex gap-2 mb-2">
                                <input type="text" name="body" class="form-control form-control-sm"
                                       placeholder="What did you do? (note)" required />
                                <input type="number" name="hours" class="form-control form-control-sm flex-shrink-0"
                                       style="width:90px" placeholder="hrs" min="0.25" max="24" step="0.25" />
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-3)">Hours optional — logged to timesheet</span>
                                <button type="submit" class="btn btn-outline-secondary btn-sm px-3 ms-auto">Post</button>
                            </div>
                        </form>
                    </div>
                </div>
                </div>{{-- /.collapse --}}
            </div>
        </div>

        {{-- RIGHT: Details panel --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                    <p class="mb-0 fw-medium small">Details</p>
                    <button class="btn btn-link btn-sm text-muted p-0" data-bs-toggle="modal" data-bs-target="#editTaskModal">Edit</button>
                </div>
                <div class="card-body p-3">
                    <dl class="row small mb-0">
                        <dt class="col-5 text-muted fw-normal">Project</dt>
                        <dd class="col-7">
                            @if($task->project)
                                <a href="{{ route('projects.show', $task->project) }}" class="text-decoration-none">{{ $task->project->name }}</a>
                            @else —
                            @endif
                        </dd>
                        <dt class="col-5 text-muted fw-normal">Status</dt>
                        <dd class="col-7">{{ $current['label'] }}</dd>
                        <dt class="col-5 text-muted fw-normal">Priority</dt>
                        <dd class="col-7">{{ $task->priority ? strtoupper($task->priority) : '—' }}</dd>
                        <dt class="col-5 text-muted fw-normal">Due</dt>
                        <dd class="col-7">{{ $task->needed_by ? $task->needed_by->format('d M Y') : '—' }}</dd>
                        @if($task->source)
                        <dt class="col-5 text-muted fw-normal">Source</dt>
                        <dd class="col-7">{{ $task->source }}</dd>
                        @endif
                        <dt class="col-5 text-muted fw-normal">Created</dt>
                        <dd class="col-7 mb-0">{{ $task->created_at->diffForHumans() }}</dd>
                    </dl>
                </div>
            </div>

            {{-- Time logged --}}
            @php $ts = $task->timesheet; $totalH = $task->totalHours(); @endphp
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <p class="mb-0 fw-medium small">Time Logged</p>
                    @if($ts)
                        <a href="{{ route('timesheets.show', $ts) }}" style="font-family:var(--font-mono);font-size:10px;color:var(--accent);text-decoration:none">View timesheet →</a>
                    @endif
                </div>
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div>
                        <div style="font-family:var(--font-display);font-size:28px;font-weight:700;color:{{ $totalH > 0 ? 'var(--accent)' : 'var(--text-3)' }};line-height:1">
                            {{ number_format($totalH, 2) }}
                        </div>
                        <div style="font-family:var(--font-mono);font-size:9px;color:var(--text-3);text-transform:uppercase;letter-spacing:.08em">hours total</div>
                    </div>
                    @if($totalH == 0)
                    <p class="small mb-0" style="color:var(--text-3)">Log time by adding a note with an hours value below.</p>
                    @endif
                </div>
            </div>

            @if($task->blocker || $task->commitments->isNotEmpty())
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <p class="mb-0 fw-medium small">Dependencies</p>
                </div>
                <div class="card-body p-3">
                    @if($task->blocker)
                    <p class="text-muted mb-2" style="font-size:11px;text-transform:uppercase;letter-spacing:.08em;font-weight:600">Waiting on</p>
                    <div class="card card-body p-2 small border-warning mb-3">
                        <p class="mb-0 fw-medium">{{ $task->blocker->person->name ?? 'Unknown' }}</p>
                        <small class="text-muted">{{ $task->blocker->description }}</small>
                        @if($task->blocker->isOverdue())
                            <small class="text-danger">Overdue</small>
                        @endif
                    </div>
                    @endif

                    @foreach($task->commitments as $commitment)
                    <p class="text-muted mb-2" style="font-size:11px;text-transform:uppercase;letter-spacing:.08em;font-weight:600">I Owe</p>
                    <div class="card card-body p-2 small border-info">
                        <p class="mb-0 fw-medium">{{ $commitment->person->name ?? 'Unknown' }}</p>
                        <small class="text-muted">{{ $commitment->description }}</small>
                        @if($commitment->needed_by)
                            <small class="text-muted">by {{ $commitment->needed_by->format('d M') }}</small>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

    </div>
</div>

{{-- Edit task modal --}}
<div class="modal fade" id="editTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('tasks.update', $task) }}">
                @csrf @method('PATCH')
                <div class="modal-header">
                    <h6 class="modal-title fw-semibold">Edit Task</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body d-flex flex-column gap-3">

                    <div>
                        <label class="form-label small fw-medium">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $task->title) }}" required />
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="form-label small fw-medium">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $task->description) }}</textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-medium">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                @foreach(['todo' => 'To Do', 'working_on' => 'In Progress', 'waiting_on' => 'Waiting On', 'done' => 'Done', 'archived' => 'Archived'] as $val => $lbl)
                                    <option value="{{ $val }}" {{ old('status', $task->status) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-medium">Priority</label>
                            <select name="priority" class="form-select form-select-sm">
                                <option value="">— none —</option>
                                @foreach(['low' => 'Low', 'med' => 'Medium', 'high' => 'High'] as $val => $lbl)
                                    <option value="{{ $val }}" {{ old('priority', $task->priority) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-medium">Due date</label>
                            <input type="date" name="needed_by" class="form-control form-control-sm"
                                   value="{{ old('needed_by', $task->needed_by?->format('Y-m-d')) }}" />
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-medium">Source</label>
                            <input type="text" name="source" class="form-control form-control-sm"
                                   value="{{ old('source', $task->source) }}" placeholder="e.g. Slack, Email" />
                        </div>
                    </div>

                </div>
                <div class="modal-footer justify-content-between">
                    <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                          onsubmit="return confirm('Delete this task and all its activity? This cannot be undone.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">Delete task</button>
                    </form>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', () => {
        new bootstrap.Modal(document.getElementById('editTaskModal')).show();
    });
</script>
@endif

<script>
document.getElementById('activityBody')?.addEventListener('hide.bs.collapse', () => {
    document.getElementById('activity-chevron').style.transform = 'rotate(-90deg)';
});
document.getElementById('activityBody')?.addEventListener('show.bs.collapse', () => {
    document.getElementById('activity-chevron').style.transform = 'rotate(0deg)';
});
</script>
</x-layouts.app>
