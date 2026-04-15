# CLAUDE.md — Dr Takaful CMS Briefing

> This file is the primary context document for Claude Code.  
> Read this entire file before writing any code.  
> Follow every instruction here precisely. Do not improvise on architecture or theme decisions.

---

## What This Project Is

This is a **private, self-hosted CRM and dashboard** for a Malaysian takaful insurance agent who runs the personal brand "Dr Takaful" (website: drtakaful.com). It is not a SaaS product. It is a personal business productivity tool used only by one person (the agent, Hana).

The system is accessed via `list.drtakaful.com` — a subdomain of her existing website.

The system helps the agent:
1. Manage her existing policyholder clients and their plans
2. Track warm and hot leads (prospective clients not yet converted)
3. Log every client interaction (channel, topic, next action) so she never loses track of who she's spoken to and when
4. Define and track "reach angles" — prospecting strategies she uses to approach different audience segments

---

## Stack — Strict. Do Not Deviate.

| Layer | Technology | Notes |
|---|---|---|
| Backend | **Laravel 12** | Use the latest Laravel 12 conventions. Do not use Laravel 10 or 11 patterns. |
| Database | **MySQL 8** | Hosted on Hostinger. All migrations must be MySQL-compatible. |
| Templating | **Laravel Blade** | All views are Blade. Do NOT use Livewire, Inertia, or Vue. |
| UI interactivity | **Alpine.js v3** | Load via CDN in the layout. Use for dropdowns, modals, toggles only. |
| CSS | **Tailwind CSS v3** | Custom colour tokens defined in `tailwind.config.js`. Use these always. |
| Auth | **Laravel Breeze** | Single-user only. Remove registration routes. Keep login only. |
| Icons | **Heroicons** | Use the `blade-heroicons` package or inline SVGs. |
| Build tool | **Vite** (default Laravel 11) | Standard setup. |

---

## Naming Conventions

- **Models**: singular PascalCase — `Client`, `Policy`, `Lead`, `Touchpoint`, `ReachAngle`
- **Controllers**: plural — `ClientController`, `LeadController`, `TouchpointController`, `ReachAngleController`, `DashboardController`
- **Routes**: kebab-case slugs — `/clients`, `/leads`, `/follow-up`, `/angles`
- **Blade views**: snake_case filenames — `index.blade.php`, `create.blade.php`, `show.blade.php`, `edit.blade.php`
- **Database tables**: snake_case plural — `clients`, `policies`, `leads`, `touchpoints`, `reach_angles`
- **Pivot tables**: alphabetical order snake_case — `angle_client`

---

## File Structure to Build

Build exactly this structure. Create every file listed.

```
app/
├── Http/
│   └── Controllers/
│       ├── DashboardController.php
│       ├── ClientController.php
│       ├── LeadController.php
│       ├── TouchpointController.php
│       └── ReachAngleController.php
├── Models/
│   ├── User.php          (already exists, extend if needed)
│   ├── Client.php
│   ├── Policy.php
│   ├── Lead.php
│   ├── Touchpoint.php
│   └── ReachAngle.php

database/
├── migrations/
│   ├── xxxx_create_clients_table.php
│   ├── xxxx_create_policies_table.php
│   ├── xxxx_create_leads_table.php
│   ├── xxxx_create_touchpoints_table.php
│   ├── xxxx_create_reach_angles_table.php
│   └── xxxx_create_angle_client_table.php
└── seeders/
    └── AdminUserSeeder.php

resources/
└── views/
    ├── layouts/
    │   └── app.blade.php
    ├── dashboard/
    │   └── index.blade.php
    ├── clients/
    │   ├── index.blade.php
    │   ├── show.blade.php
    │   ├── create.blade.php
    │   └── edit.blade.php
    ├── leads/
    │   ├── index.blade.php
    │   ├── create.blade.php
    │   └── edit.blade.php
    ├── touchpoints/
    │   ├── index.blade.php
    │   └── create.blade.php
    └── angles/
        ├── index.blade.php
        └── create.blade.php

routes/
└── web.php

tailwind.config.js
vite.config.js
```

