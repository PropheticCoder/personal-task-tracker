# WorkTracker — CLAUDE.md

Personal task-tracking system built in Laravel for a developer who works across multiple client projects.
Single user, self-hosted, PWA. The core is project-centric task tracking with automatic activity recording
and a self-drafting daily update. Developer tools (CSV→SQL, QR, PDF, etc.) are supporting utilities.

## Build status

Phase 1 complete (Laravel 12 + MySQL + Bootstrap 5 CDN installed, all packages).
Phase 2 in progress (screen stubs — being rebuilt after data model revision).
Phase 3+ not started.

## Tech stack

- **Framework:** Laravel 12
- **Database:** MySQL (`DB_CONNECTION=mysql`, database: `worktracker`). No DB-specific assumptions — swappable.
- **Frontend:** Bootstrap 5 via CDN. No Vite, no Node.js required. Livewire self-serves its JS.
- **File storage:** Laravel `Storage`, `local` disk, under `storage/app/devtools/`
- **Async:** Laravel queues for batch/large tool jobs. All task/project operations are synchronous.
- **UI:** Livewire for interactive components. Bootstrap 5 + Bootstrap Icons via CDN.
- **PWA:** manifest + service worker (Phase 4). Requires HTTPS in production.

## Data model

### Client
```
id, name (string), timestamps
```
A company or organisation the user works for.

### Person (contact at a client, or any person in a dependency)
```
id, name (string), role (string?), client_id (fk to Client, nullable), timestamps
```
Created on-the-fly. When `client_id` is set, this person is a named contact at that client.
Persons without a `client_id` are internal contacts or collaborators.

### Project
```
id, name (string), client_id (fk to Client, nullable),
status (active|paused|completed|archived), description (text?),
color (string? — hex, for visual differentiation on dashboard), timestamps
```
The primary grouping. Tasks, dependencies, and activity all belong to a project.
A project with no `client_id` is an internal project.

### Meeting
```
id, project_id (fk to Project),
title (string — e.g. "Sprint Planning Jun 3"),
held_at (datetime),
notes (text? — free-text meeting notes),
timestamps
```
- Meetings belong to a project.
- Attendees are linked via `meeting_person` pivot (meeting_id, person_id).
- Action items captured during a meeting become Tasks with `meeting_id` set.
- The meeting detail view shows notes + a live action-item list (tasks with their current statuses).
- A "Meetings" tab appears on the project detail page.

### Task
```
id, project_id (fk to Project),
meeting_id (fk to Meeting, nullable — set when task was created from a meeting),
title (string), description (text?),
status (todo|working_on|waiting_on|done|archived),
priority (low|med|high|null), needed_by (date?), source (string?), timestamps
```
- `project_id` is **required** — every task belongs to a project. No inbox.
- `meeting_id` nullable: set when the task originates from a meeting action item.

**Status model — "Who has the ball?"**
- `todo` — queued, not started
- `working_on` — developer is actively doing this (ball is in their court)
- `waiting_on` — developer has done their part; waiting on a specific person for something (always paired with a `waiting_on` Dependency record)
- `done`

A task in `working_on` status may ALSO have an `i_owe` Dependency — meaning someone is waiting on the developer's output while they work. This is the "pressure" state: working + someone depending on you.

The dashboard organises tasks into:
1. **In My Court** — `working_on` tasks (+ flag if an `i_owe` dep exists on this task)
2. **Waiting On** — `waiting_on` tasks, grouped by who the developer is waiting on
3. **Next** — `todo` tasks, ordered by priority + needed_by

### Dependency
```
id, direction (waiting_on|i_owe),
person_id (fk to Person),
task_id (fk to Task, nullable),
project_id (fk to Project, nullable — project-level dep when task_id is null),
description (string), status (open|resolved),
needed_by (date?), resolved_at (datetime?), timestamps
```
- `waiting_on` dep on a task → task auto-sets to `blocked`
- `waiting_on` dep on a project (no task) → visible on project card as a project-level blocker
- `i_owe` = commitment to deliver; feeds "owed by me" digest section

### Activity (append-only spine)
```
id, task_id (fk?), project_id (fk?), dependency_id (fk?), meeting_id (fk?),
type (note|status_change|dependency_opened|dependency_resolved|task_added|meeting_held),
body (text?), old_status (string?), new_status (string?), created_at
```
Every meaningful change writes one row automatically. User manually writes notes only.
This feeds recall views and the daily digest.

### ToolRun (supporting)
```
id, task_id (fk? — null for standalone), tool_key (string), parameters (json),
input_path (string?), input_text (text?), output_paths (json),
status (pending|processing|completed|failed), error (text?), timestamps
```

