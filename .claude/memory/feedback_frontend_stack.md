---
name: feedback-frontend-stack
description: User uses Bootstrap as their frontend framework. No Vite, no Node.js build step — all assets loaded via CDN. Tailwind is acceptable but avoid Vite/npm toolchains.
metadata:
  type: feedback
---

Use Bootstrap 5 via CDN for all UI. Do not require npm/Node.js/Vite at runtime.

**Why:** User is not a JS developer and does not use build frameworks. Their existing frontend knowledge is Bootstrap.

**How to apply:**
- In all Blade layouts: load Bootstrap CSS and JS via CDN (`<link>` + `<script>`), not `@vite(...)`
- Livewire self-serves its JS via `@livewireScripts` — that is fine to keep
- Use Bootstrap utility classes and components throughout all views
- Do not write npm scripts or reference `package.json` tooling in instructions
- If Tailwind is truly needed later, use the Tailwind browser CDN (no build step), but default to Bootstrap
