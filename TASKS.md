# cms-takaful — Improvement Tasks

Derived from system review (2026-06-05). Ordered by impact.

---

## Priority 1 — Close the Sales Loop

These close gaps between modules that should be connected but aren't.

- [ ] **Convert Lead → Client flow** — Add a "Convert to Client" button on the Lead page that creates a Client record from Lead data (name, phone), marks `converted_at`, and optionally creates a first policy skeleton
- [ ] **Link Quotation → Lead/Client** — Add a `lead_id` or `client_id` FK to the Quotations table so the quote is attached to the person, visible from the client/lead page
- [ ] **Touchpoint strategy tagging** — When logging a touchpoint, allow optionally linking which Strategy or Reach Angle was used (dropdown, optional field — data already exists, just needs wiring)

---

## Priority 2 — Content Angles Overhaul

These address the specific gaps identified in the Reach Angles / Content generation flow.

- [ ] **Add platform field to Reach Angle** — WhatsApp / Instagram / Facebook / General — stored on the angle, passed to the AI generation prompt
- [ ] **Adapt content length to platform** — Update `AngleContentService` system prompt to adjust output length per platform (WhatsApp = longer, Instagram = shorter, etc.)
- [ ] **Inject Focus Points into angle content generation** — When generating, pass the linked focus points (or a subset) into the AI prompt so generated content reflects the relevant selling angles
- [ ] **Log content usage against a Lead/Client** — When you send angle content to someone, allow marking it as "sent to [Lead/Client]" from the Content Library, creating a lightweight touchpoint record
- [ ] **Add hashtag / caption structure option** — Option to include platform-appropriate hashtags in generated content (toggle per generation or per angle)

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

## Content Angles — Specific AI Prompt Improvements

These are smaller, lower-risk changes to the existing generation prompts.

- [ ] **`AngleContentService`**: add platform to system prompt context
- [ ] **`AngleContentService`**: pass relevant focus point titles into the user prompt when the angle has linked focus points
- [ ] **`AngleContentService`**: increase `max_tokens` conditionally for WhatsApp/Facebook platforms
- [ ] **`AngleContentService`**: add optional hashtag block to the JSON response schema (`"hashtags": ["#takaful", ...]`)

---

## Original Roadmap Items

- [ ] Search across leads (clients only currently)
- [ ] Birthday reminders
- [ ] Dark mode toggle
