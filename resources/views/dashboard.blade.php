<x-layouts.app>
<div class="wt-layout d-none d-lg-flex">

    {{-- ── SIDEBAR ── --}}
    <aside class="wt-sidebar">
        <div class="wt-sb-block">
            <span class="wt-mono-label">Projects</span>
            <div id="sb-projects" style="display:flex;flex-direction:column;gap:1px"></div>
            <a href="/projects/create" id="sb-new-project" style="display:flex;align-items:center;gap:8px;padding:6px 8px;border-radius:6px;text-decoration:none;margin-top:6px;transition:background .15s" class="wt-sb-proj-link">
                <i class="bi bi-plus" style="font-size:13px;color:var(--text-3);width:8px"></i>
                <span style="font-size:13px;color:var(--text-3)">New project</span>
            </a>
        </div>

        <div class="wt-sb-block">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
                <span class="wt-mono-label" style="margin:0">Tools</span>
                <a href="/tools" style="font-family:var(--font-mono);font-size:9.5px;color:var(--accent);text-decoration:none">All →</a>
            </div>
            @foreach([['text-to-qr','Text → QR','bi-qr-code'],['csv-to-sql','CSV → SQL','bi-table'],['pdf-generate','PDF Export','bi-file-pdf'],['pdf-read','PDF Read','bi-file-earmark-text'],['word-to-html','Word → HTML','bi-filetype-doc']] as [$key,$label,$icon])
            <button onclick="openTool('{{ $key }}','{{ $label }}')" class="wt-tool-btn" style="display:flex;align-items:center;gap:8px;width:100%;background:none;border:none;padding:6px 8px;border-radius:6px;cursor:pointer;margin-bottom:1px;text-align:left;transition:background .15s">
                <i class="bi {{ $icon }}" style="font-size:13px;color:var(--text-2);width:16px;text-align:center"></i>
                <span style="font-size:13px;color:var(--text-2)">{{ $label }}</span>
            </button>
            @endforeach
        </div>
    </aside>

    {{-- ── MAIN ── --}}
    <main class="wt-main">

        {{-- Header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:22px;flex-wrap:wrap">
            <div>
                <h1 style="font-family:var(--font-display);font-size:20px;font-weight:600;letter-spacing:-0.03em;margin:0 0 2px">Today's Picture</h1>
                <p style="font-family:var(--font-mono);font-size:11px;color:var(--text-3);margin:0">{{ now()->format('l, d F Y') }}</p>
            </div>
            <div style="display:flex;align-items:center;gap:8px">
                <div style="position:relative">
                    <i class="bi bi-funnel" style="position:absolute;left:9px;top:50%;transform:translateY(-50%);font-size:11px;color:var(--text-3);pointer-events:none"></i>
                    <select id="filter-project" onchange="TT.setFilter('project',this.value)"
                        style="background:var(--bg-elevated);border:1px solid var(--border-base);border-radius:7px;color:var(--text-1);font-family:var(--font-mono);font-size:11px;padding:6px 12px 6px 28px;outline:none;cursor:pointer;appearance:none;min-width:170px">
                        <option value="">All projects</option>
                    </select>
                </div>
                <div style="position:relative">
                    <i class="bi bi-search" style="position:absolute;left:9px;top:50%;transform:translateY(-50%);font-size:11px;color:var(--text-3);pointer-events:none"></i>
                    <input id="filter-search" type="text" oninput="TT.setFilter('search',this.value)" placeholder="Search tasks…"
                        style="background:var(--bg-elevated);border:1px solid var(--border-base);border-radius:7px;color:var(--text-1);font-family:var(--font-mono);font-size:11px;padding:6px 12px 6px 28px;outline:none;width:175px"
                        onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border-base)'" />
                </div>
                <button id="btn-add-task" onclick="TT.showAddTask()" class="wt-btn wt-btn-accent" style="padding:6px 14px">
                    <i class="bi bi-plus-lg" style="font-size:11px"></i> Add Task
                </button>
            </div>
        </div>

        {{-- Projects strip --}}
        <div style="margin-bottom:28px">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
                <span style="font-family:var(--font-mono);font-size:10px;font-weight:500;text-transform:uppercase;letter-spacing:.12em;color:var(--text-3)">Projects</span>
                <a href="/projects/create" class="wt-btn" style="padding:4px 12px;font-size:10px"><i class="bi bi-plus-lg"></i> New</a>
            </div>
            <div id="projects-strip" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:10px"></div>
        </div>

        <div style="border-top:1px solid var(--border-faint);margin-bottom:20px"></div>

        {{-- Three columns --}}
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;align-items:start">

            {{-- WORKING ON --}}
            <div>
                <div style="display:flex;align-items:center;gap:7px;margin-bottom:12px">
                    <span style="width:8px;height:8px;border-radius:50%;background:var(--info);box-shadow:0 0 6px rgba(75,174,255,.4)"></span>
                    <span style="font-family:var(--font-mono);font-size:10px;font-weight:500;text-transform:uppercase;letter-spacing:.12em;color:var(--info)">Working On</span>
                    <span id="count-working" style="font-family:var(--font-mono);font-size:9px;color:var(--text-3);margin-left:auto">0</span>
                </div>
                <div id="col-working" style="display:flex;flex-direction:column;gap:8px"></div>
                <button onclick="TT.showColAdd('working')" class="tt-col-add-btn" style="width:100%;margin-top:10px;background:none;border:1px dashed var(--border-faint);border-radius:8px;color:var(--text-3);font-family:var(--font-mono);font-size:11px;padding:8px;cursor:pointer;transition:all .15s;display:flex;align-items:center;justify-content:center;gap:4px">
                    <i class="bi bi-plus" style="font-size:13px"></i> Add task
                </button>
            </div>

            {{-- WAITING ON --}}
            <div>
                <div style="display:flex;align-items:center;gap:7px;margin-bottom:12px">
                    <span style="width:8px;height:8px;border-radius:50%;background:var(--warning);animation:pip-pulse 2s ease-in-out infinite"></span>
                    <span style="font-family:var(--font-mono);font-size:10px;font-weight:500;text-transform:uppercase;letter-spacing:.12em;color:var(--warning)">Waiting On</span>
                    <span id="count-waiting" style="font-family:var(--font-mono);font-size:9px;color:var(--text-3);margin-left:auto">0</span>
                </div>
                <div id="col-waiting" style="display:flex;flex-direction:column;gap:8px"></div>
                <button onclick="TT.showColAdd('waiting')" class="tt-col-add-btn" style="width:100%;margin-top:10px;background:none;border:1px dashed var(--border-faint);border-radius:8px;color:var(--text-3);font-family:var(--font-mono);font-size:11px;padding:8px;cursor:pointer;transition:all .15s;display:flex;align-items:center;justify-content:center;gap:4px">
                    <i class="bi bi-plus" style="font-size:13px"></i> Add blocked task
                </button>
            </div>

            {{-- THEY NEED FROM ME --}}
            <div>
                <div style="display:flex;align-items:center;gap:7px;margin-bottom:12px">
                    <span style="width:8px;height:8px;border-radius:50%;background:var(--success)"></span>
                    <span style="font-family:var(--font-mono);font-size:10px;font-weight:500;text-transform:uppercase;letter-spacing:.12em;color:var(--success)">They Need From Me</span>
                    <span id="count-they-need" style="font-family:var(--font-mono);font-size:9px;color:var(--text-3);margin-left:auto">0</span>
                </div>
                <div id="col-they-need" style="display:flex;flex-direction:column;gap:8px"></div>
                <button onclick="TT.showColAdd('they-need')" class="tt-col-add-btn" style="width:100%;margin-top:10px;background:none;border:1px dashed var(--border-faint);border-radius:8px;color:var(--text-3);font-family:var(--font-mono);font-size:11px;padding:8px;cursor:pointer;transition:all .15s;display:flex;align-items:center;justify-content:center;gap:4px">
                    <i class="bi bi-plus" style="font-size:13px"></i> Add commitment
                </button>
            </div>

        </div>


    </main>
</div>

{{-- MOBILE --}}
<div class="d-lg-none" style="padding-bottom:150px">

    {{-- Floating Add Task button --}}
    <button onclick="TT.showAddTask()"
        style="position:fixed;bottom:80px;right:16px;z-index:250;
               width:54px;height:54px;border-radius:50%;
               background:var(--accent);border:none;color:#fff;
               font-size:22px;cursor:pointer;
               display:flex;align-items:center;justify-content:center;
               box-shadow:0 4px 20px rgba(124,121,255,.45);
               transition:transform .15s,box-shadow .15s"
        onmousedown="this.style.transform='scale(.94)'"
        onmouseup="this.style.transform='scale(1)'"
        ontouchstart="this.style.transform='scale(.94)'"
        ontouchend="this.style.transform='scale(1)'"
        title="Add task">
        <i class="bi bi-plus-lg"></i>
    </button>

    {{-- Sticky filter bar --}}
    <div style="position:sticky;top:50px;z-index:100;background:rgba(19,20,26,.96);-webkit-backdrop-filter:blur(14px);backdrop-filter:blur(14px);border-bottom:1px solid var(--border-base);padding:10px 16px">
        <div style="display:flex;gap:8px">
            <select id="m-filter-project" onchange="TT.setFilter('project',this.value)"
                style="flex:1;min-width:0;background:var(--bg-elevated);border:1px solid var(--border-base);border-radius:7px;color:var(--text-1);font-family:var(--font-mono);font-size:11px;padding:7px 10px;outline:none;appearance:none">
                <option value="">All projects</option>
            </select>
            <input id="m-filter-search" type="text" oninput="TT.setFilter('search',this.value)" placeholder="Search…"
                style="flex:1;min-width:0;background:var(--bg-elevated);border:1px solid var(--border-base);border-radius:7px;color:var(--text-1);font-family:var(--font-mono);font-size:11px;padding:7px 10px;outline:none" />
        </div>
    </div>

    <div style="padding:20px 16px 16px">

        {{-- Projects (collapsible) --}}
        <div style="margin-bottom:20px">
            <div style="display:flex;align-items:center;margin-bottom:0">
                <button data-bs-toggle="collapse" data-bs-target="#m-projects-collapse"
                    style="display:flex;align-items:center;gap:7px;background:none;border:none;cursor:pointer;padding:0 0 10px;flex:1;text-align:left">
                    <span style="font-family:var(--font-mono);font-size:10px;font-weight:500;text-transform:uppercase;letter-spacing:.12em;color:var(--text-3)">Projects</span>
                    <span id="m-proj-count" style="font-family:var(--font-mono);font-size:9px;color:var(--text-3)"></span>
                    <i class="bi bi-chevron-down" style="font-size:9px;color:var(--text-3);margin-left:auto;transition:transform .2s" id="m-proj-chevron"></i>
                </button>
                <a href="/projects/create"
                    style="font-family:var(--font-mono);font-size:10px;color:var(--text-3);text-decoration:none;padding:0 0 10px 12px;display:flex;align-items:center;gap:3px;transition:color .15s"
                    onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text-3)'">
                    <i class="bi bi-plus" style="font-size:13px"></i> New
                </a>
            </div>
            <div class="collapse show" id="m-projects-collapse">
                <div id="m-projects-strip" style="display:flex;flex-direction:column;gap:6px"></div>
            </div>
        </div>

        <div style="border-top:1px solid var(--border-faint);margin:0 0 20px"></div>

        <div id="m-col-working" style="display:flex;flex-direction:column;gap:8px;margin-bottom:28px"></div>
        <div id="m-col-waiting" style="display:flex;flex-direction:column;gap:8px;margin-bottom:28px"></div>
        <div id="m-col-they-need" style="display:flex;flex-direction:column;gap:8px"></div>
    </div>
