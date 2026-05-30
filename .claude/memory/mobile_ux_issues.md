---
name: mobile-ux-issues
description: WorkTracker — known mobile UX gaps to fix, identified 2026-05-30
metadata:
  type: project
---

Mobile UX pass is in progress. Known issues:

**Bottom tab bar (`wt-mob-nav` in components/layouts/app.blade.php:650)**
- Only has 3 tabs: Dashboard, Clients, Tools
- Missing: Handoffs, Update, Timesheets
- Tools is "coming soon" — should be replaced or swapped out

**Top nav on mobile**
- Nav links are `d-none d-lg-flex` — invisible on mobile
- Workspace switcher in top nav is also invisible on mobile
- No mobile equivalent for workspace switching

**Project show (`resources/views/projects/show.blade.php`)**
- Sidebar (`d-none d-lg-flex`) is completely hidden on mobile — project name, client, contacts, Edit button all invisible
- Main content shown but no mobile header for project name/status

**Task show (`resources/views/tasks/show.blade.php`)**
- Two-column layout (`col-lg-8` / `col-lg-4`) stacks correctly via Bootstrap
- But right-side details panel falls below — not ideal for quick status check on mobile

**Handoffs (`resources/views/handoffs.blade.php`)**
- `col-md-6` two-column layout collapses to single column on mobile — acceptable
- But no mobile-specific layout optimisation

**Update (`resources/views/update.blade.php`)**
- Has a mobile section already but it's incomplete/stub-level

**General**
- Touch targets may be too small (dropdown items, small buttons)
- `container-lg` adds side padding but pages can still feel cramped on small screens
- Modals are now 440-560px wide — on screens <440px they may overflow

**Why:** User requested mobile UX focus on 2026-05-30. Features are complete; polish needed.

**How to apply:** When working on mobile, prioritise: bottom nav, project show mobile header, task show quick-actions, then general spacing/touch targets.
