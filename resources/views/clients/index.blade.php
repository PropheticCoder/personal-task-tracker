<x-layouts.app>
<div class="container-lg py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h5 fw-semibold mb-0">Clients</h1>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newClientModal">
            <i class="bi bi-plus me-1"></i> New Client
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($clients->isEmpty())
        <p class="text-muted small">No clients yet. Add one to get started.</p>
    @else
    <div class="row g-3">
        @foreach($clients as $client)
        <div class="col-md-6 col-xl-4">
            <a href="{{ route('clients.show', $client) }}" class="card border-0 shadow-sm text-decoration-none text-dark h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="bg-primary-subtle rounded-2 px-3 py-2 fw-bold text-primary">
                            {{ strtoupper(substr($client->name, 0, 2)) }}
                        </div>
                        <span class="badge bg-light text-muted border">
                            {{ $client->projects->count() }} project{{ $client->projects->count() !== 1 ? 's' : '' }}
                        </span>
                    </div>
                    <h2 class="h6 fw-semibold mb-2">{{ $client->name }}</h2>
                    <div class="d-flex flex-column gap-1">
                        @foreach($client->people->take(3) as $person)
                        <div class="small text-muted">
                            <i class="bi bi-person me-1"></i>{{ $person->name }}
                            @if($person->role)
                                <span class="opacity-75">· {{ $person->role }}</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
    @endif
</div>

<div class="modal fade" id="newClientModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form method="POST" action="{{ route('clients.store') }}">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title">New Client</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-0">
                        <label class="form-label small fw-medium">Company name</label>
                        <input type="text" name="name" class="form-control form-control-sm @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" autofocus required />
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

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', () => {
        new bootstrap.Modal(document.getElementById('newClientModal')).show();
    });
</script>
@endif
</x-layouts.app>
