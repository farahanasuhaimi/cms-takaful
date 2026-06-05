# Dr Takaful CMS

> Private invite-only multi-tenant CRM for Malaysian takaful agents (AIA Public Takaful).
> Deployed at: `list.drtakaful.com`
> Built with: **Laravel 12 · MySQL · Blade · Alpine.js v3 · Tailwind CSS v3**

---

## What This Is

A private CRM for Hana and up to ~10 trusted friends (AIA takaful agents). Each user's data is fully isolated. Manages policyholders, leads, outreach interactions, prospecting strategy, and a two-sided marketplace for sharing AI-generated content and plan product knowledge.

---

## Modules

| Module | Description |
| --- | --- |
| **Auth** | Invite-only registration. Admin generates 7-day token links; no public signup. Session-based, Breeze. |
| **Multi-tenancy** | Per-user data isolation via Eloquent global scopes on all user-owned models. |
| **PII Encryption** | Client + lead name, phone, IC encrypted at rest (AES-256-CBC via `encrypted` cast). |
| **Dashboard** | Stats (clients, leads, commission, last outreach), renewal alerts, top plans, hot leads. |
| **Policyholders** | Full CRUD. Inline policy attach with catalog selector. Touchpoint log. Commission estimate. |
| **Leads** | Hot/warm split, stage tracking, convert to client. Inline touchpoint log. |
| **Follow-up Log** | Polymorphic touchpoints across clients and leads. Filter by channel. |
| **Reach Angles** | Prospecting strategy cards. AI content generation per angle. Tag clients. |
| **Content Library** | Pinned AI-generated content. Copy, unpin, list for sale in marketplace. |
| **Plan Catalog** | Register plan products with JSON attributes and commission rates. Toggle public sharing. |
| **Auto-renewal** | Renewal date computed from start date + frequency — never stale. |
| **Renewal Alerts** | Banner for ≤30-day renewals. Red badge for ≤7 days. |
| **Commission Tracking** | Per-plan rate (%). Estimated 1st-year commission per client. |
| **Credits System** | Users earn and spend credits. Transactions logged. Balance shown in sidebar. New users get 10 starter credits. |
| **Strategy Marketplace** | List pinned AI content for sale (credits). Buyer pays → seller earns → content copied to buyer's library. |
| **Policy Marketplace** | Share plan products for free. Star system (upvote). One-click import to own catalog. |
| **Admin Panel** | User management, active toggle, invite management, credits top-up, activity log. |
| **Mobile UI** | Collapsible sidebar with hamburger + backdrop. Tables scroll horizontally. |

---

## System Review — Strengths & Gaps

### Strengths

**1. Privacy done right.**
Client name, phone, and IC are encrypted at the database level via Laravel's `encrypted` cast. Correct baseline for any agent handling PII.

**2. Dashboard surfaces what matters.**
Renewal dates in 7 days, overdue follow-ups, hot leads by urgency, top commission clients. Actionable, not decorative.

**3. Strategy system has real depth.**
Script and Flow types, multi-step branching (YES / NO), AI generation, focus points taxonomy, marketplace monetization. A structured playbook, not a notes folder.

**4. Quotation builder is field-ready.**
Multi-person, multi-plan premium comparison usable in a face-to-face meeting. Duplicate feature. Plan catalog auto-loads attributes.

**5. Content engine is wired.**
Reach Angle → AI generates 3 variations (casual/story/factual) → pin → Content Library → copy to WhatsApp/Instagram.

**6. Focus Points taxonomy is solid.**
22 selling points across 6 groups (financial, protection, family, life stage, emotional, Islamic) grounded in real Takaful selling frameworks.

**7. Marketplace = passive income potential.**
Best strategies and angle content can be monetized by listing for credits.

---

### Content Angles — Observations & Gaps

The Reach Angles system is the right idea but has friction that limits real-world use.

**What works:**
- Angle concept (title + target segment + description) is flexible enough for any message framework
- 3-style split (casual/story/factual) provides variety across different platforms and audiences
- Pin system keeps best content accessible in the Content Library
- Angle → Lead/Client/Strategy linking tracks who you're targeting

**What's missing or misaligned:**

1. **No platform context.** An Instagram caption, WhatsApp broadcast, and Facebook post have different optimal lengths and tones. Content is generated without knowing the destination. A platform field on the Angle would let the AI tailor accordingly.

2. **Generated content is capped at 2–4 sentences.** Fine for Instagram, too short for WhatsApp broadcasts or Facebook where agents write more. Length should adapt to platform.

3. **No connection to Focus Points.** When generating content for "New Parents," the focus points `Ibu Bapa Baru`, `Tanggungjawab kepada Keluarga`, `Beri Keluarga Masa untuk Bernafas` are directly relevant — but the AI doesn't know about them. The generation prompt doesn't pull in the focus point framework.

