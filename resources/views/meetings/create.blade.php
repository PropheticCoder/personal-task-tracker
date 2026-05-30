<x-layouts.app>
<div class="container" style="max-width:640px">
    <div class="py-4">

        <a href="{{ route('projects.show', $project) }}" class="text-decoration-none text-muted small mb-3 d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> {{ $project->name }}
        </a>

        <h1 class="h5 fw-semibold mb-4">New Meeting</h1>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('meetings.store', $project) }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label small fw-medium">Meeting title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title') }}"
                               placeholder="e.g. Sprint Planning · Jun 3, Design Review, Kickoff" required autofocus />
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-medium">Date & time <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="held_at" class="form-control @error('held_at') is-invalid @enderror"
                                   value="{{ old('held_at', now()->format('Y-m-d\TH:i')) }}" required />
                            @error('held_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-medium">Project</label>
                            <input type="text" class="form-control form-control-sm bg-light" value="{{ $project->name }}{{ $project->client ? ' — '.$project->client->name : '' }}" disabled />
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-medium">
                            Notes
                            <span class="text-muted fw-normal">(optional — fill in during or after)</span>
                        </label>
                        <textarea name="notes" class="form-control" rows="6"
                                  placeholder="Paste or type meeting notes here. Add action items after saving.">{{ old('notes') }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">Create Meeting</button>
                        <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</x-layouts.app>