</div>

{{-- ADD TASK MODAL --}}
<div class="modal fade" id="addTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold">New Task</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="wt-mono-label">Task</label>
                    <input id="new-title" type="text" class="form-control" placeholder="What needs doing?" />
                </div>
                <div class="mb-3">
                    <label class="wt-mono-label">Project</label>
                    <select id="new-project" class="form-select">
                        <option value="">— select project —</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="wt-mono-label">Priority</label>
                    <div style="display:flex;gap:8px">
                        @foreach(['high','med','low'] as $p)
                        <label style="flex:1;cursor:pointer">
                            <input type="radio" name="new-priority" value="{{ $p }}" style="display:none" />
                            <div class="tt-prio-opt tt-prio-{{ $p }}" style="text-align:center;padding:6px;border-radius:6px;font-family:var(--font-mono);font-size:10px;border:1px solid var(--border-base);transition:all .15s;cursor:pointer">{{ strtoupper($p) }}</div>
                        </label>
                        @endforeach
                    </div>
                </div>
                <hr style="border-color:var(--border-faint);margin:16px 0">
                <p style="font-family:var(--font-mono);font-size:10px;color:var(--text-3);margin-bottom:12px;text-transform:uppercase;letter-spacing:.1em">Optional</p>
                <div class="mb-3">
                    <label class="wt-mono-label" style="display:flex;align-items:center;gap:6px">
                        <span style="width:8px;height:8px;border-radius:50%;background:var(--success)"></span>
                        Someone needs this from me
                    </label>
                    <div style="display:flex;gap:8px">
                        <input id="new-needed-person" type="text" class="form-control form-control-sm" placeholder="Who needs it?" />
                        <input id="new-needed-date" type="date" class="form-control form-control-sm" />
                    </div>
                </div>
                <div>
                    <label class="wt-mono-label" style="display:flex;align-items:center;gap:6px">
                        <span style="width:8px;height:8px;border-radius:50%;background:var(--warning)"></span>
                        I'm already blocked
                    </label>
                    <div style="display:flex;gap:8px">
                        <input id="new-blocked-person" type="text" class="form-control form-control-sm" placeholder="Who's blocking you?" />
                        <input id="new-blocked-what" type="text" class="form-control form-control-sm" placeholder="What do you need?" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button onclick="TT.submitAddTask()" class="wt-btn wt-btn-accent" style="padding:6px 20px">Add Task</button>
            </div>
        </div>
    </div>
