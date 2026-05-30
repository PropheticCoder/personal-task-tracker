<x-layouts.app>
@production
<script>window.location = '{{ route('tools.index') }}';</script>
@endproduction
<div class="container-lg py-4">

    <a href="/tools" class="text-decoration-none text-muted small mb-3 d-inline-flex align-items-center gap-1">
        <i class="bi bi-arrow-left"></i> Back to Tools
    </a>

    <div class="row g-4 mt-0">

        {{-- Run form --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex align-items-center gap-2">
                    <div class="bg-primary-subtle rounded-2 p-1">
                        <i class="bi bi-qr-code text-primary"></i>
                    </div>
                    <p class="mb-0 fw-semibold">Text → QR Code</p>
                </div>
                <div class="card-body p-4">
                    <form>
                        <div class="mb-4">
                            <label class="form-label small fw-medium">Text or URL to encode</label>
                            <textarea class="form-control" rows="4" placeholder="https://example.com or any text…"></textarea>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <label class="form-label small fw-medium">Output format</label>
                                <select class="form-select form-select-sm">
                                    <option>PNG</option>
                                    <option>SVG</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label small fw-medium">Size (px)</label>
                                <input type="number" class="form-control form-control-sm" value="300" />
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-medium">Attach to task <span class="text-muted fw-normal">(optional)</span></label>
                            <select class="form-select form-select-sm">
                                <option value="">— no task —</option>
                                <option>Fix login redirect bug</option>
                                <option>Write release notes</option>
                                <option>Review Q2 report</option>
                            </select>
                            <div class="form-text">Output will appear in the task's timeline if selected.</div>
                        </div>

                        <button class="btn btn-primary px-4">
                            <i class="bi bi-play-fill me-1"></i> Run
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Output / past runs --}}
        <div class="col-lg-5">
            {{-- Output preview (shown after run) --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-bottom">
                    <p class="mb-0 fw-medium small">Output</p>
                </div>
                <div class="card-body p-4 text-center text-muted small">
                    <i class="bi bi-qr-code display-4 text-secondary opacity-25 d-block mb-2"></i>
                    Run the tool to see output here.
                </div>
            </div>

            {{-- Previous runs --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <p class="mb-0 fw-medium small">Previous Runs</p>
                </div>
                <ul class="list-group list-group-flush small">
                    @foreach(['5m ago', '2h ago', 'Yesterday'] as $time)
                    <li class="list-group-item d-flex align-items-center gap-2 px-3 py-2">
                        <i class="bi bi-check-circle-fill text-success flex-shrink-0"></i>
                        <span class="flex-grow-1 text-muted">{{ $time }}</span>
                        <a href="#" class="btn btn-link btn-sm p-0 text-muted me-1"><i class="bi bi-download"></i></a>
                        <a href="#" class="btn btn-link btn-sm p-0 text-muted" title="Clone params"><i class="bi bi-copy"></i></a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

    </div>

</div>
</x-layouts.app>
