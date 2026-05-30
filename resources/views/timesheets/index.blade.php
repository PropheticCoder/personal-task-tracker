<x-layouts.app>
<div class="container-lg py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h5 fw-semibold mb-1" style="font-family:var(--font-display)">Timesheets</h1>
            <p style="font-family:var(--font-mono);font-size:11px;color:var(--text-3);margin:0">Time logged across tasks, ready to bill</p>
        </div>
        <a href="{{ route('timesheets.create') }}" class="wt-btn wt-btn-accent" style="padding:7px 16px">
            <i class="bi bi-plus-lg" style="font-size:11px"></i> New Timesheet
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 small mb-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($timesheets->isEmpty())
        <div class="card border-0 text-center py-5" style="background:var(--bg-raised)">
            <div style="font-size:32px;margin-bottom:12px">🕐</div>
            <p class="fw-medium mb-1">No timesheets yet</p>
            <p style="font-family:var(--font-mono);font-size:11px;color:var(--text-3);margin-bottom:20px">
                Log time on tasks, then create a timesheet to group and bill them.
            </p>
            <div>
                <a href="{{ route('timesheets.create') }}" class="wt-btn wt-btn-accent" style="padding:7px 16px">
                    Create first timesheet
                </a>
            </div>
        </div>
    @else
    <div class="d-flex flex-column gap-3">
        @foreach($timesheets as $ts)
        @php $hours = round($ts->activities->sum('hours'), 2); @endphp
        <a href="{{ route('timesheets.show', $ts) }}" class="card border-0 text-decoration-none" style="background:var(--bg-raised);border:1px solid var(--border-faint)!important;transition:border-color .2s,box-shadow .2s" onmouseover="this.style.borderColor='var(--border-base)'" onmouseout="this.style.borderColor='var(--border-faint)'">
            <div class="card-body py-3 px-4">
                <div class="d-flex align-items-center gap-4">

                    {{-- Hours bubble --}}
                    <div class="text-center flex-shrink-0" style="width:56px">
                        <div style="font-family:var(--font-display);font-size:22px;font-weight:700;color:var(--text-1);line-height:1">{{ $hours }}</div>
                        <div style="font-family:var(--font-mono);font-size:9px;color:var(--text-3);text-transform:uppercase;letter-spacing:.08em">hrs</div>
                    </div>

                    <div style="width:1px;height:36px;background:var(--border-faint);flex-shrink:0"></div>

                    {{-- Title + period --}}
                    <div class="flex-grow-1">
                        <div class="fw-semibold mb-1" style="color:var(--text-1)">{{ $ts->title }}</div>
                        <div style="font-family:var(--font-mono);font-size:10px;color:var(--text-3)">
                            {{ $ts->period_start->format('d M') }} — {{ $ts->period_end->format('d M Y') }}
                            @if($ts->client) · {{ $ts->client->name }} @endif
                        </div>
                    </div>

                    {{-- Status + entry count --}}
                    <div class="d-flex align-items-center gap-3 flex-shrink-0">
                        <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-3)">
                            {{ $ts->activities->count() }} {{ Str::plural('entry', $ts->activities->count()) }}
                        </span>
                        <span class="badge" style="font-family:var(--font-mono);font-size:9px;font-weight:500;
                            {{ $ts->status === 'submitted'
                                ? 'background:var(--success-bg);color:var(--success);border:1px solid rgba(45,212,170,.3)'
                                : 'background:var(--warning-bg);color:var(--warning);border:1px solid rgba(245,181,63,.3)' }}">
                            {{ ucfirst($ts->status) }}
                        </span>
                        <i class="bi bi-chevron-right" style="color:var(--text-3);font-size:12px"></i>
                    </div>

                </div>
            </div>
        </a>
        @endforeach
    </div>
    @endif

</div>
</x-layouts.app>
