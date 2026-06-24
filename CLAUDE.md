# Opes Books — CLAUDE.md

SaaS accounting and tax compliance platform for Cameroonian SMEs, built by **Opesware** (Douala, Cameroun). SYSCOHADA Revised chart of accounts, DGI Fiscalis/SIGIT real-time e-invoicing (2026 Finance Law), TVA 17.5% + CAC 10% of TVA = 19.25% TTC.

## Stack

- **Laravel 13** / PHP 8.4 — API backend + Blade views
- **SQLite** (dev) / **MySQL** (prod) — `DB_CONNECTION` in `.env`
- **Laravel Sanctum** — Bearer token auth
- **Alpine.js 3** — SPA reactivity in `/app`
- **Tailwind CSS CDN** — utility-first styling
- **Livewire v4** — `/tax-dashboard` and `/dgi-monitor` pages
- **DomPDF** (`barryvdh/laravel-dompdf`) — invoice PDF generation
- **Brick\Math\BigDecimal** — precise tax arithmetic

## Project structure

```
app/
  Http/Controllers/Api/V1/   # All API controllers
  Http/Middleware/            # RequireActiveSubscription, RequireRole
  Models/                    # Company, User, JournalEntry, Subscription, …
  Services/                  # CameroonTaxEngine, FiscalGeographyRouter, …
  Jobs/                      # SyncInvoiceToDgiPortalJob, ProcessTelecomPayload
resources/views/
  pages/
    login.blade.php           # Auth SPA (Alpine.js, FR/EN bilingual)
    app.blade.php             # Main SPA — all accounting pages in one file
    about.blade.php           # About / credits page
  components/app-layout.blade.php  # Shared nav layout for Livewire pages
  invoices/invoice.blade.php  # DomPDF invoice template
  errors/404.blade.php        # Custom 404
  errors/500.blade.php        # Custom 500
routes/
  api.php                    # All /api/v1/* routes
  web.php                    # / → /app, /login, /app, /about, Livewire pages
```

## Local dev

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan storage:link        # creates public/storage symlink for logos
php artisan serve
```

## API auth

All protected endpoints require `Authorization: Bearer <token>`. Tokens issued by `POST /api/v1/auth/register` or `POST /api/v1/auth/login`. Roles: `OWNER`, `ACCOUNTANT`, `CLERK`.

## SPA pages (`/app?page=<name>`)

| page | description |
|---|---|
| `dashboard` | KPI cards (revenue, TVA, CAC, charges) from trial balance |
| `journal` | Ledger entries with DGI export + PDF download per row |
| `ledger` | Grand Livre / trial balance |
| `invoice` | PDF invoice generator (DomPDF) |
| `calculator` | TVA/CAC calculator (HT ↔ TTC) |
| `import` | Bank statement CSV import |
| `subledgers` | 571x Mobile Money sub-ledger provisioning |
| `sync` | Offline sync status and push |
| `team` | Team members + invite (OWNER only) |
| `settings` | Company config: NIU, RCCM, logo, letterhead, bank details |
| `subscription` | Plan picker + Mobile Money billing (STK push) |
| `profile` | User name/email edit + password change |

## Tax constants (Cameroun)

```
TVA rate: 17.5%
CAC rate: 10% of TVA  →  1.75% of HT
TTC multiplier: 1.1925
```

## Key API routes

```
POST   /api/v1/auth/register
POST   /api/v1/auth/login
GET    /api/v1/auth/me
PUT    /api/v1/auth/profile
PUT    /api/v1/auth/password
POST   /api/v1/companies/{id}/invoice/generate          → PDF
GET    /api/v1/companies/{id}/invoice/{entry}/download  → PDF
GET    /api/v1/companies/{id}/exports/dgi-fiscalis
POST   /api/v1/companies/{id}/logo
POST   /api/v1/companies/{id}/subscriptions/initiate
GET    /api/v1/companies/{id}/subscriptions/status
```

## Git

Active dev branch: `claude/relaxed-newton-ksgwgy`
Repo: `exerateanalytical/opesbooks`
