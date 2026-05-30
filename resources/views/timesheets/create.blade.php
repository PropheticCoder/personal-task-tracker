<x-layouts.app>
<div class="container-lg py-4">

    <a href="{{ route('timesheets.index') }}" class="text-decoration-none text-muted small mb-3 d-inline-flex align-items-center gap-1">
        <i class="bi bi-arrow-left"></i> Timesheets
    </a>

    <div class="d-flex align-items-start justify-content-between mb-4">
        <div>
            <h1 class="h5 fw-semibold mb-1" style="font-family:var(--font-display)">New Combined Timesheet</h1>
            <p style="font-family:var(--font-mono);font-size:11px;color:var(--text-3);margin:0">
                Select task timesheets or individual activities to combine for billing
            </p>
        </div>
    </div>

    <form method="POST" action="{{ route('timesheets.store') }}">
        @csrf

        <div class="row g-4">

            {{-- LEFT: Details + Total --}}
            <div class="col-lg-4">
                <div class="card border-0 mb-3" style="background:var(--bg-raised);border:1px solid var(--border-faint)!important">
                    <div class="card-body p-4 d-flex flex-column gap-3">

                        <div>
                            <label class="wt-mono-label">Title</label>
                            <input type="text" name="title"
                                   class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title', 'Week of '.now()->startOfWeek()->format('d M Y')) }}"
                                   required autofocus />
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row g-2">
                            <div class="col-6">
                                <label class="wt-mono-label">From</label>
                                <input type="date" name="period_start" class="form-control form-control-sm"
                                       value="{{ old('period_start', now()->startOfWeek()->format('Y-m-d')) }}" required />
                            </div>
                            <div class="col-6">
                                <label class="wt-mono-label">To</label>
                                <input type="date" name="period_end" class="form-control form-control-sm"
                                       value="{{ old('period_end', now()->endOfWeek()->format('Y-m-d')) }}" required />
                            </div>
                        </div>

                        <div>
                            <label class="wt-mono-label">Client <span style="color:var(--text-3)">(optional)</span></label>
                            <select name="client_id" class="form-select form-select-sm">
                                <option value="">— no client —</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="wt-mono-label">Notes <span style="color:var(--text-3)">(optional)</span></label>
                            <textarea name="notes" class="form-control form-control-sm" rows="3"
                                      placeholder="Billing notes, invoice reference…">{{ old('notes') }}</textarea>
                        </div>

                    </div>
                </div>

                {{-- Running total --}}
                <div class="card border-0 text-center py-4" style="background:var(--bg-raised);border:1px solid var(--border-faint)!important">
                    <div style="font-family:var(--font-display);font-size:40px;font-weight:700;color:var(--accent);line-height:1" id="grand-total">0</div>
                    <div style="font-family:var(--font-mono);font-size:10px;color:var(--text-3);text-transform:uppercase;letter-spacing:.1em;margin-top:6px">Total Hours</div>
                    <div style="font-family:var(--font-mono);font-size:10px;color:var(--text-3);margin-top:4px" id="entry-count">0 entries selected</div>
                </div>

                <div class="mt-3 d-flex flex-column gap-2">
                    <button type="submit" class="wt-btn wt-btn-accent w-100" style="padding:9px;justify-content:center">
                        <i class="bi bi-file-earmark-check me-1"></i> Create Timesheet
                    </button>
                    <a href="{{ route('timesheets.index') }}" class="btn btn-outline-secondary btn-sm w-100">Cancel</a>
                </div>
            </div>

            {{-- RIGHT: Selection --}}
            <div class="col-lg-8">

                {{-- Task Timesheets --}}
                @if($taskTimesheets->isNotEmpty())
                <div class="card border-0 mb-3" style="background:var(--bg-raised);border:1px solid var(--border-faint)!important">
                    <div style="padding:14px 20px;border-bottom:1px solid var(--border-faint);display:flex;align-items:center;justify-content:space-between">
                        <div>
                            <span style="font-family:var(--font-mono);font-size:10px;font-weight:500;text-transform:uppercase;letter-spacing:.1em;color:var(--text-2)">Task Timesheets</span>
                            <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-3);margin-left:8px">Each task's logged hours</span>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" onclick="selectAllTS()" class="wt-btn" style="padding:3px 10px;font-size:10px">All</button>
                            <button type="button" onclick="clearAllTS()" class="wt-btn" style="padding:3px 10px;font-size:10px">None</button>
                        </div>
                    </div>

                    @foreach($taskTimesheets as $projectId => $sheets)
                    @php $project = $sheets->first()->task?->project; @endphp
                    <div style="border-bottom:1px solid var(--border-faint)">
                        <div style="padding:8px 20px;background:rgba(0,0,0,.12);display:flex;align-items:center;gap:8px">
                            <span style="width:8px;height:8px;border-radius:2px;background:{{ $project?->color ?? '#7c7c98' }};flex-shrink:0"></span>
                            <span style="font-family:var(--font-mono);font-size:10px;font-weight:500;color:var(--text-2);text-transform:uppercase;letter-spacing:.08em">
                                {{ $project?->name ?? 'Unknown Project' }}
                            </span>
                        </div>

                        @foreach($sheets as $ts)
                        @php $tsHours = round($ts->activities->whereNotNull('hours')->sum('hours'), 2); @endphp
                        <label class="d-flex align-items-start gap-3 px-4 py-3 ts-row" style="cursor:pointer;transition:background .15s;border-bottom:1px solid var(--border-faint)" onmouseover="this.style.background='rgba(255,255,255,.03)'" onmouseout="this.style.background='transparent'">
                            <input type="checkbox" name="timesheet_ids[]" value="{{ $ts->id }}"
                                   class="form-check-input mt-1 flex-shrink-0 ts-check"
                                   data-hours="{{ $tsHours }}"
                                   onchange="recalc()"
                                   {{ in_array($ts->id, old('timesheet_ids', [])) ? 'checked' : '' }} />
                            <div class="flex-grow-1">
                                <div style="font-size:13px;font-weight:500;color:var(--text-1);margin-bottom:2px">
                                    {{ $ts->task?->title ?? $ts->title }}
                                </div>
                                <div style="font-family:var(--font-mono);font-size:10px;color:var(--text-3)">
                                    {{ $ts->activities->whereNotNull('hours')->count() }} {{ Str::plural('entry', $ts->activities->whereNotNull('hours')->count()) }}
                                    · {{ $ts->period_start->format('d M') }}
                                    @if($ts->period_start != $ts->period_end) – {{ $ts->period_end->format('d M') }} @endif
                                </div>
                            </div>
                            <div style="font-family:var(--font-display);font-size:16px;font-weight:700;color:var(--accent);flex-shrink:0">
                                {{ $tsHours }}h
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Loose activities --}}
                @if($looseActivities->isNotEmpty())
                <div class="card border-0" style="background:var(--bg-raised);border:1px solid var(--border-faint)!important">
                    <div style="padding:14px 20px;border-bottom:1px solid var(--border-faint);display:flex;align-items:center;justify-content:space-between">
                        <div>
                            <span style="font-family:var(--font-mono);font-size:10px;font-weight:500;text-transform:uppercase;letter-spacing:.1em;color:var(--text-2)">Individual Activities</span>
                            <span style="font-family:var(--font-mono);font-size:10px;color:var(--text-3);margin-left:8px">Not yet on a timesheet</span>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" onclick="selectAllAct()" class="wt-btn" style="padding:3px 10px;font-size:10px">All</button>
                            <button type="button" onclick="clearAllAct()" class="wt-btn" style="padding:3px 10px;font-size:10px">None</button>
                        </div>
                    </div>

                    @foreach($looseActivities as $projectId => $activities)
                    @php $project = $activities->first()->project; @endphp
                    <div style="border-bottom:1px solid var(--border-faint)">
                        <div style="padding:8px 20px;background:rgba(0,0,0,.12);display:flex;align-items:center;gap:8px">
                            <span style="width:8px;height:8px;border-radius:2px;background:{{ $project?->color ?? '#7c7c98' }};flex-shrink:0"></span>
                            <span style="font-family:var(--font-mono);font-size:10px;font-weight:500;color:var(--text-2);text-transform:uppercase;letter-spacing:.08em">
                                {{ $project?->name ?? 'Unknown' }}
                            </span>
                        </div>

                        @foreach($activities as $activity)
                        <label class="d-flex align-items-start gap-3 px-4 py-3 act-row" style="cursor:pointer;transition:background .15s;border-bottom:1px solid var(--border-faint)" onmouseover="this.style.background='rgba(255,255,255,.03)'" onmouseout="this.style.background='transparent'">
                            <input type="checkbox" name="activity_ids[]" value="{{ $activity->id }}"
                                   class="form-check-input mt-1 flex-shrink-0 act-check"
                                   data-hours="{{ $activity->hours }}"
                                   onchange="recalc()"
                                   {{ in_array($activity->id, old('activity_ids', [])) ? 'checked' : '' }} />
                            <div class="flex-grow-1">
                                <div style="font-size:13px;font-weight:500;color:var(--text-1);margin-bottom:2px">
                                    {{ $activity->body ?? ucfirst(str_replace('_', ' ', $activity->type)) }}
                                </div>
                                @if($activity->task)
                                <div style="font-family:var(--font-mono);font-size:10px;color:var(--text-3)">
                                    <i class="bi bi-check2-square me-1"></i>{{ $activity->task->title }}
                                    · {{ $activity->created_at->format('d M') }}
                                </div>
                                @endif
                            </div>
                            <div style="font-family:var(--font-display);font-size:16px;font-weight:700;color:var(--accent);flex-shrink:0">
                                {{ $activity->hours }}h
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @endforeach
                </div>
                @endif

                @if($taskTimesheets->isEmpty() && $looseActivities->isEmpty())
                <div class="card border-0 text-center py-5" style="background:var(--bg-raised)">
                    <div style="font-size:32px;margin-bottom:12px">⏱</div>
                    <p class="fw-medium mb-1" style="color:var(--text-1)">No timed activities yet</p>
                    <p style="font-family:var(--font-mono);font-size:11px;color:var(--text-3);margin:0">
                        Open a task and log time using the note form's "hrs" field.
                    </p>
                </div>
                @endif

            </div>
        </div>
    </form>

</div>

<script>
function recalc() {
    let total = 0, count = 0;
    document.querySelectorAll('.ts-check:checked, .act-check:checked').forEach(cb => {
        total += parseFloat(cb.dataset.hours) || 0;
        count++;
    });
    document.getElementById('grand-total').textContent = Math.round(total * 100) / 100;
    document.getElementById('entry-count').textContent = count + ' ' + (count === 1 ? 'item' : 'items') + ' selected';
}

function selectAllTS()  { document.querySelectorAll('.ts-check').forEach(c  => { c.checked = true;  }); recalc(); }
function clearAllTS()   { document.querySelectorAll('.ts-check').forEach(c  => { c.checked = false; }); recalc(); }
function selectAllAct() { document.querySelectorAll('.act-check').forEach(c => { c.checked = true;  }); recalc(); }
function clearAllAct()  { document.querySelectorAll('.act-check').forEach(c => { c.checked = false; }); recalc(); }

document.addEventListener('DOMContentLoaded', recalc);
</script>
</x-layouts.app>