</div>

{{-- COLUMN ADD MODAL --}}
<div class="modal fade" id="colAddModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold" id="col-add-title">Add Task</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="col-add-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button onclick="TT.submitColAdd()" class="wt-btn wt-btn-accent" style="padding:6px 20px">Add</button>
            </div>
        </div>
    </div>
</div>

{{-- TOOL MODAL --}}
<div class="modal fade" id="toolModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold" id="tool-title">Run Tool</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="wt-mono-label">Input</label>
                    <textarea class="form-control" rows="5" placeholder="Paste your content here…"></textarea>
                </div>
                <div style="background:var(--bg-elevated);border:1px solid var(--border-faint);border-radius:8px;padding:12px">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="tool-link-toggle" role="switch">
                        <label class="form-check-label" for="tool-link-toggle" style="font-size:13px;font-weight:500">Link result to a task</label>
                    </div>
                    <div id="tool-link-panel" style="display:none;margin-top:10px">
                        <select id="tool-task-select" class="form-select form-select-sm"></select>
                        <p style="font-family:var(--font-mono);font-size:10px;color:var(--text-3);margin:6px 0 0">Output appears in the task's activity timeline.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a id="tool-full-link" href="/tools" style="font-family:var(--font-mono);font-size:11px;color:var(--text-2);text-decoration:none;margin-right:auto">Full tool page →</a>
                <button class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button class="wt-btn wt-btn-accent" style="padding:6px 18px"><i class="bi bi-play-fill" style="font-size:11px"></i> Run</button>
            </div>
        </div>
    </div>
</div>

<style>
.tt-col-add-btn:hover { border-color:var(--border-base)!important;color:var(--text-1)!important; }
.wt-sb-proj-link:hover,.wt-tool-btn:hover { background:rgba(255,255,255,.04); }
.wt-tool-btn:hover span,.wt-tool-btn:hover i { color:var(--text-1)!important; }