---

## Database Schema — Build Exactly This

### Migration: `create_clients_table`

```php
Schema::create('clients', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('phone')->nullable();           // Format: 60xxxxxxxxx
    $table->string('ic_no')->nullable();           // Malaysian IC number
    $table->string('email')->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
});
```

### Migration: `create_policies_table`

```php
Schema::create('policies', function (Blueprint $table) {
    $table->id();
    $table->foreignId('client_id')->constrained()->onDelete('cascade');
    $table->enum('plan_type', [
        'medical', 'critical_illness', 'personal_accident',
        'group', 'hibah', 'income', 'other'
    ]);
    $table->string('plan_name')->nullable();       // e.g. "A-Plus Med"
    $table->decimal('coverage_amount', 12, 2)->nullable();
    $table->date('start_date')->nullable();
    $table->date('renewal_date')->nullable();
    $table->decimal('premium_monthly', 10, 2)->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
});
```

### Migration: `create_leads_table`

```php
Schema::create('leads', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('phone')->nullable();
    $table->enum('source', [
        'referral', 'social_media', 'cold_outreach',
        'event', 'walk_in', 'other'
    ])->default('other');
    $table->string('interest_area')->nullable();   // Free text: "Medical Card + CI"
    $table->enum('temperature', ['hot', 'warm'])->default('warm');
    $table->enum('stage', [
        'new', 'contacted', 'presented', 'negotiating', 'stalled'
    ])->default('new');
    $table->date('next_contact')->nullable();
    $table->text('notes')->nullable();
    $table->timestamp('converted_at')->nullable(); // Set when promoted to client
    $table->timestamps();
});
```

### Migration: `create_touchpoints_table`

Touchpoints are **polymorphic** — they can belong to either a `Client` or a `Lead`.

```php
Schema::create('touchpoints', function (Blueprint $table) {
    $table->id();
    $table->morphs('touchable');                   // touchable_type + touchable_id
    $table->dateTime('contacted_at');
    $table->enum('channel', [
        'whatsapp', 'phone_call', 'in_person',
        'dm_instagram', 'dm_facebook', 'email', 'other'
    ])->default('whatsapp');
    $table->string('topic');
    $table->text('notes')->nullable();
    $table->string('next_action')->nullable();
    $table->date('next_action_date')->nullable();
    $table->timestamps();
});
```

### Migration: `create_reach_angles_table`

```php
Schema::create('reach_angles', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description')->nullable();
    $table->string('target_segment')->nullable();  // e.g. "Working adults 30–45"
    $table->enum('status', ['active', 'paused', 'archived'])->default('active');
    $table->timestamps();
});
```

### Migration: `create_plan_products_table`

```php
Schema::create('plan_products', function (Blueprint $table) {
    $table->id();
    $table->enum('plan_type', ['medical','critical_illness','personal_accident','group','hibah','income','other']);
    $table->string('name');
    $table->decimal('commission_first_year', 10, 2)->nullable(); // percentage, e.g. 12.50 = 12.5%
    $table->json('attributes')->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
});
```

> **Note:** `policies` table no longer has a `renewal_date` column. It was replaced with `frequency` enum (`monthly`/`yearly`). Renewal date is computed dynamically via `Policy::nextRenewalDate()`. The `policies` table also has `plan_product_id` (nullable FK to `plan_products`).

### Migration: `create_angle_client_table` (pivot)

```php
Schema::create('angle_client', function (Blueprint $table) {
    $table->id();
    $table->foreignId('reach_angle_id')->constrained()->onDelete('cascade');
    $table->foreignId('client_id')->constrained()->onDelete('cascade');
    $table->timestamp('reached_at')->useCurrent();
});
```

---

## Eloquent Models — Build These

