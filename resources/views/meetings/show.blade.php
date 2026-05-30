<x-layouts.app>
@php
    $statusColors = [
        'todo'       => 'warning',
        'working_on' => 'primary',
        'waiting_on' => 'danger',
        'done'       => 'success',
        'archived'   => 'secondary',
    ];
    $statusLabels = [
        'todo'       => 'To Do',
        'working_on' => 'In Progress',
        'waiting_on' => 'Waiting On',
        'done'       => 'Done',
        'archived'   => 'Archived',
    ];
    $prioColors = [
        'high' => 'bg-danger-subtle text-danger border border-danger-subtle',
        'med'  => 'bg-warning-subtle text-warning border border-warning-subtle',
        'low'  => 'bg-light text-muted border',
    ];
@endphp

<div class="container-lg py-4">

    <a href="{{ route('projects.show', $meeting->project) }}" class="text-decoration-none text-muted small mb-3 d-inline-flex align-items-center gap-1">
        <i class="bi bi-arrow-left"></i> {{ $meeting->project->name }}
    </a>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 small mb-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex align-items-start justify-content-between mb-3">
        <div>
            <h1 class="h5 fw-semibold mb-1">{{ $meeting->title }}</h1>
            <div class="d-flex align-items-center gap-2 text-muted small">
                <i class="bi bi-calendar3"></i> {{ $meeting->held_at->format('D j M Y, H:i') }}
                <span>·</span>
                <a href="{{ route('projects.show', $meeting->project) }}" class="text-muted text-decoration-none">
                    <i class="bi bi-folder me-1"></i>{{ $meeting->project->name }}
                </a>
                @if($meeting->project->client)
                    <span class="badge bg-light text-muted border fw-normal">{{ $meeting->project->client->name }}</span>
                @endif
            </div>
        </div>
        <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editMeetingModal">
            <i class="bi bi-pencil me-1"></i> Edit
        </button>
    </div>

    <div class="row g-4">

        {{-- LEFT: Notes --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                    <p class="mb-0 fw-medium small"><i class="bi bi-file-text me-1 text-muted"></i> Notes</p>
                    <button class="btn btn-link btn-sm p-0 text-muted" data-bs-toggle="modal" data-bs-target="#editMeetingModal">
                        <i class="bi bi-pencil"></i>
                    </button>
                </div>
                <div class="card-body p-4">
                    @if($meeting->notes)
                        <div class="small text-dark" style="white-space:pre-line;line-height:1.7">{{ $meeting->notes }}</div>
                    @else
                        <p class="text-muted small mb-0">No notes yet. <button class="btn btn-link btn-sm p-0" data-bs-toggle="modal" data-bs-target="#editMeetingModal">Add notes →</button></p>
                    @endif
                </div>
            </div>
        </div>

        {{-- RIGHT: Action items --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                    <p class="mb-0 fw-medium small"><i class="bi bi-list-check me-1 text-muted"></i> Action Items</p>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addActionModal">
                        <i class="bi bi-plus me-1"></i> Add action
                    </button>
                </div>
                <div class="card-body p-3 d-flex flex-column gap-2">
                    @forelse($meeting->tasks as $task)
                    <div class="card border-0 bg-light">
                        <div class="card-body py-2 px-3">
                            <div class="d-flex align-items-start gap-2">
                                <span class="badge bg-{{ $statusColors[$task->status] ?? 'secondary' }}-subtle text-{{ $statusColors[$task->status] ?? 'secondary' }} border border-{{ $statusColors[$task->status] ?? 'secondary' }}-subtle flex-shrink-0 mt-0">
                                    {{ $statusLabels[$task->status] ?? $task->status }}
                                </span>
                                <div class="flex-grow-1">
                                    <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none text-dark small fw-medium">
                                        {{ $task->title }}
                                    </a>
                                </div>
                                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                    @if($task->needed_by)
                                        <small class="text-muted">{{ $task->needed_by->format('d M') }}</small>
                                    @endif
                                    @if($task->priority)
                                        <span class="badge fw-normal {{ $prioColors[$task->priority] }}">{{ strtoupper($task->priority) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                        <p class="text-muted small mb-0">No action items yet.</p>
                    @endforelse
                </div>
            </div>

            @if($meeting->tasks->isNotEmpty())
            <div class="d-flex gap-3 mt-2 small text-muted">
                @php
                    $counts = $meeting->tasks->groupBy('status')->map->count();
                @endphp
                @if($counts->get('working_on'))
                    <span><i class="bi bi-arrow-right-circle text-primary me-1"></i>{{ $counts->get('working_on') }} in progress</span>
                @endif
                @if($counts->get('waiting_on'))
                    <span><i class="bi bi-hourglass-split text-danger me-1"></i>{{ $counts->get('waiting_on') }} waiting</span>
                @endif
                @if($counts->get('todo'))
                    <span><i class="bi bi-list-check text-warning me-1"></i>{{ $counts->get('todo') }} to do</span>
                @endif
                @if($counts->get('done'))
                    <span><i class="bi bi-check-circle text-success me-1"></i>{{ $counts->get('done') }} done</span>
                @endif
            </div>
            @endif
        </div>

    </div>
</div>

{{-- Edit meeting modal --}}
<div class="modal fade" id="editMeetingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('meetings.update', $meeting) }}">
                @csrf @method('PATCH')
                <div class="modal-header">
                    <h6 class="modal-title fw-semibold">Edit Meeting</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body d-flex flex-column gap-3">
                    <div>
                        <label class="form-label small fw-medium">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control"
                               value="{{ old('title', $meeting->title) }}" required />
                    </div>
                    <div>
                        <label class="form-label small fw-medium">Date & time <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="held_at" class="form-control"
                               value="{{ old('held_at', $meeting->held_at->format('Y-m-d\TH:i')) }}" required />
                    </div>
                    <div>
                        <label class="form-label small fw-medium">Notes</label>
                        <textarea name="notes" class="form-control" rows="8">{{ old('notes', $meeting->notes) }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Add action item modal --}}
<div class="modal fade" id="addActionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('meetings.tasks.store', $meeting) }}">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-semibold">Add Action Item</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body d-flex flex-column gap-3">
                    <div>
                        <label class="form-label small fw-medium">What needs to happen? <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control"
                               placeholder="e.g. Write release notes" required />
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-medium">Priority</label>
                            <select name="priority" class="form-select form-select-sm">
                                <option value="">— none —</option>
                                <option value="high">High</option>
                                <option value="med">Medium</option>
                                <option value="low">Low</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-medium">Due by</label>
                            <input type="date" name="needed_by" class="form-control form-control-sm" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Add action</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', () => {
        new bootstrap.Modal(document.getElementById('editMeetingModal')).show();
    });
</script>
@endif
</x-layouts.app>
