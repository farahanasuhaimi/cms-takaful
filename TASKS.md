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

- [x] **Decision: rebuild angle content generation** — wired back up properly via `AngleContentController` + `angle-contents.generate`/`angle-contents.pin` routes *(done 2026-06-06)*
- [x] **Rename "Content Library" nav link to "Strategy Library"** — nav now correctly labeled, routes to `strategies.index` *(done 2026-06-06)*
- [ ] **Add content output to Angles** — angles currently produce nothing usable; link an angle to a strategy or add a simple text area for "what to say" so the angle has a deployable output
- [ ] **Add activity trail to Angle → Client/Lead links** — `angle_client` pivot has `reached_at` but no notes field; not verified whether this is surfaced anywhere in the UI

---

## Priority 3 — Remove Daily Friction

- [x] **Quotation PDF export** — Print / Save PDF via browser (A4 landscape, branded header, watermark, disclaimer) *(done 2026-06-06)*
- [x] **Renewal → auto-create touchpoint** — Dashboard renewal card: "+ Follow-up" creates a touchpoint for the client with renewal date pre-filled *(done 2026-06-06)*
- [x] **Focus Points → Lead tagging** — "Tags" button on each lead row; tap focus points to toggle; chips shown below lead name *(done 2026-06-06)*
- [x] **Policy renewal "mark as renewed"** — Dashboard renewal card: "Renewed ✓" advances start_date by one period, drops from the renewal alert *(done 2026-06-06)*

---

## Priority 4 — Visibility & Output

- [ ] **Strategy effectiveness tracking** — When a strategy is used in a touchpoint (Priority 1), surface a count of "times used" and "conversions linked" on the Strategy show page
- [ ] **Export clients/leads to CSV** — Basic data export for backup and external reporting
- [ ] **Email/notification for overdue follow-ups** — Daily digest email or webhook for overdue touchpoints so you don't have to log in to find them
- [ ] **Content calendar view** — A simple calendar or weekly planner view that maps pinned angle content to planned posting dates

---

## Priority 5 — Daily Posts (AI Content Pipeline)

- [x] **Daily content posts module** — new post-generation feature with DeepSeek integration *(done 2026-06-28)*
- [x] **5, then 2, image prompt options per post** — neutral + emotional framing *(done 2026-06-28)*
- [x] **Link Reach Angles → Daily Posts** — angle-aware generation *(done 2026-06-29)*
- [x] **Link Product Catalog → Daily Posts** — one-way LLM context feed; a plan product can feed its data into post generation *(done 2026-06-29)*
- [ ] **Test product-linked generation end-to-end** on `list.drtakaful.com` — link a product to a draft post, hit Generate, confirm output quality
- [ ] **Fill in Idaman Health Flex-i plan attributes** in the catalog — currently sparse

---

## Priority 6 — Quotation Improvements

- [x] **Quotation → Social Card generator** — 1080×1080 matcha/strawberry-themed downloadable PNG (contact bar, premium table, highlights, price tag, CTA banner), client-side html2canvas export, contact details saved per-user *(done 2026-07-05)*
- [x] **Dynamic per-plan attributes linked to Plan Catalog** — loading a catalog plan still auto-fills known fixed fields (coverage, room & board, waiver, etc.), but any other catalog attribute (Deduktibel, Health Wallet, Panel Bonus, AIA Vitality, etc.) now flows into an editable "Additional Details" list instead of being dropped; shows in the comparison table and feeds Social Card highlights *(done 2026-07-05)*
- [ ] **Type-specific known-field mapping** — current fixed fields (umur_matang, pampasan_matang, room_board) read like a Hibah/Medical blend; revisit once real Hibah/PA catalog entries are tested through the new Additional Details flow, in case those types want their own first-class fields instead of falling into "extra"

---

## Security

- [x] **Fix 4 HIGH vulnerabilities** — XSS, SSRF, TLS bypass, deactivated-session reuse *(done 2026-06-24)*
- [x] **Fix MEDIUM vulnerabilities** — IDOR, mass assignment, marketplace scope leaks *(done 2026-06-24)*

---

## Original Roadmap Items

- [ ] Search across leads (clients only currently)
- [ ] Birthday reminders
- [ ] Dark mode toggle
