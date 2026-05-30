<x-layouts.app>
<div class="container" style="max-width:600px">
    <div class="py-4">

        <a href="/" class="text-decoration-none text-muted small mb-3 d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>

        <h1 class="h5 fw-semibold mb-4">New Project</h1>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('projects.store') }}" method="POST" id="project-form">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label small fw-medium">Project name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" placeholder="e.g. Platform Redesign" required autofocus />
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-medium">Client</label>
                        <div class="input-group">
                            <select name="client_id" id="client-select" class="form-select">
                                <option value="">— no client (internal project) —</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#newClientModal">
                                <i class="bi bi-plus"></i> New client
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-medium">Description <span class="text-muted fw-normal">(optional)</span></label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Brief context about this project…">{{ old('description') }}</textarea>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <label class="form-label small fw-medium">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="active" {{ old('status','active') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="paused" {{ old('status') === 'paused' ? 'selected' : '' }}>Paused</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-medium">Card colour</label>
                            <div class="d-flex gap-2 mt-1 flex-wrap">
                                @foreach(['#0d6efd','#198754','#dc3545','#fd7e14','#6f42c1','#0dcaf0','#6c757d'] as $c)
                                <label class="d-flex align-items-center justify-content-center rounded-circle"
                                       style="width:26px;height:26px;background:{{ $c }};cursor:pointer;outline:{{ old('color','#0d6efd') === $c ? '3px solid #fff' : 'none' }};box-shadow:{{ old('color','#0d6efd') === $c ? '0 0 0 4px '.$c : 'none' }}">
                                    <input type="radio" name="color" value="{{ $c }}" class="visually-hidden"
                                           {{ old('color','#0d6efd') === $c ? 'checked' : '' }}
                                           onchange="updateColorSelection(this)" />
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">Create Project</button>
                        <a href="/" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

{{-- New client inline modal --}}
<div class="modal fade" id="newClientModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">New Client</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-0">
                    <label class="form-label small fw-medium">Company name</label>
                    <input type="text" id="inline-client-name" class="form-control form-control-sm" placeholder="Acme Corp" />
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="saveInlineClient()">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
function updateColorSelection(radio) {
    document.querySelectorAll('input[name="color"]').forEach(r => {
        const label = r.closest('label');
        if (r.checked) {
            label.style.outline = '3px solid #fff';
            label.style.boxShadow = '0 0 0 4px ' + r.value;
        } else {
            label.style.outline = 'none';
            label.style.boxShadow = 'none';
        }
    });
}

async function saveInlineClient() {
    const name = document.getElementById('inline-client-name').value.trim();
    if (!name) return;

    const res = await fetch('{{ route('clients.store') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ name }),
    });

    if (res.ok) {
        const data = await res.json();
        const select = document.getElementById('client-select');
        const option = new Option(name, data.id, true, true);
        select.add(option);
        bootstrap.Modal.getInstance(document.getElementById('newClientModal')).hide();
        document.getElementById('inline-client-name').value = '';
    }
}
</script>
</x-layouts.app>
