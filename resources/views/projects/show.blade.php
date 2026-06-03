<x-layouts.app>
@php
    $tasksByStatus = $project->tasks->groupBy('status');
    $working = $tasksByStatus->get('working_on', collect());
    $waiting = $tasksByStatus->get('waiting_on', collect());
    $todo    = $tasksByStatus->get('todo', collect());
    $done    = $tasksByStatus->get('done', collect());

    $statusColors = [
        'active'    => 'success',
        'paused'    => 'warning',
        'completed' => 'secondary',
        'archived'  => 'secondary',
    ];
    $prioColors = [
        'high' => 'bg-danger-subtle text-danger border border-danger-subtle',
        'med'  => 'bg-warning-subtle text-warning border border-warning-subtle',
        'low'  => 'bg-light text-muted border',
    ];
@endphp

<div class="container-fluid py-0">
<div class="d-flex" style="min-height:calc(100vh - 56px)">

    {{-- LEFT: Project info panel --}}
    <aside class="flex-shrink-0 d-none d-lg-flex flex-column" style="width:240px;background:var(--bg-raised);border-right:1px solid var(--border-faint)">
        <div class="p-3 border-bottom">
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="rounded-circle flex-shrink-0" style="width:12px;height:12px;background:{{ $project->color ?? '#6c757d' }}"></span>
                <h1 class="h6 fw-semibold mb-0">{{ $project->name }}</h1>
            </div>
            @if($project->client)
                <span class="badge bg-light text-muted border fw-normal small">{{ $project->client->name }}</span>
            @endif
            <span class="badge bg-{{ $statusColors[$project->status] ?? 'secondary' }}-subtle text-{{ $statusColors[$project->status] ?? 'secondary' }} border border-{{ $statusColors[$project->status] ?? 'secondary' }}-subtle ms-1 small">
                {{ ucfirst($project->status) }}
            </span>
        </div>

        @if($project->description)
        <div class="p-3 border-bottom">
            <p class="small text-muted mb-0">{{ $project->description }}</p>
        </div>
        @endif

        @if($project->client && $project->client->people->isNotEmpty())
        <div class="p-3 border-bottom">
            <p class="text-uppercase text-muted fw-semibold mb-2" style="font-size:10px;letter-spacing:.1em">Client Contacts</p>
            @foreach($project->client->people as $person)
            <div class="d-flex align-items-center gap-2 mb-2">
                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width:28px;height:28px;font-size:12px">
                    {{ strtoupper(substr($person->name, 0, 1)) }}
                </div>
                <div>
                    <p class="mb-0 small fw-medium">{{ $person->name }}</p>
                    @if($person->role)
                        <p class="mb-0 text-muted" style="font-size:11px">{{ $person->role }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <div class="p-3 mt-auto">
            <button class="btn btn-outline-secondary btn-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#editProjectModal">
                <i class="bi bi-pencil me-1"></i> Edit Project
            </button>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <main class="flex-grow-1 overflow-auto">

        {{-- Mobile project header (desktop sidebar replaces this) --}}
        <div class="d-lg-none border-bottom px-3 py-2" style="background:var(--bg-raised);display:flex;align-items:center;gap:10px">
            <span class="rounded-circle flex-shrink-0" style="width:10px;height:10px;background:{{ $project->color ?? '#6c757d' }}"></span>
            <span class="fw-semibold small flex-grow-1" style="color:var(--text-1)">{{ $project->name }}</span>
            @if($project->client)
                <span class="badge bg-light border fw-normal" style="font-size:10px">{{ $project->client->name }}</span>
            @endif
            <span class="badge bg-{{ $statusColors[$project->status] ?? 'secondary' }}-subtle text-{{ $statusColors[$project->status] ?? 'secondary' }} border border-{{ $statusColors[$project->status] ?? 'secondary' }}-subtle" style="font-size:10px">
                {{ ucfirst($project->status) }}
            </span>
            <button class="btn btn-link btn-sm p-0 text-muted ms-1" data-bs-toggle="modal" data-bs-target="#editProjectModal">
                <i class="bi bi-pencil" style="font-size:13px"></i>
            </button>
        </div>

        {{-- Mobile: description + contacts (collapsible) --}}
        @if($project->description || ($project->client && $project->client->people->isNotEmpty()))
        <div class="d-lg-none" style="background:var(--bg-raised)">
            <button class="btn btn-link btn-sm text-muted w-100 text-start px-3 border-bottom"
                    style="font-family:var(--font-mono);font-size:10px;text-transform:uppercase;letter-spacing:.08em;padding-top:7px;padding-bottom:7px;text-decoration:none"
                    data-bs-toggle="collapse" data-bs-target="#proj-mob-details">
                <i class="bi bi-info-circle me-1"></i>Details &amp; Contacts
                <i class="bi bi-chevron-down ms-1" style="font-size:9px"></i>
            </button>
            <div class="collapse" id="proj-mob-details" style="border-bottom:1px solid var(--border-faint)">
                <div class="px-3 py-3">
                    @if($project->description)
                    <p class="small mb-3" style="color:var(--text-2)">{{ $project->description }}</p>
                    @endif
                    @if($project->client && $project->client->people->isNotEmpty())
                    <p style="font-family:var(--font-mono);font-size:9px;font-weight:500;text-transform:uppercase;letter-spacing:.1em;color:var(--text-3);margin-bottom:10px">Client Contacts</p>
                    @foreach($project->client->people as $person)
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width:28px;height:28px;font-size:12px">
                            {{ strtoupper(substr($person->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="mb-0 small fw-medium">{{ $person->name }}</p>
                            @if($person->role)<p class="mb-0" style="font-size:11px;color:var(--text-3)">{{ $person->role }}</p>@endif
                            @if($person->email)<a href="mailto:{{ $person->email }}" style="font-size:11px;color:var(--accent);text-decoration:none;display:block">{{ $person->email }}</a>@endif
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Project tabs --}}
        <div class="border-bottom sticky-top proj-tabs-bar" style="top:0;z-index:100;background:var(--bg-raised)">
            <div class="container-lg">
                <div class="d-flex flex-wrap align-items-center justify-content-between py-2 gap-2">
                    <ul class="nav nav-tabs border-0 gap-1 flex-shrink-0">
                        <li class="nav-item">
                            <a class="nav-link active fw-medium" href="#">
                                <i class="bi bi-list-check me-1"></i> Tasks
                                <span class="badge bg-primary ms-1">{{ $project->tasks->whereNotIn('status',['done','archived'])->count() }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-muted" href="{{ route('meetings.create', $project) }}">
                                <i class="bi bi-camera-video me-1"></i> + Meeting
                            </a>
                        </li>
                        @if($project->meetings->isNotEmpty())
                        <li class="nav-item">
                            <a class="nav-link text-muted" href="#meetings-section">
                                <i class="bi bi-clock-history me-1"></i> Meetings
                                <span class="badge bg-secondary ms-1" style="font-size:9px">{{ $project->meetings->count() }}</span>
                            </a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link text-muted" href="#"><i class="bi bi-clock-history me-1"></i> Timeline</a>
                        </li>
                    </ul>

                    <form action="{{ route('tasks.store') }}" method="POST" class="d-flex gap-2 wt-add-task-form">
                        @csrf
                        <input type="hidden" name="project_id" value="{{ $project->id }}" />
                        <input type="hidden" name="status" value="todo" />
                        <input type="text" name="title" class="form-control form-control-sm" placeholder="Add task…" required />
                        <button class="btn btn-primary btn-sm px-3" style="white-space:nowrap">Add</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="container-lg py-4">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show py-2 small mb-3" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- IN PROGRESS --}}
            @if($working->isNotEmpty())
            <section class="mb-4">
                <p class="text-uppercase fw-semibold mb-3" style="font-size:11px;letter-spacing:.1em;color:var(--info)">
                    <i class="bi bi-arrow-right-circle-fill me-1"></i>In Progress
                </p>
                <div class="d-flex flex-column gap-2">
                    @foreach($working as $task)
                    <div class="card border-0 shadow-sm">
                        <div class="card-body py-2 px-3">
                            <div class="d-flex align-items-center gap-3">
                                <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none fw-medium small flex-grow-1" style="color:var(--text-1)">
                                    {{ $task->title }}
                                </a>
                                @if($task->needed_by)
                                    <small class="text-muted flex-shrink-0">{{ $task->needed_by->format('d M') }}</small>
                                @endif
                                @if($task->priority)
                                    <span class="badge fw-normal flex-shrink-0 {{ $prioColors[$task->priority] ?? '' }}">{{ strtoupper($task->priority) }}</span>
                                @endif
                                <div class="dropdown flex-shrink-0">
                                    <button class="btn btn-link btn-sm p-0 text-muted" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <form method="POST" action="{{ route('tasks.update', $task) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="done">
                                                <button class="dropdown-item small" type="submit"><i class="bi bi-check-circle text-muted me-2"></i>Mark Done</button>
                                            </form>
                                        </li>
                                        <li>
                                            <form method="POST" action="{{ route('tasks.update', $task) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="todo">
                                                <button class="dropdown-item small" type="submit"><i class="bi bi-arrow-counterclockwise text-muted me-2"></i>Move to Todo</button>
                                            </form>
                                        </li>
                                        <li><a class="dropdown-item small" href="{{ route('tasks.show', $task) }}"><i class="bi bi-arrow-right text-muted me-2"></i>Open</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>
            @endif

            {{-- WAITING ON --}}
            @if($waiting->isNotEmpty())
            <section class="mb-4">
                <p class="text-uppercase fw-semibold mb-3" style="font-size:11px;letter-spacing:.1em;color:var(--danger)">
                    <i class="bi bi-hourglass-split me-1"></i>Waiting On
                </p>
                <div class="d-flex flex-column gap-2">
                    @foreach($waiting as $task)
                    <div class="card border-0 shadow-sm">
                        <div class="card-body py-2 px-3">
                            <div class="d-flex align-items-center gap-3">
                                <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none fw-medium small flex-grow-1" style="color:var(--text-1)">{{ $task->title }}</a>
                                @if($task->priority)
                                    <span class="badge fw-normal flex-shrink-0 {{ $prioColors[$task->priority] ?? '' }}">{{ strtoupper($task->priority) }}</span>
                                @endif
                                <div class="dropdown flex-shrink-0">
                                    <button class="btn btn-link btn-sm p-0 text-muted" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <form method="POST" action="{{ route('tasks.update', $task) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="working_on">
                                                <button class="dropdown-item small" type="submit"><i class="bi bi-play-circle text-info me-2"></i>Resume (→ In Progress)</button>
                                            </form>
                                        </li>
                                        <li>
                                            <form method="POST" action="{{ route('tasks.update', $task) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="done">
                                                <button class="dropdown-item small" type="submit"><i class="bi bi-check-circle text-muted me-2"></i>Mark Done</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>
            @endif

            {{-- TODO --}}
            @if($todo->isNotEmpty())
            <section class="mb-4">
                <p class="text-uppercase fw-semibold mb-3" style="font-size:11px;letter-spacing:.1em;color:var(--success)">
                    <i class="bi bi-arrow-right me-1"></i>To Do
                </p>
                <div class="d-flex flex-column gap-2">
                    @foreach($todo as $task)
                    <div class="card border-0 shadow-sm">
                        <div class="card-body py-2 px-3">
                            <div class="d-flex align-items-center gap-3">
                                <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none small fw-medium flex-grow-1" style="color:var(--text-1)">{{ $task->title }}</a>
                                @if($task->needed_by)
                                    <small class="text-muted flex-shrink-0">{{ $task->needed_by->format('d M') }}</small>
                                @endif
                                @if($task->priority)
                                    <span class="badge fw-normal flex-shrink-0 {{ $prioColors[$task->priority] ?? '' }}">{{ strtoupper($task->priority) }}</span>
                                @endif
                                <div class="dropdown flex-shrink-0">
                                    <button class="btn btn-link btn-sm p-0 text-muted" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <form method="POST" action="{{ route('tasks.update', $task) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="working_on">
                                                <button class="dropdown-item small" type="submit"><i class="bi bi-play-circle text-primary me-2"></i>Start (→ In Progress)</button>
                                            </form>
                                        </li>
                                        <li>
                                            <form method="POST" action="{{ route('tasks.update', $task) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="done">
                                                <button class="dropdown-item small" type="submit"><i class="bi bi-check-circle text-muted me-2"></i>Mark Done</button>
                                            </form>
                                        </li>
                                        <li><a class="dropdown-item small" href="{{ route('tasks.show', $task) }}"><i class="bi bi-arrow-right text-muted me-2"></i>Open</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>
            @endif

            {{-- DONE (collapsible) --}}
            @if($done->isNotEmpty())
            <section>
                <button class="text-uppercase fw-semibold btn btn-link p-0 text-decoration-none text-muted mb-3"
                        style="font-size:11px;letter-spacing:.1em"
                        data-bs-toggle="collapse" data-bs-target="#doneTasks">
                    <i class="bi bi-check-circle me-1"></i>Done ({{ $done->count() }}) <i class="bi bi-chevron-down"></i>
                </button>
                <div class="collapse" id="doneTasks">
                    <div class="d-flex flex-column gap-2">
                        @foreach($done as $task)
                        <div class="card border-0" style="background:var(--bg-elevated);opacity:0.6">
                            <div class="card-body py-2 px-3">
                                <span class="small text-decoration-line-through" style="color:var(--text-3)">{{ $task->title }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </section>
            @endif

            @if($project->tasks->isEmpty())
                <p class="text-muted small">No tasks yet — add one above.</p>
            @endif

            {{-- MEETINGS --}}
            @if($project->meetings->isNotEmpty())
            <section id="meetings-section" class="mt-2">
                <p class="text-uppercase fw-semibold mb-3" style="font-size:11px;letter-spacing:.1em;color:var(--text-3)">
                    <i class="bi bi-camera-video me-1"></i>Meetings
                </p>
                <div class="d-flex flex-column gap-2">
                    @foreach($project->meetings as $meeting)
                    <div class="card border-0 shadow-sm">
                        <div class="card-body py-2 px-3">
                            <div class="d-flex align-items-center gap-3">
                                <a href="{{ route('meetings.show', $meeting) }}"
                                   class="text-decoration-none fw-medium small flex-grow-1" style="color:var(--text-1)">
                                    {{ $meeting->title }}
                                </a>
                                <small class="text-muted flex-shrink-0">{{ $meeting->held_at->format('d M Y') }}</small>
                                @php $actionCount = $meeting->tasks_count ?? 0; @endphp
                                @if($actionCount)
                                    <span class="badge bg-light border text-muted flex-shrink-0" style="font-size:9px">
                                        {{ $actionCount }} action {{ Str::plural('item', $actionCount) }}
                                    </span>
                                @endif
                                <a href="{{ route('meetings.show', $meeting) }}"
                                   class="text-muted flex-shrink-0" style="font-size:12px">
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-2">
                    <a href="{{ route('meetings.create', $project) }}"
                       style="font-family:var(--font-mono);font-size:11px;color:var(--accent);text-decoration:none">
                        + Log a meeting
                    </a>
                </div>
            </section>
            @endif

        </div>
    </main>

</div>
</div>

{{-- Edit project modal --}}
<div class="modal fade" id="editProjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('projects.update', $project) }}">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h6 class="modal-title fw-semibold">Edit Project</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body d-flex flex-column gap-3">

                    <div>
                        <label class="form-label small fw-medium">Project name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $project->name) }}" required />
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="form-label small fw-medium">Client</label>
                        <select name="client_id" class="form-select">
                            <option value="">— internal project —</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id', $project->client_id) == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label small fw-medium">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $project->description) }}</textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-medium">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                @foreach(['active','paused','completed','archived'] as $s)
                                    <option value="{{ $s }}" {{ old('status', $project->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-medium">Card colour</label>
                            <div class="d-flex gap-2 mt-1 flex-wrap" id="edit-color-picker">
                                @foreach(['#0d6efd','#198754','#dc3545','#fd7e14','#6f42c1','#0dcaf0','#6c757d'] as $c)
                                @php $selected = old('color', $project->color) === $c; @endphp
                                <label class="d-flex align-items-center justify-content-center rounded-circle"
                                       style="width:26px;height:26px;background:{{ $c }};cursor:pointer;{{ $selected ? 'outline:3px solid #fff;box-shadow:0 0 0 4px '.$c : '' }}">
                                    <input type="radio" name="color" value="{{ $c }}" class="visually-hidden"
                                           {{ $selected ? 'checked' : '' }}
                                           onchange="updateEditColor(this)" />
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer justify-content-between">
                    <form method="POST" action="{{ route('projects.destroy', $project) }}"
                          onsubmit="return confirm('Delete this project and ALL its tasks? This cannot be undone.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">Delete project</button>
                    </form>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateEditColor(radio) {
    document.querySelectorAll('#edit-color-picker input[name="color"]').forEach(r => {
        const label = r.closest('label');
        label.style.outline   = r.checked ? '3px solid #fff' : 'none';
        label.style.boxShadow = r.checked ? '0 0 0 4px ' + r.value : 'none';
    });
}
</script>

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', () => {
        new bootstrap.Modal(document.getElementById('editProjectModal')).show();
    });
</script>
@endif
</x-layouts.app>
