---
name: feedback-memory-location
description: User wants all Claude thinking and project memory stored inside the project at .claude/memory/, not in external memory paths.
metadata:
  type: feedback
---

Always store project memory inside the project at `.claude/memory/`, not at the external `~/.claude/projects/...` path.

**Why:** User wants everything — Claude's thinking, notes, context — to live in the project repo so it's portable and co-located with the code.

**How to apply:** For this project, read and write memory files to `C:\xampp\htdocs\Worship Dev\personal-task-tracker\.claude\memory\`. Keep `MEMORY.md` updated as the index. The external path should only hold a redirect pointer.
