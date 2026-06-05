# cms-takaful — Improvement Tasks

Derived from system review (2026-06-05). Ordered by impact.

---

## Priority 1 — Close the Sales Loop

These close gaps between modules that should be connected but aren't.

- [x] **Convert Lead → Client flow** — Inline "Convert" button on Leads page; creates Client from lead data, migrates all touchpoints, marks `converted_at`, shows "Converted Lead" badge on Client *(done 2026-06-05)*
- [x] **Link Quotation → Lead/Client** — Add a `lead_id` or `client_id` FK to the Quotations table so the quote is attached to the person, visible from the client/lead page *(done 2026-06-05)*
- [x] **Touchpoint strategy tagging** — When logging a touchpoint, allow optionally linking which Strategy or Reach Angle was used (dropdown, optional field — data already exists, just needs wiring) *(done 2026-06-05)*

---

## Priority 2 — Content Angles Overhaul

The AI content generation (casual/story/factual) is no longer active — `AngleContentService` has no route or controller. The angles module needs a decision: remove the dead code or rebuild the feature properly.

- [ ] **Decision: remove or rebuild angle content generation** — either strip `AngleContentService`, `AngleContent` model, `angle_contents` table, and `angles/library.blade.php`, OR wire them back up with a proper `AngleContentController` and routes
- [ ] **Rename "Content Library" nav link to "Strategy Library"** — current label is misleading; it routes to `strategies.index`
- [ ] **Add content output to Angles** — angles currently produce nothing usable; link an angle to a strategy or add a simple text area for "what to say" so the angle has a deployable output
- [ ] **Add activity trail to Angle → Client/Lead links** — record when the angle was used with a specific person (date + notes), not just that a link exists

---

## Priority 3 — Remove Daily Friction

- [ ] **Quotation PDF export** — Branded, clean, shareable PDF of the comparison table for the prospect
- [ ] **Renewal → auto-create touchpoint** — From the dashboard renewal card, one click creates a follow-up touchpoint with the right date pre-filled
- [ ] **Focus Points → Lead tagging** — On the Lead page, allow tagging which focus points resonated during a conversation (builds a picture of what sells to whom)
- [ ] **Policy renewal "mark as renewed"** — From the renewal alert, mark a policy as renewed without navigating to the policy edit page

---

## Priority 4 — Visibility & Output

- [ ] **Strategy effectiveness tracking** — When a strategy is used in a touchpoint (Priority 1), surface a count of "times used" and "conversions linked" on the Strategy show page
- [ ] **Export clients/leads to CSV** — Basic data export for backup and external reporting
- [ ] **Email/notification for overdue follow-ups** — Daily digest email or webhook for overdue touchpoints so you don't have to log in to find them
- [ ] **Content calendar view** — A simple calendar or weekly planner view that maps pinned angle content to planned posting dates

---

---

## Original Roadmap Items

- [ ] Search across leads (clients only currently)
- [ ] Birthday reminders
- [ ] Dark mode toggle