### `Client.php`
```php
class Client extends Model {
    protected $fillable = ['name', 'phone', 'ic_no', 'email', 'notes'];

    public function policies() {
        return $this->hasMany(Policy::class);
    }

    public function touchpoints() {
        return $this->morphMany(Touchpoint::class, 'touchable')->latest('contacted_at');
    }

    public function reachAngles() {
        return $this->belongsToMany(ReachAngle::class, 'angle_client')->withPivot('reached_at');
    }

    public function lastTouchpoint() {
        return $this->touchpoints()->latest('contacted_at')->first();
    }
}
```

### `Policy.php`
```php
class Policy extends Model {
    protected $fillable = [
        'client_id', 'plan_product_id', 'plan_type', 'plan_name',
        'coverage_amount', 'start_date', 'frequency', 'premium_monthly', 'notes'
    ];

    protected $casts = ['start_date' => 'date'];

    public function client() { return $this->belongsTo(Client::class); }
    public function planProduct() { return $this->belongsTo(PlanProduct::class); }

    // Computes next renewal date from start_date + frequency (monthly/yearly)
    public function nextRenewalDate(): ?Carbon { ... }

    // Estimated 1st year commission in RM: annualised premium × planProduct commission rate
    // Monthly: premium × 12 × rate%. Yearly: premium × rate%. Returns null if data missing.
    public function estimatedCommissionFirstYear(): ?float { ... }
}
```

### `Lead.php`
```php
class Lead extends Model {
    protected $fillable = [
        'name', 'phone', 'source', 'interest_area',
        'temperature', 'stage', 'next_contact', 'notes', 'converted_at'
    ];

    protected $casts = [
        'next_contact'  => 'date',
        'converted_at'  => 'datetime',
    ];

    public function touchpoints() {
        return $this->morphMany(Touchpoint::class, 'touchable')->latest('contacted_at');
    }

    public function isConverted(): bool {
        return $this->converted_at !== null;
    }
}
```

### `Touchpoint.php`
```php
class Touchpoint extends Model {
    protected $fillable = [
        'touchable_type', 'touchable_id',
        'contacted_at', 'channel', 'topic', 'notes',
        'next_action', 'next_action_date'
    ];

    protected $casts = [
        'contacted_at'     => 'datetime',
        'next_action_date' => 'date',
    ];

    public function touchable() {
        return $this->morphTo();
    }
}
```

### `ReachAngle.php`
```php
class ReachAngle extends Model {
    protected $fillable = ['title', 'description', 'target_segment', 'status'];

    public function clients() {
        return $this->belongsToMany(Client::class, 'angle_client')->withPivot('reached_at');
    }
}
```

---

## Routes — `routes/web.php`

```php
Route::middleware('auth')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Policyholders
    Route::resource('clients', ClientController::class);

    // Policies (nested under clients)
    Route::post('clients/{client}/policies', [ClientController::class, 'storePolicy'])->name('clients.policies.store');
    Route::delete('clients/{client}/policies/{policy}', [ClientController::class, 'destroyPolicy'])->name('clients.policies.destroy');

    // Leads
    Route::resource('leads', LeadController::class);
    Route::post('leads/{lead}/convert', [LeadController::class, 'convert'])->name('leads.convert');

    // Touchpoints (polymorphic — works for both clients and leads)
    Route::get('follow-up', [TouchpointController::class, 'index'])->name('touchpoints.index');
    Route::post('clients/{client}/touchpoints', [TouchpointController::class, 'storeForClient'])->name('clients.touchpoints.store');
    Route::post('leads/{lead}/touchpoints', [TouchpointController::class, 'storeForLead'])->name('leads.touchpoints.store');

    // Reach Angles
    Route::resource('angles', ReachAngleController::class);
    Route::post('angles/{angle}/clients/{client}', [ReachAngleController::class, 'attachClient'])->name('angles.attach');

});
```

Remove the default `/register` route from `routes/auth.php` or the Breeze-generated auth routes. Login only.

---

## Controllers — Behaviour Description

