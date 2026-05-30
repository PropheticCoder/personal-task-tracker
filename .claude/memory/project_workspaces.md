---
name: project-workspaces
description: WorkTracker — workspace/company feature — BUILT. Notes on gotchas and what's left.
metadata:
  type: project
---

Workspaces separate employment, personal, and freelance contexts. **Feature is built and live.**

**Why:** User wants to switch between contexts (Work, Personal, Freelance) without mixing tasks and projects. Each workspace is a separate view. Default workspace loads on login.

**How it works:**
- `workspaces` table + `workspace_user` pivot (explicit name — NOT `user_workspace`)
- `users.default_workspace_id` FK to workspaces
- `projects.workspace_id` — only projects are scoped; tasks/activities inherit via project
- `SetWorkspace` middleware runs on all auth routes, resolves from session → user default → first available
- Nav shows workspace colour dot + name with dropdown switcher
- New users get "Personal" workspace auto-created on register

**CRITICAL gotcha:** Pivot table is named `workspace_user`. Laravel convention would generate `user_workspace`. Both `User::workspaces()` and `Workspace::users()` must pass `'workspace_user'` as the second argument to `belongsToMany()` or queries will fail with "table not found".

**What's still needed:**
- Existing projects (created before workspaces) have `workspace_id = null` — they are invisible on the dashboard. Need a way to assign them (bulk assign UI or project edit modal update).
- Project edit modal doesn't yet include a workspace selector.

[[project-worktracker-spec]]
