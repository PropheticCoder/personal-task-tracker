# WorkTracker — Project Memory Index

- [Project build spec](project_worktracker_spec.md) — Full build status, data model, routes, tech stack as of 2026-05-30 session 2
- [Workspace feature](project_workspaces.md) — BUILT. Multi-workspace support. Critical gotcha: pivot table is `workspace_user` not `user_workspace`
- [Mobile UX issues](mobile_ux_issues.md) — Known mobile gaps: bottom nav incomplete, project show no mobile header, touch targets, workspace switcher missing on mobile
- [Memory location preference](feedback_memory_location.md) — All Claude memory/thinking stays in `.claude/memory/` inside the project, not external paths
- [Frontend stack preference](feedback_frontend_stack.md) — Bootstrap 5 via CDN; no Vite/Node/npm toolchain required