### `DashboardController`
- `index()`: Fetch and pass to view:
  - `$totalClients` — count of clients
  - `$hotLeads` — count of leads where `temperature = hot` and `converted_at IS NULL`
  - `$warmLeads` — count of leads where `temperature = warm` and `converted_at IS NULL`
  - `$recentClients` — latest 5 clients (by `updated_at`)
  - `$urgentLeads` — leads where `temperature = hot` and `converted_at IS NULL`, ordered by `next_contact ASC`, limit 5
  - `$recentTouchpoints` — latest 5 touchpoints, eager-load `touchable` (either client or lead)
  - `$renewingSoon` — policies whose computed `nextRenewalDate()` falls within 30 days, ordered ascending (PHP collection — no `renewal_date` column)
  - `$topCommissionClients` — top 5 clients ranked by total estimated 1st-year commission (PHP computed via `estimatedCommissionFirstYear()`)
  - `$totalEstimatedCommission` — sum of all clients' estimated 1st-year commission
  - `$topPlanProducts` — top 5 plan products ranked by number of policies using them

### `ClientController`
- Full resourceful CRUD
- `index()`: paginate clients 20 per page, with search by name or phone (`?q=`)
- `show()`: load client with `policies`, `touchpoints` (last 10), and `reachAngles`
- `storePolicy()`: create a policy attached to the client
- `destroyPolicy()`: soft-delete or hard-delete a policy from client

### `LeadController`
- Full resourceful CRUD (excluding show — leads are listed and edited inline)
- `index()`: separate hot leads and warm leads into two collections, show both on same page
- `convert($lead)`: set `converted_at = now()`, create a new `Client` record from lead data, redirect to the new client's create/show page so the agent can add policies

### `TouchpointController`
- `index()`: paginate all touchpoints 20 per page, eager-load `touchable`, with filter by channel
- `storeForClient($client)`: validate input, create touchpoint morphed to client
- `storeForLead($lead)`: validate input, create touchpoint morphed to lead

### `ReachAngleController`
- Full resourceful CRUD
- `index()`: list all angles, annotate each with `reached_count` (count of pivot rows)
- `attachClient($angle, $client)`: attach a client to an angle via pivot table if not already attached

---

## Validation Rules

### Client
```php
'name'  => 'required|string|max:255',
'phone' => 'nullable|string|max:20',
'ic_no' => 'nullable|string|max:20',
'email' => 'nullable|email|max:255',
'notes' => 'nullable|string',
```

### Policy
```php
'plan_type'        => 'required|in:medical,critical_illness,personal_accident,group,hibah,income,other',
'plan_name'        => 'nullable|string|max:255',
'coverage_amount'  => 'nullable|numeric|min:0',
'start_date'       => 'nullable|date',
'renewal_date'     => 'nullable|date|after_or_equal:start_date',
'premium_monthly'  => 'nullable|numeric|min:0',
'notes'            => 'nullable|string',
```

### Lead
```php
'name'          => 'required|string|max:255',
'phone'         => 'nullable|string|max:20',
'source'        => 'required|in:referral,social_media,cold_outreach,event,walk_in,other',
'interest_area' => 'nullable|string|max:255',
'temperature'   => 'required|in:hot,warm',
'stage'         => 'required|in:new,contacted,presented,negotiating,stalled',
'next_contact'  => 'nullable|date',
'notes'         => 'nullable|string',
```

### Touchpoint
```php
'contacted_at'     => 'required|date',
'channel'          => 'required|in:whatsapp,phone_call,in_person,dm_instagram,dm_facebook,email,other',
'topic'            => 'required|string|max:255',
'notes'            => 'nullable|string',
'next_action'      => 'nullable|string|max:255',
'next_action_date' => 'nullable|date',
```

---

## Tailwind Config — `tailwind.config.js`

