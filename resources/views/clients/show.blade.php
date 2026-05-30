<x-layouts.app>
<div class="container-lg py-4">

    <a href="{{ route('clients.index') }}" class="text-decoration-none text-muted small mb-3 d-inline-flex align-items-center gap-1">
        <i class="bi bi-arrow-left"></i> Clients
    </a>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex align-items-start gap-3 mb-4">
        <div class="bg-primary-subtle rounded-3 px-3 py-2 fw-bold text-primary fs-5">
            {{ strtoupper(substr($client->name, 0, 2)) }}
        </div>
        <div>
            <h1 class="h5 fw-semibold mb-0">{{ $client->name }}</h1>
            <span class="text-muted small">{{ $client->projects->count() }} active project{{ $client->projects->count() !== 1 ? 's' : '' }}</span>
        </div>
        <div class="d-flex gap-2 ms-auto">
            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editClientModal">
                <i class="bi bi-pencil me-1"></i> Edit
            </button>
            <form method="POST" action="{{ route('clients.destroy', $client) }}"
                  onsubmit="return confirm('Delete {{ addslashes($client->name) }} and all their contacts? This cannot be undone.')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-trash3 me-1"></i> Delete
                </button>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                    <p class="mb-0 fw-medium small">Contacts</p>
                    <button class="btn btn-link btn-sm p-0 text-primary" data-bs-toggle="modal" data-bs-target="#addContactModal">+ Add</button>
                </div>
                <div class="card-body p-3 d-flex flex-column gap-3">
                    @forelse($client->people as $person)
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width:32px;height:32px;font-size:13px">
                            {{ strtoupper(substr($person->name, 0, 1)) }}
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0 small fw-medium">{{ $person->name }}</p>
                            @if($person->role)
                                <p class="mb-0" style="font-size:11px;color:var(--text-3)">{{ $person->role }}</p>
                            @endif
                            @if($person->email)
                                <a href="mailto:{{ $person->email }}" style="font-size:11px;color:var(--accent);text-decoration:none">{{ $person->email }}</a>
                            @endif
                            @if($person->phone)
                                <p class="mb-0" style="font-size:11px;color:var(--text-3)">{{ $person->phone }}</p>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('people.destroy', $person) }}"
                              onsubmit="return confirm('Remove {{ addslashes($person->name) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-link btn-sm p-0 text-muted" title="Remove contact">
                                <i class="bi bi-trash3" style="font-size:12px"></i>
                            </button>
                        </form>
                    </div>
                    @empty
                        <p class="text-muted small mb-0">No contacts yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <p class="mb-0 fw-medium small">Projects</p>
                </div>
                @forelse($client->projects as $project)
                <a href="{{ route('projects.show', $project) }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                    <p class="mb-0 small fw-medium flex-grow-1">{{ $project->name }}</p>
                    <span @class([
                        'badge',
                        'bg-success-subtle text-success border border-success-subtle' => $project->status === 'active',
                        'bg-warning-subtle text-warning border border-warning-subtle' => $project->status === 'paused',
                        'bg-secondary-subtle text-secondary border' => !in_array($project->status, ['active','paused']),
                    ])>{{ ucfirst($project->status) }}</span>
                </a>
                @empty
                <div class="card-body p-3">
                    <p class="text-muted small mb-0">No projects yet.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

{{-- Edit client modal --}}
<div class="modal fade" id="editClientModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form method="POST" action="{{ route('clients.update', $client) }}">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h6 class="modal-title">Edit Client</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-0">
                        <label class="form-label small fw-medium">Company name</label>
                        <input type="text" name="name" class="form-control form-control-sm @error('name') is-invalid @enderror"
                               value="{{ old('name', $client->name) }}" required />
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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

{{-- Add contact modal --}}
<div class="modal fade" id="addContactModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form method="POST" action="{{ route('people.store') }}">
                @csrf
                <input type="hidden" name="client_id" value="{{ $client->id }}">
                <div class="modal-header">
                    <h6 class="modal-title">Add Contact</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body d-flex flex-column gap-3">
                    <div>
                        <label class="form-label small fw-medium">Name</label>
                        <input type="text" name="name" class="form-control form-control-sm" value="{{ old('name') }}" required />
                    </div>
                    <div>
                        <label class="form-label small fw-medium">Role <span class="text-muted fw-normal">(optional)</span></label>
                        <input type="text" name="role" class="form-control form-control-sm" value="{{ old('role') }}" placeholder="e.g. Product Manager" />
                    </div>
                    <div>
                        <label class="form-label small fw-medium">Email <span class="text-muted fw-normal">(optional)</span></label>
                        <input type="email" name="email" class="form-control form-control-sm" value="{{ old('email') }}" placeholder="name@company.com" />
                    </div>
                    <div>
                        <label class="form-label small fw-medium">Phone <span class="text-muted fw-normal">(optional)</span></label>
                        <input type="text" name="phone" class="form-control form-control-sm" value="{{ old('phone') }}" placeholder="+27 82 000 0000" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($errors->has('name'))
<script>
    document.addEventListener('DOMContentLoaded', () => {
        new bootstrap.Modal(document.getElementById('editClientModal')).show();
    });
</script>
@endif
</x-layouts.app>