---

## Dashboard design (project-centric)

### Desktop
- **Left sidebar:** Quick-add (captures to inbox if no project selected), global inbox count badge,
  global handoffs summary (total open waiting_on / i_owe counts).
- **Main area:** Grid of Project cards. Each card shows:
  - Project name + Client name chip
  - Status badge (active/paused)
  - **NOW** section: tasks currently `in_progress`
  - **BLOCKED** section: open `waiting_on` dependencies (task-level or project-level)
  - **NEXT** section: highest-priority `todo` task
  - Footer: counts (X todo, X blocked) + last activity time + "View project →" link

### Mobile
- Sticky quick-add bar at top
- Project list (cards, compact) — tap card → project detail
- Bottom tab bar: Projects | Inbox | Handoffs | Update

### Project detail page (`GET /projects/{project}`)
Full view of one project:
- Header: name, client, status, description
- **Tasks tab:** all tasks for this project, grouped by status
- **Handoffs tab:** all open dependencies (waiting_on + i_owe) for this project
- **Timeline tab:** full Activity stream for this project
- Quick-add pre-filled with this project

---

## Key services

### DailyUpdateService
Generates draft for a time window. Sections:
- Completed, In Progress (with notes as bullet text), Blocked/Waiting, Owed by Me, Up Next
- Grouped by project for readability

### ToolRunner + ToolModule (see §Tools below)

---

## Routes

```
# Dashboard & projects
GET  /                          Dashboard — project cards grid
GET  /projects/create           New project form
POST /projects                  Create project
GET  /projects/{project}        Project detail (tasks + handoffs + timeline)
PATCH /projects/{project}       Edit project

# Tasks
POST /tasks                     Quick-add (project_id optional → inbox if omitted)
POST /api/tasks/sync            Offline queue flush (PWA)
GET  /tasks/{task}              Task detail + timeline
PATCH /tasks/{task}             Edit / status change
POST /tasks/{task}/notes        Add progress note

# Clients & People
GET  /clients                   Client list
POST /clients                   Create client
GET  /clients/{client}          Client detail (projects + contacts)
POST /people                    Create person (usually inline from dep form)

# Handoffs (global view)
GET  /handoffs                  Global handoffs board (all projects)
POST /dependencies              Open a dependency
POST /dependencies/{dep}/resolve

# Daily update
GET  /update                    Generate + show editable draft

# Tools
GET  /tools                     List all tools
GET  /tools/{key}               Run form
POST /tools/{key}/run           Execute (optional ?task_id=)
GET  /runs/{run}/artifacts/{i}  Download stored output (NEVER regenerates)
POST /runs/{run}/clone          Pre-fill new run from params
```

---

## Supporting tools (packages)

| Tool              | Package                    | Notes                                      |
|-------------------|----------------------------|--------------------------------------------|
| CSV → SQL         | `league/csv`               | Type inference + SQL dialect grammar class |
| Text → QR         | `endroid/qr-code`          |                                            |
| CSV → QR batch    | `endroid/qr-code`          | Queued; zip output                         |
| PDF generate      | `barryvdh/laravel-dompdf`  |                                            |
| PDF read          | `smalot/pdfparser`         | Text extraction                            |
| Word → clean HTML | `phpoffice/phpword`        | Map Word styles → semantic HTML            |
| Utilities bucket  | (built-in)                 | JSON⇆YAML, base64, UUID, hash, etc.       |

---

## Build order

1. Migrations + models: Client, Person (with client_id), Project, Task (with project_id), Meeting (with pivot), Activity
2. Dashboard (project cards) + quick-add (project required — no inbox)
3. Status changes auto-write Activity (TaskObserver)
4. Project detail page (Tasks + Meetings tabs, tasks grouped by status)
5. Meeting detail: notes + action items → create tasks from meeting
6. Dependency model + Handoffs board + auto-blocking logic (DependencyObserver)
7. DailyUpdateService + /update draft
7. PWA shell: manifest + service worker + device-adaptive surfaces
8. Offline quick-add: IndexedDB + POST /api/tasks/sync
9. ToolRunner + ToolModule contract + Text→QR end-to-end
10. Remaining tools; queue for batch/large jobs
11. Clients page, People management, utilities bucket

Steps 1–6 solve the real problem. Steps 7–8 make it a daily habit. 9–11 are supporting convenience.

---

## Non-goals

- No live DB connections as a tool data source
- No regenerate-on-demand — stored artifacts are source of truth
- No OCR for scanned PDFs
- No full offline mode (only quick-add offline)
- No multi-user / shared workspace