```js
/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        matcha: {
          50:  '#e8f0eb',
          100: '#c5d9cc',
          200: '#9fbfaa',
          400: '#6a9b78',
          600: '#4a7c59',
          800: '#2d5a3d',
          900: '#1a3324',
        },
        strawberry: {
          50:  '#fceef2',
          100: '#f5c8d5',
          200: '#ed99b5',
          400: '#e07090',
          600: '#c94f6d',
          800: '#8f2a47',
          900: '#5e1830',
        },
      },
      fontFamily: {
        sans: ['system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'sans-serif'],
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
```

---

## Layout Shell — `resources/views/layouts/app.blade.php`

The entire app lives inside a two-column shell:
- Left: sidebar (220px wide, dark matcha background) — fixed overlay on mobile (hamburger toggle via Alpine.js), static in flex flow on desktop (`lg:`)
- Right: flex column — topbar (56px) + scrollable main content area

**Mobile behaviour:** sidebar is hidden by default, slides in from left when hamburger is tapped. Dark backdrop closes it on tap. Topbar search is hidden on mobile (`hidden lg:block`). Tables use `overflow-x-auto` wrappers.

### Sidebar structure (top to bottom):
1. Logo area: "Dr Takaful" in white, "list.drtakaful.com" in muted white below
2. Nav sections with labels:
   - **Overview** → Dashboard
   - **Clients** → My Policyholders, Warm & Hot Leads, Follow-up Log
   - **Strategy** → Reach Angles
   - **Settings** → (placeholder for future)
3. Bottom: agent name "Hana · AIA Public Takaful" in small muted text

### Active nav item styling:
- Left border: `border-l-2 border-strawberry-400`
- Background: `bg-white/10`
- Text: `text-white`

### Topbar structure:
- Page title (left)
- Search input (centre)
- "+ New" primary button (right, context-sensitive)
- Avatar circle initials "HN" (far right)

### Alpine.js CDN (include in layout `<head>`):
```html
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
```

---

## View: `dashboard/index.blade.php`

Sections to render (in order):
1. **Stats row** — 4 metric cards in a grid: Total Policyholders, Hot Leads, Warm Leads, Last Outreach
2. **Two-column grid**:
   - Left: Recent Policyholders (last 5, with name, plan types, last contacted label)
   - Right: Hot & Warm Leads (top 5 by urgency, with temperature tag)
3. **Two-column grid**:
   - Left: Follow-up Log (last 5 touchpoints — name, topic, date, channel dot indicator)
   - Right: Reach Angles (all active angles — title, target segment, reached count)

---

## View: `clients/index.blade.php`

- Search bar at top (`?q=` param)
- Table: Name | Phone | Plans | Last Contacted | Last Topic | Actions
- Pagination at bottom
- "New Client" button links to `clients/create`
- Each row links to `clients/{id}` (show page)

## View: `clients/show.blade.php`

Split layout:
- Left column (60%):
  - Client header (name, phone, IC, email, notes)
  - Policies section: list of attached policies with plan type, coverage, renewal date — with "Add Policy" inline form (Alpine modal)
  - Add Touchpoint inline form (collapsed by default, Alpine toggle)
- Right column (40%):
  - Full interaction history (touchpoints, paginated)
  - Reach Angles this client is linked to

## View: `leads/index.blade.php`

Two sections on one page:
- Hot Leads section (strawberry accent) — list with temperature badge, interest area, stage, next contact date, action buttons
- Warm Leads section (amber accent) — same structure
- "Convert to Client" button on each lead row → POST to `leads/{id}/convert`
- "Log Touchpoint" quick button → inline form with Alpine

## View: `touchpoints/index.blade.php`

- All touchpoints across clients and leads in chronological order (newest first)
- Filter by channel (dropdown)
- Each row: date | person name (linked) | channel | topic | next action

## View: `angles/index.blade.php`

- Card grid (2 columns desktop, 1 column mobile)
- Each card: angle title, target segment, status badge, reached count, description
- "Add Angle" button at top

---

## Seeder — `AdminUserSeeder.php`

