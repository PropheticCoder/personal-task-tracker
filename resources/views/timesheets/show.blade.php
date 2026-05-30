<x-layouts.app>
<div class="container-lg py-4">

    <a href="{{ route('timesheets.index') }}" class="text-decoration-none text-muted small mb-3 d-inline-flex align-items-center gap-1">
        <i class="bi bi-arrow-left"></i> Timesheets
    </a>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 small mb-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex align-items-start justify-content-between mb-4 gap-3 flex-wrap">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h1 class="h5 fw-semibold mb-0" style="font-family:var(--font-display)">{{ $timesheet->title }}</h1>
                <span class="badge" style="font-family:var(--font-mono);font-size:9px;font-weight:500;
                    {{ $timesheet->status === 'submitted'
                        ? 'background:var(--success-bg);color:var(--success);border:1px solid rgba(45,212,170,.3)'
                        : 'background:var(--warning-bg);color:var(--warning);border:1px solid rgba(245,181,63,.3)' }}">
                    {{ ucfirst($timesheet->status) }}
                </span>
            </div>
            <div style="font-family:var(--font-mono);font-size:11px;color:var(--text-3)">
                {{ $timesheet->period_start->format('d M Y') }} — {{ $timesheet->period_end->format('d M Y') }}
                @if($timesheet->client) · <span style="color:var(--text-2)">{{ $timesheet->client->name }}</span> @endif
            </div>
        </div>
        <div class="d-flex gap-2">
            @if($timesheet->status === 'draft')
            <form method="POST" action="{{ route('timesheets.submit', $timesheet) }}">
                @csrf
                <button type="submit" class="wt-btn wt-btn-accent" style="padding:6px 16px">
                    <i class="bi bi-send me-1" style="font-size:11px"></i> Submit
                </button>
            </form>
            @endif
            <button onclick="window.print()" class="wt-btn" style="padding:6px 14px">
                <i class="bi bi-printer me-1" style="font-size:11px"></i> Print
            </button>
        </div>
    </div>

    <div class="row g-4">

        {{-- LEFT: Entries by project --}}
        <div class="col-lg-8">
            @forelse($byProject as $group)
            @php $project = $group['project']; @endphp
            <div class="card border-0 mb-3" style="background:var(--bg-raised);border:1px solid var(--border-faint)!important">

                {{-- Project header --}}
                <div class="card-header d-flex align-items-center gap-2" style="padding:12px 16px">
                    <span style="width:10px;height:10px;border-radius:2px;background:{{ $project->color ?? '#7c7c98' }};flex-shrink:0"></span>
                    <span class="fw-semibold" style="color:var(--text-1)">{{ $project->name }}</span>
                    @if($project->client)
                        <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-3)">· {{ $project->client->name ?? '' }}</span>
                    @endif
                    <span style="font-family:var(--font-display);font-size:15px;font-weight:700;color:var(--accent);margin-left:auto">
                        {{ number_format($group['hours'], 2) }}h
                    </span>
                </div>

                {{-- Entry rows --}}
                <div class="card-body p-0">
                    @foreach($group['entries'] as $entry)
                    <div class="d-flex align-items-start gap-3 px-4 py-3" style="border-bottom:1px solid var(--border-faint)">
                        <div class="flex-grow-1">
                            <div style="font-size:13px;font-weight:500;color:var(--text-1);margin-bottom:3px">
                                {{ $entry->body ?? ucfirst(str_replace('_',' ',$entry->type)) }}
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                @if($entry->task)
                                <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-3)">
                                    <i class="bi bi-check2-square me-1"></i>{{ $entry->task->title }}
                                </span>
                                @endif
                                <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-3)">
                                    {{ $entry->created_at->format('D, d M') }}
                                </span>
                            </div>
                        </div>
                        <div style="font-family:var(--font-display);font-size:15px;font-weight:700;color:var(--text-2);flex-shrink:0">
                            {{ number_format($entry->hours, 2) }}h
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>
            @empty
            <div class="card border-0 text-center py-5" style="background:var(--bg-raised)">
                <p class="text-muted small mb-0">No timed entries on this timesheet.</p>
            </div>
            @endforelse
        </div>

        {{-- RIGHT: Summary --}}
        <div class="col-lg-4">

            {{-- Grand total --}}
            <div class="card border-0 text-center mb-3 py-4" style="background:var(--bg-raised);border:1px solid var(--border-faint)!important">
                <div style="font-family:var(--font-display);font-size:48px;font-weight:700;color:var(--accent);line-height:1">
                    {{ number_format($totalHours, 2) }}
                </div>
                <div style="font-family:var(--font-mono);font-size:11px;color:var(--text-3);text-transform:uppercase;letter-spacing:.1em;margin-top:4px">
                    Total Hours
                </div>
            </div>

            {{-- Per-project summary --}}
            <div class="card border-0 mb-3" style="background:var(--bg-raised);border:1px solid var(--border-faint)!important">
                <div class="card-header" style="padding:10px 16px">
                    <span style="font-family:var(--font-mono);font-size:9.5px;font-weight:500;text-transform:uppercase;letter-spacing:.1em;color:var(--text-3)">
                        By Project
                    </span>
                </div>
                <div class="card-body p-0">
                    @foreach($byProject as $group)
                    <div class="d-flex align-items-center gap-2 px-4 py-2" style="border-bottom:1px solid var(--border-faint)">
                        <span style="width:8px;height:8px;border-radius:2px;background:{{ $group['project']->color ?? '#7c7c98' }};flex-shrink:0"></span>
                        <span style="font-size:13px;color:var(--text-1);flex:1">{{ $group['project']->name }}</span>
                        <span style="font-family:var(--font-mono);font-size:11px;font-weight:600;color:var(--accent)">
                            {{ number_format($group['hours'], 2) }}h
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>

            @if($timesheet->notes)
            <div class="card border-0" style="background:var(--bg-raised);border:1px solid var(--border-faint)!important">
                <div class="card-header" style="padding:10px 16px">
                    <span style="font-family:var(--font-mono);font-size:9.5px;font-weight:500;text-transform:uppercase;letter-spacing:.1em;color:var(--text-3)">
                        Notes
                    </span>
                </div>
                <div class="card-body px-4 py-3">
                    <p class="small mb-0" style="color:var(--text-2);white-space:pre-line">{{ $timesheet->notes }}</p>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

<style>
@media print {
    .wt-nav, .wt-mob-nav, a[href="{{ route('timesheets.index') }}"], .d-flex.gap-2 { display: none !important; }
    body { background: white !important; color: black !important; }
    .card { border: 1px solid #ddd !important; background: white !important; }
}
</style>
</x-layouts.app>
