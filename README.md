# Dr Takaful CMS

> Private client management system for a Malaysian takaful agent (AIA Public Takaful).
> Deployed at: `list.drtakaful.com`
> Built with: **Laravel 12 · MySQL · Blade · Alpine.js v3 · Tailwind CSS**

---

## What This Is

A single-user CRM dashboard for Hana, a takaful consultant running the **Dr Takaful** brand. Manages policyholders, leads, outreach interactions, and prospecting strategy — all in one place.

---

## Modules

| Module              | Status  | Description                                                                                                                                                             |
| ------------------- | ------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Auth                | ✅ Done | Login only (no registration). Breeze, matcha-themed.                                                                                                                    |
| Dashboard           | ✅ Done | Stats (clients, leads, commission, last outreach), renewal alerts, top commission revenue, top plan conversion, recent clients, hot leads, follow-up log, reach angles. |
| Policyholders       | ✅ Done | Full CRUD. Inline policy attach with catalog selector. Touchpoint log. 1st year commission estimate card per client.                                                    |
| Leads               | ✅ Done | Hot/warm split, stage tracking, convert to client. Inline touchpoint log.                                                                                               |
| Follow-up Log       | ✅ Done | Polymorphic touchpoints across clients and leads. Filter by channel.                                                                                                    |
| Reach Angles        | ✅ Done | Prospecting strategy cards. Tag clients to angles.                                                                                                                      |
| Plan Catalog        | ✅ Done | Register plan products with dynamic JSON attributes and 1st year commission rate (%). Under Settings.                                                                   |
| Auto-renewal        | ✅ Done | Renewal date computed from start date + frequency. Never goes stale.                                                                                                    |
| Renewal Alert       | ✅ Done | Dashboard banner for policies renewing within 30 days. Red badge ≤ 7 days.                                                                                              |
| Commission Tracking | ✅ Done | Per-plan commission rate (%). Estimated 1st year commission per client and across all policyholders.                                                                    |
| Mobile UI           | ✅ Done | Collapsible sidebar with hamburger + backdrop. Tables scroll horizontally on small screens.                                                                             |

---

## Roadmap (Not Yet Built)

- [ ] Export clients to CSV
- [ ] Birthday reminder (if DOB stored)
- [ ] Dark mode toggle
- [ ] Search across leads (currently search only covers clients)

---

## Stack

| Layer            | Technology                                  |
| ---------------- | ------------------------------------------- |
| Backend          | Laravel 12 (PHP 8.2+)                       |
| Database         | MySQL 8                                     |
| Templating       | Laravel Blade                               |
| UI interactivity | Alpine.js v3 (CDN)                          |
| CSS              | Tailwind CSS v3 — matcha × strawberry theme |
| Auth             | Laravel Breeze (session, single user)       |
| Build tool       | Vite                                        |

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

Edit `.env` — set your DB credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cms_takaful
DB_USERNAME=root
DB_PASSWORD=your_password
```

Then:

```bash
php artisan migrate
php artisan db:seed --class=AdminUserSeeder
npm run build
php artisan serve
```

Visit `http://localhost:8000` and log in:

Change the password after first login via tinker:

```bash
php artisan tinker
>>> \App\Models\User::first()->update(['password' => bcrypt('your_new_password')]);
```

---

## Deployment (Hostinger)

1. Upload project to subdomain directory (e.g. `/public_html/list/`)
2. Set document root to the `/public` folder
3. Ensure `mod_rewrite` is enabled (standard on Hostinger shared)
4. Run on server:

```bash
composer install --no-dev --optimize-autoloader
npm run build
php artisan migrate --force
php artisan db:seed --class=AdminUserSeeder
php artisan config:cache
php artisan route:cache
php artisan view:cache
chmod -R 755 storage bootstrap/cache
```

---

## Login

`list.drtakaful.com/login` — session-based, 8-hour lifetime. No public registration.

---

_Built by Hana · AIA Public Takaful · Dr Takaful brand_