```php
public function run(): void {
    \App\Models\User::create([
        'name'     => 'Hana',
        'email'    => 'admin@drtakaful.com',
        'password' => bcrypt('takaful2024!'),
    ]);
}
```

---

## Authentication Setup Notes

After installing Breeze:

1. In `routes/auth.php` — **remove or comment out** the registration routes:
   - `Route::get('register', ...)`
   - `Route::post('register', ...)`

2. Remove `RegisteredUserController` references.

3. Redirect after login should go to `/` (dashboard).

4. The login page should use the matcha theme — update `resources/views/auth/login.blade.php`:
   - Background: `bg-matcha-50`
   - Card: white, rounded-xl, shadow-sm
   - Button: `bg-matcha-600 hover:bg-matcha-800 text-white`
   - Logo or site name "Dr Takaful" centred above the form

---

## Important UX Rules

- **No red flash messages for errors** — use inline validation error messages below each field
- **Success flash** — use a green toast-style banner at the top that auto-dismisses after 3 seconds (Alpine.js: `x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"`)
- **Empty states** — every list/table must have an empty state message (e.g., "No clients yet. Add your first policyholder.")
- **Confirm before delete** — use `x-data` and Alpine to show a simple inline confirmation before destructive actions. Do not use `window.confirm()`.
- **WhatsApp deep link** — wherever a phone number is displayed, wrap it in: `<a href="https://wa.me/{{ $client->phone }}" target="_blank">{{ $client->phone }}</a>`
- **Date formatting** — display all dates as `d M Y` format (e.g. "14 Apr 2026") using `{{ $date->format('d M Y') }}`
- **Currency** — display Malaysian Ringgit as `RM {{ number_format($amount, 2) }}`

---

## Build Order

Build in this exact sequence to avoid dependency issues:

1. Run `laravel new drtakaful-cms` and select **Blade** stack when prompted (Laravel 12 interactive installer)
2. Configure `.env` database credentials
3. Run Tailwind config setup, install `@tailwindcss/forms`
4. Remove registration routes
5. Create all migrations (in order: clients → policies → leads → touchpoints → reach_angles → angle_client)
6. Run `php artisan migrate`
7. Create all Models with relationships
8. Create all Controllers (empty stubs first)
9. Define all routes in `web.php`
10. Build `layouts/app.blade.php` (shell, sidebar, topbar)
11. Build Dashboard view + `DashboardController@index`
12. Build Clients views + `ClientController` (all methods)
13. Build Leads views + `LeadController` (all methods)
14. Build Touchpoints views + `TouchpointController`
15. Build Angles views + `ReachAngleController`
16. Run seeder: `php artisan db:seed --class=AdminUserSeeder`
17. Run `npm run build`
18. Test all routes locally with `php artisan serve`

---

## Do Not Do These Things

- Do not use Livewire
- Do not use Inertia.js
- Do not use Vue or React
- Do not use Laravel Sanctum or Passport (session auth only)
- Do not create a public registration page
- Do not add any external CSS framework other than Tailwind
- Do not use `dd()` or `dump()` in production code
- Do not hardcode the agent's name anywhere except the seeder and the layout bottom text
- Do not add unnecessary npm packages — keep the dependency list minimal
- Do not use Laravel's default `User` model avatar or profile features — not needed

---

## Final Notes for Claude Code

- This project will be deployed on **Hostinger shared hosting or VPS**. Keep that in mind — no Docker, no Redis, no queue workers needed.
- PHP version is 8.2 minimum. Use nullsafe operators (`?->`) and match expressions where appropriate.
- All Blade files must be clean, readable, and well-commented with `{{-- section name --}}` block comments.
- When in doubt about UI structure, refer to the matcha-strawberry theme described in this file and the README.
- Every form must have `@csrf`. Every DELETE action must use a form with `@method('DELETE')`.
- Eager-load relationships in controllers to avoid N+1 queries: `Client::with('policies', 'touchpoints')->paginate(20)`

---

*End of CLAUDE.md*