/* Task cards */
.tt-card { background:var(--bg-raised);border:1px solid var(--border-faint);border-radius:8px;padding:11px 13px;transition:border-color .2s,box-shadow .2s;animation:card-in .35s cubic-bezier(.16,1,.3,1) both; }
.tt-card:hover { border-color:var(--border-base); }
@keyframes card-in { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
.tt-card-out { opacity:0;transform:scale(.97);transition:opacity .25s,transform .25s; }

/* Card inner elements */
.tt-card-head { display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:6px; }
.tt-task-link { font-size:13px;font-weight:500;color:var(--text-1);text-decoration:none;line-height:1.4;flex:1; }
.tt-task-link:hover { color:var(--accent); }
.tt-card-meta { display:flex;align-items:center;gap:6px;flex-wrap:wrap; }
.tt-project-tag { font-family:var(--font-mono);font-size:9.5px;font-weight:500; }
.tt-prio { font-family:var(--font-mono);font-size:9px;padding:1.5px 5px;border-radius:3px; }
.tt-prio.high { color:var(--danger);background:var(--danger-bg); }
.tt-prio.med  { color:var(--warning);background:var(--warning-bg); }
.tt-prio.low  { color:var(--text-3);background:rgba(255,255,255,.04); }

/* Menu button */
.tt-menu-btn { background:none;border:none;color:var(--text-3);cursor:pointer;padding:0 3px;font-size:16px;line-height:1;transition:color .15s;flex-shrink:0; }
.tt-menu-btn:hover { color:var(--text-1); }

/* Blocker block inside card */
.tt-blocker-block { margin-top:8px;background:rgba(244,167,41,.07);border:1px solid rgba(244,167,41,.2);border-radius:6px;padding:8px 10px; }
.tt-blocker-block.overdue { background:rgba(255,92,108,.07);border-color:rgba(255,92,108,.25); }
.tt-blocker-label { font-family:var(--font-mono);font-size:9px;color:var(--text-3);text-transform:uppercase;letter-spacing:.1em;margin-bottom:3px; }
.tt-blocker-who { font-size:12px;font-weight:600;color:var(--warning); }
.tt-blocker-block.overdue .tt-blocker-who { color:var(--danger); }
.tt-blocker-what { font-size:11px;color:var(--text-2);margin-top:2px; }
.tt-overdue-tag { font-family:var(--font-mono);font-size:9px;color:var(--danger);background:var(--danger-bg);padding:1px 5px;border-radius:3px;margin-top:4px;display:inline-block; }

/* Commitment badge on working card */
.tt-commit-badge { margin-top:7px;background:rgba(46,196,160,.07);border:1px solid rgba(46,196,160,.2);border-radius:5px;padding:5px 8px;font-size:11px;color:var(--success);display:flex;align-items:center;gap:5px; }

/* They-need card: commitment detail */
.tt-need-block { margin-top:8px;padding:8px 10px;background:rgba(46,196,160,.07);border:1px solid rgba(46,196,160,.2);border-radius:6px; }
.tt-need-who { font-size:13px;font-weight:600;color:var(--success); }
.tt-need-date { font-family:var(--font-mono);font-size:10px;color:var(--text-2);margin-top:2px; }
.tt-need-also-blocked { margin-top:6px;font-size:11px;color:var(--warning);display:flex;align-items:center;gap:4px; }

/* Resolve button */
.tt-resolve-btn { display:block;width:100%;margin-top:8px;background:none;border:1px solid rgba(46,196,160,.3);border-radius:5px;color:var(--success);font-family:var(--font-mono);font-size:10px;padding:5px 8px;cursor:pointer;text-align:left;transition:all .15s; }
.tt-resolve-btn:hover { background:var(--success-bg);border-color:var(--success); }

/* Priority radio opts */
.tt-prio-opt { user-select:none; }
input[name="new-priority"]:checked + .tt-prio-opt { border-color:transparent; }
input[name="new-priority"][value="high"]:checked + .tt-prio-high { background:var(--danger-bg);color:var(--danger);border-color:rgba(255,92,108,.3); }
input[name="new-priority"][value="med"]:checked + .tt-prio-med { background:var(--warning-bg);color:var(--warning);border-color:rgba(244,167,41,.3); }
input[name="new-priority"][value="low"]:checked + .tt-prio-low { background:rgba(255,255,255,.06);color:var(--text-2); }
</style>

<script>
/* ══════════════════════════════════════════════════════
   TaskTracker — Client State Manager
   Replace TT.tasks / TT.projects with Livewire props
   when wiring to the backend. Render logic stays identical.
══════════════════════════════════════════════════════ */
const TT = {

    /* ── State ── */

    clients:  @json($clients),
    projects: @json($projects),
    tasks:    @json($tasks),

    filter: { project: '', search: '' },
    _colAddType: null,
    _addModal: null,
    _colModal: null,

    /* ── Computed views ── */
    workingOn()  { return this._filtered(t => t.status==='working' && !t.blocked_by_person); },
    waitingOn()  { return this._filtered(t => t.status==='working' && !!t.blocked_by_person); },
    theyNeed()   { return this._filtered(t => t.status==='working' && !!t.needed_by_person); },

    _filtered(fn) {
        return this.tasks.filter(t => {
            if (!fn(t)) return false;
            if (this.filter.project && this._project(t)?.name !== this.filter.project) return false;
            if (this.filter.search  && !t.title.toLowerCase().includes(this.filter.search.toLowerCase())) return false;
            return true;
        });
    },

    _project(t) { return this.projects.find(p => p.id === t.project_id); },
    _find(id)   { return this.tasks.find(t => t.id == id); },

    /* ── Server helpers ── */
    _csrf() { return document.querySelector('meta[name="csrf-token"]')?.content ?? ''; },

    async _patch(taskId, body) {
        await fetch(`/tasks/${taskId}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this._csrf(), 'Accept': 'application/json' },
            body: JSON.stringify(body),
        });
    },

    async _post(url, body) {
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this._csrf(), 'Accept': 'application/json' },
            body: JSON.stringify(body),
        });
        if (!res.ok) return null;
        return res.json();
    },

    /* ── Actions ── */
    async setBlocker(id, person, what) {
        const t = this._find(id);
        t.blocked_by_person = person; t.blocked_by_what = what; t.blocked_overdue = false;
        this.render();
        const result = await this._post('/dependencies', {
            direction: 'waiting_on', task_id: id, person_name: person, description: what,
        });
        if (result?.dep_id) t.blocker_dep_id = result.dep_id;
    },

    async clearBlocker(id) {
        const t = this._find(id);
        t.blocked_by_person = null; t.blocked_by_what = null; t.blocked_overdue = false;
        this.render();
        await this._post(`/tasks/${id}/resolve-blocker`, {});
    },

    async setCommitment(id, person, date) {
        const t = this._find(id);
        t.needed_by_person = person; t.needed_by_date = date;
        this.render();
        const result = await this._post('/dependencies', {
            direction: 'i_owe', task_id: id, person_name: person,
            description: `Output needed by ${person}`, needed_by: date || null,
        });
        if (result?.dep_id) t.commitment_dep_id = result.dep_id;
    },

    async clearCommitment(id) {
        const t = this._find(id);
        const depId = t.commitment_dep_id;
        t.needed_by_person = null; t.needed_by_date = null; t.commitment_dep_id = null;
        this.render();
        if (depId) await this._post(`/dependencies/${depId}/resolve`, {});
    },

    markDone(id) {
        document.querySelectorAll(`[data-task-id="${id}"]`).forEach(c => c.classList.add('tt-card-out'));
        setTimeout(() => {
            this.tasks = this.tasks.filter(t => t.id != id);
            this.render();
        }, 260);
        this._patch(id, { status: 'done' });
    },

    async addTask(data) {
        if (!data.project_id) return null;
        const tempId = Date.now();
        const task = {
            id: tempId,
            title: data.title,
            project_id: parseInt(data.project_id),
            priority: data.priority || null,
            status: 'working',
            needed_by_person: null, needed_by_date: null,
            blocked_by_person: null, blocked_by_what: null,
            blocked_overdue: false, blocker_dep_id: null, commitment_dep_id: null,
        };
        this.tasks.push(task);
        this.render();

        const result = await this._post('/tasks', {
            title: data.title, project_id: data.project_id,
            priority: data.priority || null, status: 'working_on',
        });
        if (!result?.id) {
            this.tasks = this.tasks.filter(t => t.id !== tempId);
            this.render();
            return null;
        }
        task.id = result.id;
        return task;
    },

    setFilter(key, val) {
        this.filter[key] = val;
        // Sync desktop ↔ mobile filters
        const map = { project:['filter-project','m-filter-project'], search:['filter-search','m-filter-search'] };
        map[key]?.forEach(id => { const el=document.getElementById(id); if(el && el.value !== val) el.value = val; });
        this.render();
    },

    /* ── UI helpers ── */
    showAddTask() {
        if (!this._addModal) this._addModal = new bootstrap.Modal(document.getElementById('addTaskModal'));
        this._populateProjectSelect('new-project');
        this._addModal.show();
        setTimeout(() => document.getElementById('new-title')?.focus(), 300);
    },

    async submitAddTask() {
        const title     = document.getElementById('new-title')?.value.trim();
        const projectId = document.getElementById('new-project')?.value;
        if (!title)     { document.getElementById('new-title').focus(); return; }
        if (!projectId) { document.getElementById('new-project').focus(); return; }

        const prio          = document.querySelector('input[name="new-priority"]:checked')?.value;
        const neededPerson  = document.getElementById('new-needed-person').value.trim();
        const neededDate    = document.getElementById('new-needed-date').value;
        const blockedPerson = document.getElementById('new-blocked-person').value.trim();
        const blockedWhat   = document.getElementById('new-blocked-what').value.trim();

        // Reset + close before async work
        ['new-title','new-needed-person','new-needed-date','new-blocked-person','new-blocked-what']
            .forEach(id => { const el=document.getElementById(id); if(el) el.value=''; });
        document.querySelectorAll('input[name="new-priority"]').forEach(r => r.checked = false);
        this._addModal.hide();

        const task = await this.addTask({ title, project_id: projectId, priority: prio });
        if (!task) return;

        if (blockedPerson) await this.setBlocker(task.id, blockedPerson, blockedWhat || '(no details)');
        if (neededPerson)  await this.setCommitment(task.id, neededPerson, neededDate || null);
    },

    showColAdd(type) {
        this._colAddType = type;
        if (!this._colModal) this._colModal = new bootstrap.Modal(document.getElementById('colAddModal'));
        const titles = { working:'Add Task', waiting:'Add Blocked Task', 'they-need':'Add Commitment' };
        document.getElementById('col-add-title').textContent = titles[type];
        const projectOpts = this.projects.map(p => `<option value="${p.id}">${p.name}</option>`).join('');
        const bodies = {
            working: `
                <div class="mb-3"><label class="wt-mono-label">Task</label><input id="ca-title" class="form-control" placeholder="What needs doing?" /></div>
                <div class="mb-0"><label class="wt-mono-label">Project</label><select id="ca-project" class="form-select"><option value="">— select —</option>${projectOpts}</select></div>`,
            waiting: `
                <div class="mb-3"><label class="wt-mono-label">Task</label><input id="ca-title" class="form-control" placeholder="What are you working on?" /></div>
                <div class="mb-3"><label class="wt-mono-label">Project</label><select id="ca-project" class="form-select"><option value="">— select —</option>${projectOpts}</select></div>
                <div class="mb-3"><label class="wt-mono-label" style="color:var(--warning)">Who are you waiting on?</label><input id="ca-blocked-person" class="form-control" placeholder="Person name" /></div>
                <div class="mb-0"><label class="wt-mono-label">What do you need from them?</label><input id="ca-blocked-what" class="form-control" placeholder="What they need to provide" /></div>`,
            'they-need': `
                <div class="mb-3"><label class="wt-mono-label">Task</label><input id="ca-title" class="form-control" placeholder="What do they need?" /></div>
                <div class="mb-3"><label class="wt-mono-label">Project</label><select id="ca-project" class="form-select"><option value="">— select —</option>${projectOpts}</select></div>
                <div class="mb-3"><label class="wt-mono-label" style="color:var(--success)">Who needs this from you?</label><input id="ca-needed-person" class="form-control" placeholder="Person name" /></div>
                <div class="mb-0"><label class="wt-mono-label">By when?</label><input id="ca-needed-date" type="date" class="form-control" /></div>`,
        };
        document.getElementById('col-add-body').innerHTML = bodies[type];
        this._colModal.show();
        setTimeout(() => document.getElementById('ca-title')?.focus(), 300);
    },

    async submitColAdd() {
        const title = document.getElementById('ca-title')?.value.trim();
        if (!title) return;

        const projectId      = document.getElementById('ca-project')?.value;
        const blockedPerson  = document.getElementById('ca-blocked-person')?.value.trim();
        const blockedWhat    = document.getElementById('ca-blocked-what')?.value.trim();
        const neededPerson   = document.getElementById('ca-needed-person')?.value.trim();
        const neededDate     = document.getElementById('ca-needed-date')?.value;

        if (!projectId) { alert('Please select a project.'); return; }

        this._colModal.hide();

        const task = await this.addTask({ title, project_id: projectId });
        if (!task) return;

        if (this._colAddType === 'waiting' && blockedPerson) {
            await this.setBlocker(task.id, blockedPerson, blockedWhat || '(no details)');
        }
        if (this._colAddType === 'they-need' && neededPerson) {
            await this.setCommitment(task.id, neededPerson, neededDate || null);
        }
    },

    _visibleCard(id) {
        return [...document.querySelectorAll(`[data-task-id="${id}"]`)]
            .find(c => c.offsetParent !== null)
            ?? document.querySelector(`[data-task-id="${id}"]`);
    },

    showBlockerForm(id) {
        document.querySelectorAll(`[data-task-id="${id}"]`).forEach(c => {
            c.querySelector('.tt-blocker-form').style.display = 'block';
            c.querySelector('.tt-card-actions').style.display = 'none';
        });
        this._visibleCard(id)?.querySelector('.tt-bf-person')?.focus();
    },

    confirmBlocker(id) {
        const card = this._visibleCard(id);
        const person = card.querySelector('.tt-bf-person').value.trim();
        const what   = card.querySelector('.tt-bf-what').value.trim();
        if (!person || !what) return;
        this.setBlocker(id, person, what);
    },

    cancelForm(id) {
        document.querySelectorAll(`[data-task-id="${id}"]`).forEach(c => {
            c.querySelectorAll('.tt-blocker-form,.tt-commit-form').forEach(f => f.style.display = 'none');
            const a = c.querySelector('.tt-card-actions');
            if (a) a.style.display = '';
        });
    },

    showCommitForm(id) {
        document.querySelectorAll(`[data-task-id="${id}"]`).forEach(c => {
            c.querySelector('.tt-commit-form').style.display = 'block';
            c.querySelector('.tt-card-actions').style.display = 'none';
        });
        this._visibleCard(id)?.querySelector('.tt-cf-person')?.focus();
    },

    confirmCommit(id) {
        const card = this._visibleCard(id);
        const person = card.querySelector('.tt-cf-person').value.trim();
        const date   = card.querySelector('.tt-cf-date').value;
        if (!person) return;
        this.setCommitment(id, person, date);
    },

    _populateProjectSelect(elId) {
        const sel = document.getElementById(elId);
        if (!sel) return;
        const existing = sel.innerHTML.split('<option value="">')[0];
        sel.innerHTML = '<option value="">— select project —</option>' +
            this.projects.map(p => `<option value="${p.id}">${p.name}</option>`).join('');
    },

    /* ── Render ── */
    render() {
        this._renderCol('col-working',  this.workingOn(),  'working');
        this._renderCol('col-waiting',  this.waitingOn(),  'waiting');
        this._renderCol('col-they-need',this.theyNeed(),   'they-need');
        this._renderCol('m-col-working', this.workingOn(), 'working',  true);
        this._renderCol('m-col-waiting', this.waitingOn(), 'waiting',  true);
        this._renderCol('m-col-they-need',this.theyNeed(),'they-need', true);
        document.getElementById('count-working')?.textContent   && (document.getElementById('count-working').textContent   = this.workingOn().length);
        document.getElementById('count-waiting')?.textContent   && (document.getElementById('count-waiting').textContent   = this.waitingOn().length);
        document.getElementById('count-they-need')?.textContent && (document.getElementById('count-they-need').textContent = this.theyNeed().length);
        this._renderSidebar();
        this._renderProjectsStrip();
        this._renderFilterOptions();
        this._renderToolTaskSelect();
    },

    _renderCol(colId, tasks, type, isMobile) {
        const el = document.getElementById(colId);
        if (!el) return;
        if (isMobile) {
            const labels = { working:'Working On', waiting:'Waiting On', 'they-need':'They Need From Me' };
            const colors = { working:'var(--info)', waiting:'var(--warning)', 'they-need':'var(--success)' };
            const hdr = tasks.length > 0 ? `<div style="display:flex;align-items:center;gap:7px;margin-bottom:10px"><span style="width:7px;height:7px;border-radius:50%;background:${colors[type]}"></span><span style="font-family:var(--font-mono);font-size:10px;text-transform:uppercase;letter-spacing:.12em;color:${colors[type]}">${labels[type]}</span></div>` : '';
            el.innerHTML = hdr + (tasks.length ? tasks.map(t => this._cardHTML(t, type, true)).join('') : '');
            return;
        }
        el.innerHTML = tasks.map(t => this._cardHTML(t, type, false)).join('');
    },

    _cardHTML(t, type, mini) {
        const proj  = this._project(t);
        const pname = proj?.name   || '';
        const pclr  = proj?.color  || '#666';
        const prio  = t.priority   ? `<span class="tt-prio ${t.priority}">${t.priority.toUpperCase()}</span>` : '';
        const leftClr = type==='waiting' ? (t.blocked_overdue?'var(--danger)':'var(--warning)') : pclr;

        let inner = '';

        if (type === 'working') {
            const commitBadge = t.needed_by_person
                ? `<div class="tt-commit-badge"><i class="bi bi-person-check" style="font-size:11px"></i><span style="font-weight:600">${this._esc(t.needed_by_person)}</span> needs this${t.needed_by_date ? ' by ' + this._fmtDate(t.needed_by_date) : ''}<button onclick="TT.clearCommitment(${t.id})" style="margin-left:auto;background:none;border:none;color:var(--text-3);cursor:pointer;font-size:10px;padding:0" title="Remove commitment">×</button></div>` : '';

            inner = `
                <div class="tt-card-head">
                    <a href="/tasks/${t.id}" class="tt-task-link">${this._esc(t.title)}</a>
                    <div class="dropdown tt-card-actions" style="flex-shrink:0">
                        <button class="tt-menu-btn" data-bs-toggle="dropdown">⋮</button>
                        <ul class="dropdown-menu dropdown-menu-end" style="min-width:190px">
                            <li><a class="dropdown-item small" href="#" onclick="event.preventDefault();TT.showBlockerForm(${t.id})"><i class="bi bi-hourglass-split text-warning me-2"></i>I'm blocked on this</a></li>
                            <li><a class="dropdown-item small" href="#" onclick="event.preventDefault();TT.showCommitForm(${t.id})"><i class="bi bi-person-check text-success me-2"></i>Someone needs this from me</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item small" href="#" onclick="event.preventDefault();TT.markDone(${t.id})"><i class="bi bi-check-circle text-muted me-2"></i>Mark done</a></li>
                            <li><a class="dropdown-item small" href="/tasks/${t.id}"><i class="bi bi-arrow-right text-muted me-2"></i>Open task</a></li>
                        </ul>
                    </div>
                </div>
                <div class="tt-card-meta">${ pname ? `<span class="tt-project-tag" style="color:${pclr}">${this._esc(pname)}</span>` : '' }${prio}</div>
                ${commitBadge}
                <div class="tt-blocker-form" style="display:none;margin-top:10px;background:var(--bg-elevated);border:1px solid var(--border-base);border-radius:8px;padding:10px">
                    <p style="font-family:var(--font-mono);font-size:9px;color:var(--warning);text-transform:uppercase;letter-spacing:.1em;margin:0 0 8px">Who are you waiting on?</p>
                    ${this._personSelectForProject(t.project_id, 'tt-bf-person', '— select contact —')}
                    <input type="text" class="tt-bf-what form-control form-control-sm mb-2" placeholder="What do you need from them?" />
                    <div style="display:flex;gap:6px">
                        <button onclick="TT.cancelForm(${t.id})" class="btn btn-outline-secondary btn-sm flex-fill">Cancel</button>
                        <button onclick="TT.confirmBlocker(${t.id})" class="wt-btn wt-btn-accent" style="flex:2;justify-content:center;padding:5px">I'm waiting →</button>
                    </div>
                </div>
                <div class="tt-commit-form" style="display:none;margin-top:10px;background:var(--bg-elevated);border:1px solid var(--border-base);border-radius:8px;padding:10px">
                    <p style="font-family:var(--font-mono);font-size:9px;color:var(--success);text-transform:uppercase;letter-spacing:.1em;margin:0 0 8px">Who needs this from you?</p>
                    ${this._personSelectForProject(t.project_id, 'tt-cf-person', '— select contact —')}
                    <input type="date" class="tt-cf-date form-control form-control-sm mb-2" />
                    <div style="display:flex;gap:6px">
                        <button onclick="TT.cancelForm(${t.id})" class="btn btn-outline-secondary btn-sm flex-fill">Cancel</button>
                        <button onclick="TT.confirmCommit(${t.id})" class="wt-btn wt-btn-accent" style="flex:2;justify-content:center;padding:5px">Flag it</button>
                    </div>
                </div>`;

        } else if (type === 'waiting') {
            const overdueTag  = t.blocked_overdue ? `<span class="tt-overdue-tag">OVERDUE — chase this</span>` : '';
            const alsoCommit  = t.needed_by_person
                ? `<div style="margin-top:7px;font-size:11px;color:var(--success);border-top:1px solid var(--border-faint);padding-top:6px"><i class="bi bi-person-check me-1"></i><strong>${this._esc(t.needed_by_person)}</strong> also needs this${t.needed_by_date?' by '+this._fmtDate(t.needed_by_date):''}</div>` : '';

            inner = `
                <div class="tt-card-head">
                    <a href="/tasks/${t.id}" class="tt-task-link">${this._esc(t.title)}</a>
                    <div class="dropdown" style="flex-shrink:0">
                        <button class="tt-menu-btn" data-bs-toggle="dropdown">⋮</button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item small" href="#" onclick="event.preventDefault();TT.clearBlocker(${t.id})"><i class="bi bi-play-circle text-info me-2"></i>Blocker resolved — continue</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item small" href="#" onclick="event.preventDefault();TT.markDone(${t.id})"><i class="bi bi-check-circle text-muted me-2"></i>Mark done</a></li>
                        </ul>
                    </div>
                </div>
                <div class="tt-card-meta" style="margin-bottom:8px">${ pname ? `<span class="tt-project-tag" style="color:${pclr}">${this._esc(pname)}</span>` : '' }${prio}</div>
                <div class="tt-blocker-block ${t.blocked_overdue?'overdue':''}">
                    <div class="tt-blocker-label">Waiting on</div>
                    <div class="tt-blocker-who">${this._esc(t.blocked_by_person)}</div>
                    <div class="tt-blocker-what">${this._esc(t.blocked_by_what)}</div>
                    ${overdueTag}
                </div>
                <button onclick="TT.clearBlocker(${t.id})" class="tt-resolve-btn">
                    <i class="bi bi-check-lg me-1"></i>Got it — continuing →
                </button>
                ${alsoCommit}`;

        } else { // they-need
            const isBlocked = !!t.blocked_by_person;
            const blockedNote = isBlocked
                ? `<div class="tt-need-also-blocked"><i class="bi bi-exclamation-triangle"></i> Blocked: waiting on <strong>${this._esc(t.blocked_by_person)}</strong></div>` : '';
            const dateStr = t.needed_by_date ? this._fmtDate(t.needed_by_date) : null;
            const overdueTag = t.blocked_overdue ? `<span class="tt-overdue-tag">OVERDUE</span>` : '';

            inner = `
                <div class="tt-card-head">
                    <a href="/tasks/${t.id}" class="tt-task-link">${this._esc(t.title)}</a>
                    <div class="dropdown" style="flex-shrink:0">
                        <button class="tt-menu-btn" data-bs-toggle="dropdown">⋮</button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item small" href="#" onclick="event.preventDefault();TT.clearCommitment(${t.id})"><i class="bi bi-x-circle text-muted me-2"></i>Remove commitment</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item small" href="#" onclick="event.preventDefault();TT.markDone(${t.id})"><i class="bi bi-check-circle text-muted me-2"></i>Mark done</a></li>
                            <li><a class="dropdown-item small" href="/tasks/${t.id}"><i class="bi bi-arrow-right text-muted me-2"></i>Open task</a></li>
                        </ul>
                    </div>
                </div>
                <div class="tt-card-meta">${ pname ? `<span class="tt-project-tag" style="color:${pclr}">${this._esc(pname)}</span>` : '' }${prio}</div>
                <div class="tt-need-block">
                    <div style="font-family:var(--font-mono);font-size:9px;color:var(--text-3);margin-bottom:3px">NEEDED BY</div>
                    <div class="tt-need-who">${this._esc(t.needed_by_person)}</div>
                    ${dateStr ? `<div class="tt-need-date">by ${dateStr}</div>` : ''}
                    ${overdueTag}
                    ${blockedNote}
                </div>`;
        }

        return `<div class="tt-card" data-task-id="${t.id}" style="border-left:3px solid ${leftClr}">${inner}</div>`;
    },

    _renderSidebar() {
        const el = document.getElementById('sb-projects');
        if (!el) return;
        el.innerHTML = this.projects.map(p => `
            <a href="/projects/${p.id}" class="wt-sb-proj-link" style="display:flex;align-items:center;gap:8px;padding:6px 8px;border-radius:6px;text-decoration:none;transition:background .15s">
                <span style="width:8px;height:8px;border-radius:2px;background:${p.color};flex-shrink:0"></span>
                <span style="font-size:13px;font-weight:500;color:var(--text-1);flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${this._esc(p.name)}</span>
                ${p.client ? `<span style="font-family:var(--font-mono);font-size:9px;color:var(--text-3)">${this._esc(p.client)}</span>` : ''}
            </a>`).join('');
    },

    _renderProjectsStrip() {
        const el = document.getElementById('projects-strip');
        if (el) {
            el.innerHTML = this.projects.map(p => `
                <div class="wt-card" style="cursor:default">
                    <div style="height:2px;background:${p.color}"></div>
                    <div style="padding:12px 14px;display:flex;align-items:center;gap:10px">
                        <div style="flex:1;min-width:0">
                            <a href="/projects/${p.id}" style="font-family:var(--font-display);font-size:14px;font-weight:600;color:var(--text-1);letter-spacing:-0.02em;text-decoration:none;display:block;margin-bottom:2px">${this._esc(p.name)}</a>
                            <div style="font-family:var(--font-mono);font-size:10px;color:var(--text-3)">${p.client || 'Internal'}</div>
                        </div>
                        <a href="/update" class="wt-btn" style="padding:4px 10px;font-size:10px;flex-shrink:0;text-decoration:none" title="Daily update">
                            <i class="bi bi-send" style="font-size:10px"></i> Update
                        </a>
                    </div>
                </div>`).join('');
        }
        const mel = document.getElementById('m-projects-strip');
        const countEl = document.getElementById('m-proj-count');
        if (countEl) countEl.textContent = this.projects.length ? `(${this.projects.length})` : '';
        if (!mel) return;
        if (!this.projects.length) {
            mel.innerHTML = `<p style="font-family:var(--font-mono);font-size:11px;color:var(--text-3);text-align:center;padding:16px 0">No projects yet. <a href="/projects/create" style="color:var(--accent);text-decoration:none">Create one →</a></p>`;
            return;
        }
        mel.innerHTML = this.projects.map(p => `
            <a href="/projects/${p.id}" style="display:flex;align-items:center;gap:10px;padding:10px 12px;background:var(--bg-raised);border:1px solid var(--border-faint);border-left:3px solid ${p.color};border-radius:8px;text-decoration:none">
                <div style="flex:1;min-width:0">
                    <div style="font-family:var(--font-display);font-size:14px;font-weight:600;color:var(--text-1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${this._esc(p.name)}</div>
                    <div style="font-family:var(--font-mono);font-size:10px;color:var(--text-3)">${p.client ? this._esc(p.client) : 'Internal'}</div>
                </div>
                <i class="bi bi-chevron-right" style="color:var(--text-3);font-size:12px;flex-shrink:0"></i>
            </a>`).join('');

        // Chevron rotation on collapse toggle
        const collapse = document.getElementById('m-projects-collapse');
        const chevron  = document.getElementById('m-proj-chevron');
        if (collapse && chevron && !collapse._chevronWired) {
            collapse._chevronWired = true;
            collapse.addEventListener('hide.bs.collapse', () => chevron.style.transform = 'rotate(-90deg)');
            collapse.addEventListener('show.bs.collapse', () => chevron.style.transform = 'rotate(0deg)');
        }
    },

    _renderFilterOptions() {
        ['filter-project','m-filter-project'].forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;
            const val = el.value;
            el.innerHTML = '<option value="">All projects</option>' +
                this.projects.map(p => `<option value="${p.name}" ${p.name===val?'selected':''}>${p.name}</option>`).join('');
        });
    },

    _renderToolTaskSelect() {
        const el = document.getElementById('tool-task-select');
        if (!el) return;
        el.innerHTML = '<option value="">— select task —</option>' +
            this.projects.map(p => {
                const ptasks = this.tasks.filter(t => t.project_id===p.id && t.status==='working');
                if (!ptasks.length) return '';
                return `<optgroup label="${p.name}">${ptasks.map(t => `<option value="${t.id}">${t.title}</option>`).join('')}</optgroup>`;
            }).join('');
    },

    generateUpdate(projectId) {
        const p   = this.projects.find(x => x.id === projectId);
        const pts = this.tasks.filter(t => t.project_id === projectId && t.status === 'working');
        const working = pts.filter(t => !t.blocked_by_person && !t.needed_by_person);
        const waiting = pts.filter(t => t.blocked_by_person);
        const commits = pts.filter(t => t.needed_by_person);

        let text = `${p.name}${p.client ? ' — ' + p.client : ''}\n${new Date().toLocaleDateString('en-GB',{day:'numeric',month:'short',year:'numeric'})}\n`;
        if (working.length)  text += `\n🔄 Working On:\n${working.map(t=>'   • '+t.title).join('\n')}`;
        if (waiting.length)  text += `\n⏳ Blocked:\n${waiting.map(t=>`   • ${t.title}\n     → Waiting on ${t.blocked_by_person} for: ${t.blocked_by_what}${t.blocked_overdue?' (OVERDUE)':''}`).join('\n')}`;
        if (commits.length)  text += `\n📋 Commitments:\n${commits.map(t=>`   • ${t.title} → ${t.needed_by_person}${t.needed_by_date?' by '+this._fmtDate(t.needed_by_date):''}`).join('\n')}`;
        if (!pts.length)     text += `\nNo active tasks.`;

        const modal = new bootstrap.Modal(document.createElement('div'));
        // Quick inline update display via a temporary modal
        document.getElementById('__update-text').value = text;
        document.getElementById('__update-project').textContent = p.name;
        new bootstrap.Modal(document.getElementById('updateModal')).show();
    },

    // Generates a grouped <select> of all contacts across all clients.
    // cssClass applied to the <select> element; selectedName pre-selects by name.
    _personSelect(cssClass, placeholder, selectedName) {
        const opts = this.clients.map(c => {
            const options = c.people.map(p => {
                const sel = selectedName && selectedName === p.name ? ' selected' : '';
                const role = p.role ? ` · ${p.role}` : '';
                return `<option value="${this._esc(p.name)}"${sel}>${this._esc(p.name)}${role}</option>`;
            }).join('');
            return `<optgroup label="${this._esc(c.name)}">${options}</optgroup>`;
        }).join('');

        return `<select class="${cssClass} form-select form-select-sm"
                    style="background:var(--bg-raised);border:1px solid var(--border-faint);border-radius:6px;color:var(--text-1);font-family:var(--font-body);font-size:12px;padding:6px 9px;outline:none;margin-bottom:6px">
                    <option value="">${placeholder}</option>
                    ${opts}
                    <optgroup label="─────────────────">
                        <option value="__new">+ Add new contact…</option>
                    </optgroup>
                </select>`;
    },

    _personSelectForProject(projectId, cssClass, placeholder) {
        const proj = this.projects.find(p => p.id === projectId);
        // Collect contacts: project's client people + Internal people
        const relevant = this.clients.filter(c =>
            c.id === null || (proj && c.name === proj.client)
        );
        const listId = `dl-${cssClass}-${projectId}`;
        const options = relevant.flatMap(c =>
            c.people.map(p => {
                const role = p.role ? ` · ${p.role}` : '';
                return `<option value="${this._esc(p.name)}">${this._esc(p.name)}${role}</option>`;
            })
        ).join('');
        return `
            <datalist id="${listId}">${options}</datalist>
            <input type="text" class="${cssClass} form-control form-control-sm mb-2"
                   list="${listId}" placeholder="${placeholder}"
                   style="background:var(--bg-raised);border:1px solid var(--border-faint);color:var(--text-1)" />`;
    },

    _esc(s) {
        if (!s) return '';
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    },

    _fmtDate(d) {
        if (!d) return '';
        try { return new Date(d+'T12:00:00').toLocaleDateString('en-GB',{day:'numeric',month:'short'}); } catch(e){ return d; }
    },

    init() { this.render(); },
};

/* Tool modal helper */
function openTool(key, label) {
    document.getElementById('tool-title').textContent = label;
    document.getElementById('tool-full-link').href = '/tools/' + key;
    document.getElementById('tool-link-toggle').onchange = function() {
        document.getElementById('tool-link-panel').style.display = this.checked ? 'block' : 'none';
    };
    TT._renderToolTaskSelect();
    new bootstrap.Modal(document.getElementById('toolModal')).show();
}

document.addEventListener('DOMContentLoaded', () => TT.init());
</script>

{{-- Update preview modal (generated per project) --}}
<div class="modal fade" id="updateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold"><span id="__update-project"></span> — Update</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <textarea id="__update-text" class="form-control" rows="14"
                    style="font-family:var(--font-body);font-size:13px;line-height:1.8;resize:vertical"></textarea>
                <p style="font-family:var(--font-mono);font-size:10px;color:var(--text-3);margin:8px 0 0">Edit freely — this is your draft to share.</p>
            </div>
            <div class="modal-footer">
                <button data-bs-dismiss="modal" class="btn btn-outline-secondary btn-sm">Close</button>
                <button onclick="navigator.clipboard.writeText(document.getElementById('__update-text').value);this.textContent='✓ Copied!';setTimeout(()=>this.innerHTML='<i class=\'bi bi-clipboard\' style=\'font-size:11px\'></i> Copy',1500)" class="wt-btn wt-btn-accent" style="padding:6px 18px">
                    <i class="bi bi-clipboard" style="font-size:11px"></i> Copy
                </button>
            </div>
        </div>
    </div>
</div>

</x-layouts.app>
