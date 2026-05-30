---
name: project-worktracker-spec
description: WorkTracker — personal Laravel task tracker for a developer working across client projects. Full build status and architecture as of 2026-05-30.
metadata:
  type: project
---

WorkTracker is for a developer who:
- Receives projects from clients
- Each project has contacts (people they talk to)
- Works on tasks within those projects
- Goes to meetings that produce/update tasks
- Needs a morning per-project status update

---

## Build status (as of 2026-05-30)

**All migrations ran. All core models exist. Core screens built and wired.**

### What is built and working (as of 2026-05-30 session 2)
- Auth (login, register, logout)
- Handoffs board (`/handoffs`) — real data, workspace-scoped, grouped by project, resolve actions, add handoff modal
- Daily Update (`/update`) — real data from DB, two tabs (Team Update editable standup + What I've Done activity log), time window selector (yesterday/today/week)
- Deletions — tasks, activities (notes only), projects, clients, contacts all have working DELETE routes and UI
- Tools — coming soon in production (`APP_ENV=production`), live in dev
- Workspaces — multi-workspace support, session-based switching, workspace middleware, auto-create on register
- Dashboard — project strip + task columns (Working On / Waiting On / They Need From Me), scoped to active workspace
- Projects — create, show (tasks grouped by status), edit modal, colour picker
- Tasks — create (from project quick-add or dashboard modal), show (activity timeline, time logged, edit modal), status change buttons, note + hours logging
- Dependencies — waiting_on / i_owe, create via dashboard inline forms, resolve via "Got it" button
- Meetings — create, show (notes + action items), edit, add action item tasks
- Clients — list, create, show (contacts + projects), edit client name, add/edit contacts (name, role, email, phone)
- People — name, role, email, phone
- Timesheets — per-task auto-created on task creation; combined timesheet creation page; show page with per-project breakdown; submit + print
- Profile — edit name/email, change password

### What is NOT built yet
- Tools backend (stub views exist — CSV→SQL, QR, PDF, Word→HTML — marked "coming soon" in production)
- PWA / offline support
- Mobile UX pass (in progress — bottom nav incomplete, project show sidebar hidden on mobile, most pages not mobile-optimised)

---

## Core mental model — "Who has the ball?"

Every task at any moment is in one of these states:
- **working_on** — "I'm doing this right now"
- **waiting_on** — "I've done my part, I need something from [person] to continue"
- **they_need_it** — "I'm working on this AND someone is depending on my output" (dual state: active + pressure)
- **done**
- **todo** — queued, not started

**CRITICAL CONSTRAINT — dependencies are always task-scoped:**
Both `waiting_on` and `i_owe` (depending_on_me) dependencies ONLY exist in the context of a task the developer is actively working on. No standalone/floating dependencies. You always create/select the task first, then attach the dep.

- **Waiting On column** = tasks with `waiting_on` status + a `waiting_on` dep record
- **They Need From Me column** = tasks with `working_on` status + an `i_owe` dep record

---

## Data model (all tables exist in DB)

| Table | Key fields |
|---|---|
| workspaces | id, name, color, description |
| workspace_user | workspace_id, user_id (pivot — explicit name, NOT user_workspace) |
| users | id, name, email, password, default_workspace_id |
| clients | id, name |
| people | id, name, role, email, phone, client_id |
| projects | id, workspace_id, client_id, name, status, description, color |
| meetings | id, project_id, title, held_at, notes |
| meeting_person | meeting_id, person_id (pivot) |
| tasks | id, project_id, meeting_id, title, description, status, priority, needed_by, source |
| dependencies | id, direction, person_id, task_id, project_id, description, status, needed_by, resolved_at |
| activities | id, task_id, project_id, dependency_id, meeting_id, timesheet_id, type, body, hours, old_status, new_status, created_at |
| timesheets | id, task_id (nullable, unique — for auto task timesheets), type (task\|combined), title, period_start, period_end, client_id, notes, status |
| tool_runs | id, task_id, tool_key, parameters, input_path, input_text, output_paths, status, error |

**IMPORTANT:** Pivot table is `workspace_user` (not `user_workspace`). Both `User::workspaces()` and `Workspace::users()` must specify `'workspace_user'` as the second argument to `belongsToMany`.

---

## Time tracking model
- `activities.hours` (nullable decimal) — every activity can carry hours
- When hours are logged on a task note/status-change, they auto-assign to the task's auto-timesheet
- Every task gets a `type=task` timesheet auto-created when the task is created
- Combined timesheets (`type=combined`) are manually assembled from task timesheets + loose activities
- `/timesheets/create` shows two sections: task timesheets (grouped by project) + loose activities

---

## Workspace scoping
- All projects have `workspace_id`; tasks/activities inherit scope via project
- `SetWorkspace` middleware runs on all auth'd routes, resolves workspace from session → user default → first available → redirects to `/workspaces/create` if none
- `app('current_workspace')` and `View::share('currentWorkspace', ...)` available in all controllers/views
- Nav shows workspace switcher dropdown with colour dot

---

## Key routes
```
GET  /                           Dashboard (workspace-scoped)
GET  /projects/create            New project
POST /projects                   Create project (auto-sets workspace_id)
GET  /projects/{project}         Project detail
PATCH /projects/{project}        Edit project

POST /tasks                      Create task (auto-creates timesheet)
GET  /tasks/{task}               Task detail + activity + time logged
PATCH /tasks/{task}              Update task
POST /tasks/{task}/notes         Add note (with optional hours)
POST /tasks/{task}/resolve-blocker

POST /dependencies               Create dep (waiting_on / i_owe)
POST /dependencies/{dep}/resolve

GET  /clients                    Client list
POST /clients                    Create client
GET  /clients/{client}           Client detail
PATCH /clients/{client}          Edit client
POST /people                     Create person (with email + phone)

GET  /meetings/{meeting}         Meeting detail
PATCH /meetings/{meeting}        Edit meeting
POST /meetings/{meeting}/tasks   Add action item

GET  /timesheets                 Timesheet list
GET  /timesheets/create          Combined timesheet creator
POST /timesheets                 Store combined timesheet
GET  /timesheets/{timesheet}     Timesheet detail
POST /timesheets/{timesheet}/submit

GET  /profile                    Profile page
PATCH /profile                   Update name/email
PATCH /profile/password          Change password

GET  /workspaces/create          Create workspace
POST /workspaces                 Store workspace
POST /workspaces/{ws}/switch     Switch active workspace
POST /workspaces/{ws}/default    Set default workspace
PATCH /workspaces/{ws}           Update workspace
```

---

## Tech stack
- Laravel 12, MySQL, Bootstrap 5 CDN, Bootstrap Icons CDN
- Google Fonts: Bricolage Grotesque (display), Plus Jakarta Sans (body), Azeret Mono (mono)
- Dark theme via CSS variables in `resources/views/components/layouts/app.blade.php`
- No Vite, no Node/npm required for app runtime
- Livewire installed but not yet used