4. **No content cadence or structure.** On-demand generation only. No concept of a posting rhythm or content calendar (e.g. Week 1 = educational, Week 2 = story, Week 3 = CTA).

5. **No hashtag or caption structure.** Content is plain prose. Most platforms benefit from hashtags or a specific caption formula. Nothing in the output is platform-ready out of the box.

6. **Angle → Client/Lead linking is passive.** You can link a client to an angle, but there's no way to record "I sent this content to this client on this date." The link exists but produces no activity trail.

---

### Weaknesses Across the System

1. **Lead → Client conversion is manual and incomplete.** `converted_at` exists on Lead but there's no flow to create a Client from a Lead automatically.
2. **Quotations are orphaned.** `prospect_name`/`prospect_phone` are plain text fields — no FK to Lead or Client. Can't answer "show all quotations for Ahmad" from the client page.
3. **No notification layer.** Overdue follow-ups and renewals exist in the dashboard but only if you log in. Nothing emails or pings you.
4. **Strategies and touchpoints don't talk to each other.** Can't record which strategy was used in a meeting. No way to know what's working vs. sitting unused.
5. **No export.** Can't export clients, leads, or policies to Excel/CSV. No PDF for quotations.
6. **Angle content not tied to activity.** Generation history floats without context — no record of what was actually sent to whom.

---

## Improvement Tasks

See [`TASKS.md`](TASKS.md) for the full prioritised task list.

---

## Roadmap (original)

- [ ] Export clients to CSV
- [ ] Search across leads (clients only currently)
- [ ] Birthday reminders
- [ ] Dark mode toggle

---

## Stack

| Layer | Technology |
| --- | --- |
| Backend | Laravel 12 (PHP 8.2+) |
| Database | MySQL 8 |
| Templating | Laravel Blade |
| UI interactivity | Alpine.js v3 (CDN) |
| CSS | Tailwind CSS v3 — matcha × strawberry theme |
| Auth | Laravel Breeze (session, invite-only) |
| Build tool | Vite |

---

## Database

```
users                    — name, email, password, credits, is_admin, is_active
invitations              — token, email, invited_by, used_at, expires_at
credit_transactions      — user_id, amount, type, description
activity_logs            — user_id, action, subject_type, subject_id, description

clients                  — user_id, name*, phone*, ic_no*, email, notes
policies                 — client_id, plan_product_id, plan_type, coverage, start_date, frequency
leads                    — user_id, name*, phone*, source, temperature, stage, converted_at
touchpoints              — polymorphic (client OR lead), channel, topic, notes, next_action_date
reach_angles             — user_id, title, description, target_segment, status
angle_client             — pivot: reach_angle_id, client_id
angle_contents           — user_id, angle_id, batch, style, content, is_pinned, model
plan_products            — user_id, plan_type, name, commission_first_year, is_shared, shared_note
marketplace_policy_stars — user_id, plan_product_id
marketplace_listings     — seller_user_id, angle_content_id, title, price_credits, status
marketplace_purchases    — buyer_user_id, listing_id, credits_paid, imported_content_id
settings                 — user_id, key, value

* encrypted at rest
```

---

## Architecture Notes

- **Multi-tenancy via global scopes** — `where('user_id', auth()->id())` applied automatically on all user-owned models. Marketplace queries use `withoutGlobalScopes()` to browse across users.
- **Encrypted search** — SQL LIKE doesn't work on encrypted columns. Client/lead search uses collection filter after `->get()` (fine at ≤100 records/user).
- **Credits atomicity** — `CreditService::spend()` / `award()` wrap increment + transaction log in `DB::transaction()`.
- **Marketplace purchase** — buyer's credit deducted, seller credited, content copied into buyer's "Marketplace Imports" angle (auto-created via `firstOrCreate`).

---

## Local Setup

```bash
git clone https://github.com/farahanasuhaimi/cms-takaful.git
cd cms-takaful

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Edit `.env` — set DB credentials and an `OPENAI_API_KEY` for AI content generation.

```bash
php artisan migrate
php artisan serve
```

Create the first admin user via tinker:

```bash
php artisan tinker
>>> \App\Models\User::create(['name' => 'Admin', 'email' => 'you@example.com', 'password' => bcrypt('secret'), 'is_admin' => true, 'credits' => 10]);
```

Then use the admin panel to invite other users.

---

## Deployment (Hostinger)

```bash
composer install --no-dev --optimize-autoloader
npm run build
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
chmod -R 755 storage bootstrap/cache
```

---

_Built by Hana · AIA Public Takaful · Dr Takaful brand_
