# Project Plan - Dr Takaful CMS
Created: 2026-04-15
Source: CLAUDE.md + README.md

## Instructions
- Auto-commit code after each completed todo item
- Update this file every 5 completed items (checkpoint save)
- Commit this file alongside code changes to keep project history in sync

## Architecture

```
list.drtakaful.com  (subdomain, Hostinger)
│
├── Auth              Laravel Breeze — login only, matcha theme
├── Dashboard         Stats (5 cards incl. commission), renewal alerts, top commission + top plan conversion
├── Policyholders     Clients CRUD + inline policy attach + touchpoint log + commission estimate card
├── Leads             Hot/warm split, stage tracking, convert to client, inline touchpoint log
├── Follow-up Log     Polymorphic touchpoints (clients + leads), filter by channel
├── Reach Angles      Prospecting strategy cards, tag clients
├── Plan Catalog      Register plan products, dynamic JSON attributes, 1st year commission rate (%)
├── Auto-renewal      Renewal date computed from start_date + frequency (nextRenewalDate())
├── Renewal Alert     Dashboard banner — policies renewing within 30 days
├── Commission        estimatedCommissionFirstYear() on Policy — rate × annualised premium
└── Mobile UI         Hamburger sidebar, backdrop overlay, overflow-x-auto tables
```

Stack: Laravel 12 · PHP 8.2+ · MySQL 8 · Blade · Alpine.js v3 (CDN) · Tailwind CSS v3 (matcha x strawberry) · Vite · Laravel Breeze

Deployed to: Hostinger shared hosting — no Docker, no Redis, no queue workers.

## Implementation Plan

### Phase 1: Foundation
- [x] Laravel 12 project scaffolded (Blade stack)
- [x] .env configured for MySQL
- [x] Tailwind config with matcha + strawberry tokens
- [x] @tailwindcss/forms installed
- [x] Laravel Breeze installed, registration routes removed
- [x] Login page styled with matcha theme
- [x] AdminUserSeeder created (admin@drtakaful.com)
- [x] Migration: clients
- [x] Migration: policies
- [x] Migration: leads
- [x] Migration: touchpoints (polymorphic)
- [x] Migration: reach_angles
- [x] Migration: angle_client (pivot)
- [x] php artisan migrate run

### Phase 2: Models and Routes
- [x] Model: Client (policies, touchpoints morphMany, reachAngles belongsToMany)
- [x] Model: Policy
- [x] Model: Lead (touchpoints morphMany, isConverted())
- [x] Model: Touchpoint (morphTo)
- [x] Model: ReachAngle (clients belongsToMany)
- [x] Routes: all resourceful + nested policy routes + convert + touchpoint store + angle attach
- [x] Controller stubs: DashboardController, ClientController, LeadController, TouchpointController, ReachAngleController

### Phase 3: Views and Controller Logic
- [x] Layout: layouts/app.blade.php (sidebar + topbar shell, Alpine.js CDN)
- [x] Dashboard: DashboardController@index + dashboard/index.blade.php
- [x] Clients: full CRUD views + ClientController (index, show, create, edit, storePolicy, destroyPolicy)
- [x] Leads: index + create + edit views + LeadController (index, store, update, destroy, convert)
- [x] Touchpoints: index view + TouchpointController (index, storeForClient, storeForLead)
- [x] Reach Angles: index + create views + ReachAngleController (index, store, update, destroy, attachClient)
- [x] Alpine.js interactions: modals, inline forms, delete confirm, success toast

### Phase 4: Extended Features
- [x] Plan Catalog — plan product registry with dynamic JSON attributes (under Settings)
- [x] Auto-renewal — renewal date computed from start date + frequency (never stale)

### Phase 5: Deployment (Hostinger)
- [ ] Provision subdomain list.drtakaful.com on Hostinger
- [ ] Upload project to subdomain directory (/public_html/list/ or equivalent)
- [ ] Set document root to /public
- [ ] Configure .env on server (DB credentials, APP_ENV=production, APP_KEY)
- [ ] Run: composer install --no-dev --optimize-autoloader
- [ ] Run: npm run build
- [ ] Run: php artisan migrate --force
- [ ] Run: php artisan db:seed --class=AdminUserSeeder
- [ ] Run: php artisan config:cache && route:cache && view:cache
- [ ] Set permissions: chmod -R 755 storage bootstrap/cache
- [ ] Test login at list.drtakaful.com/login
- [ ] Change default password via tinker

### Phase 6: Roadmap (Post-Deploy)
- [x] Renewal alert on dashboard — highlight policies renewing in next 30 days
- [ ] Export clients to CSV
- [ ] Birthday reminder (requires DOB field on clients)
- [x] Mobile-optimised views
- [ ] Dark mode toggle
- [ ] Search across leads (currently search only covers clients)

### Phase 7: Commission Tracking
- [x] commission_first_year (%) field on plan_products — migration + model + controller + forms + index display
- [x] Policy::estimatedCommissionFirstYear() — annualised premium × rate, respects monthly/yearly frequency
- [x] Commission estimate card on client show page — per-policy breakdown + total
- [x] Dashboard stat card — Est. Commission (Yr 1) total
- [x] Dashboard section — Top Commission Revenue (top 5 clients by commission)
- [x] Dashboard section — Top Plans by Conversion (top 5 plan products by policy count)

## Progress Log

2026-04-14 - Full build session completed: all 8 modules built and pushed to farahanasuhaimi/cms-takaful
2026-04-15 - Project cloned to K:\cms-takaful. Plan file created. Next: browser test then Hostinger deploy.
2026-04-15 - Deployed to Hostinger (debugging pending). Built renewal alert: policies renewing within 30 days shown on dashboard with days-left badge (red <= 7 days, amber otherwise).
2026-04-15 - Mobile optimisation: collapsible sidebar with hamburger + backdrop overlay (Alpine.js), topbar search hidden on mobile, overflow-x-auto on clients and leads tables.
2026-04-15 - Commission tracking: commission_first_year (%) on plan_products, estimatedCommissionFirstYear() on Policy model, commission card on client show, dashboard stat + top commission + top conversion sections.
2026-04-15 - Updated README.md, CLAUDE.md, and project-plan.md to reflect all post-launch additions.
