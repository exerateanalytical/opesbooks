# Deploying OPESBooks to cPanel Shared Hosting

This guide ships the app to a cPanel shared host. No build step is required —
the front-end uses Tailwind and Alpine via CDN, so there is **no `npm run build`**.

**Requirements on the host:** PHP **8.3+**, MySQL **5.7+ / MariaDB 10.3+**, and
the PHP extensions Laravel needs (pdo_mysql, mbstring, openssl, json, ctype,
fileinfo, bcmath, gd). Composer is recommended; if unavailable, see step 3B.

---

## 1. Create the MySQL database (cPanel → MySQL® Databases)

1. **Create database** → e.g. `opesbooks` (cPanel prefixes it: `cpaneluser_opesbooks`).
2. **Create user** → e.g. `opesbk` with a strong password.
3. **Add user to database** → grant **ALL PRIVILEGES**.
4. Note the final names (`cpaneluser_opesbooks`, `cpaneluser_opesbk`) for `.env`.

---

## 2. Choose where the app lives (document root)

Laravel must serve from its `public/` folder — never expose the project root.

### Option A — Recommended (subdomain or document-root change)
- Create a subdomain like `app.opesbooks.cm` (cPanel → Domains/Subdomains), OR
- For the main domain, edit its **Document Root** (cPanel → Domains → manage).
- Upload the **entire project** to e.g. `/home/cpaneluser/opesbooks`.
- Set the domain/subdomain **Document Root to `…/opesbooks/public`**.
- Done — no file juggling. Skip to step 3.

### Option B — Fallback (main domain, cannot change document root)
- Upload the project to `/home/cpaneluser/opesbooks` (a sibling of `public_html`).
- Copy the **contents of the project's `public/` folder** into `public_html/`
  (this brings `manifest.json`, `sw.js`, `icon.svg`, `.htaccess`, the storage
  symlink, etc.).
- Replace `public_html/index.php` with `deploy/public_html-index.php`
  (rename to `index.php`; adjust `$base` if your app folder isn't `opesbooks`).
- Put `deploy/public_html-.htaccess` in `public_html/` as `.htaccess`.

---

## 3. Get the code + dependencies onto the server

### 3A — With SSH/Terminal + Composer (preferred)
```bash
cd ~/opesbooks
git clone https://github.com/exerateanalytical/opesbooks.git .   # or upload + unzip
composer install --no-dev --optimize-autoloader
```

### 3B — Upload-only (no SSH / no Composer)
- On your machine run `composer install --no-dev --optimize-autoloader`,
  then zip the project **including the `vendor/` folder** and upload+extract it
  via File Manager. (The app runs entirely from committed code + `vendor/`.)

---

## 4. Configure the environment

```bash
cp .env.production.example .env
php artisan key:generate          # or paste a generated key if no CLI
```
Edit `.env` and set: `APP_URL`, the three `DB_*` values from step 1, `MAIL_*`
(a cPanel email account), and optionally `GEMINI_API_KEY` / payment keys.
Keep `APP_ENV=production`, `APP_DEBUG=false`, `FILESYSTEM_DISK=public`.

---

## 5. Migrate, seed, link storage

```bash
php artisan migrate --force
php artisan db:seed --class=CountryConfigSeeder --force
php artisan db:seed --class=FeatureFlagSeeder  --force
php artisan db:seed --class=PlanConfigSeeder   --force
# Optional (demo data): php artisan db:seed --force
php artisan storage:link
```
> No CLI? Run migrations once via a temporary protected route, or import a
> `mysqldump` of a locally-migrated MySQL DB through phpMyAdmin.

The migrations create everything new: `company_user`, `country_configs`,
`crm_*`, `projects_*`, `ai_*`, `mecef_*`, `api_keys`/`api_request_logs`,
`announcements`, `platform_settings`, `feature_flags`, billing tables,
`system_metrics`, `admin_impersonation_logs`, webhook tables,
`in_app_notifications`, onboarding columns, etc.

---

## 6. Permissions

```bash
chmod -R 775 storage bootstrap/cache
```
Ensure `storage/` and `bootstrap/cache/` are writable by the web user.

---

## 7. The single cron line (drives EVERYTHING)

cPanel → **Cron Jobs** → add, running **every minute**:

```
* * * * * /usr/local/bin/php /home/cpaneluser/opesbooks/artisan schedule:run >> /dev/null 2>&1
```
(Use the host's real PHP 8.3 binary path — check cPanel → "Select PHP Version".)

This one line covers **all** background work, because the scheduler runs:
- `webhooks:deliver` (every minute) — webhook delivery with retry/backoff
- `queue:work --stop-when-empty` (every minute) — drains the DB queue
  (DGI sync, telecom jobs) **without a persistent worker**
- `metrics:record` (every 5 min) — System Health charts
- daily/monthly jobs — recurring transactions, overdue invoices, depreciation

No separate `queue:work` daemon is needed.

---

## 8. Cache for production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
Re-run these after any `.env` or code change (or just run `bash deploy/deploy.sh`).

---

## 9. HTTPS

cPanel → **SSL/TLS Status** → run **AutoSSL** for the domain. Once active,
uncomment the "Force HTTPS" block in the public `.htaccess` and set
`SESSION_SECURE_COOKIE=true` (already in the production env template).

---

## 10. Verify

- `https://yourdomain/api/v1/health` → `{"status":"ok"}`
- `https://yourdomain/login` loads; register → onboarding wizard.
- `/admin/login` works for the SUPER_ADMIN.
- Issue an API key in admin → call `/api/v1/integration/tax/vat-summary` with it.
- Trigger a webhook/payment and confirm the cron processed it within ~1 min.

---

## Updating later

With SSH: `bash deploy/deploy.sh` (pull → composer → migrate → cache).
Upload-only: re-upload changed files, then re-run steps 5 & 8 as needed.

---

## Notes & gotchas

- **No asset build** — Tailwind/Alpine load from CDN. (For a fully offline/CSP-
  strict setup you could self-host them later; not required to ship.)
- **`FILESYSTEM_DISK=public`** is mandatory or logo uploads/URLs 404.
- **Mail**: uses cPanel SMTP. Until configured, transactional emails are sent
  best-effort and fail silently (in-app notifications still work).
- **Payments & AI** run in **sandbox / "unavailable"** mode until you set the
  Orange Money / MTN MoMo merchant keys and `GEMINI_API_KEY`.
- **Ollama (offline AI)** cannot run on shared hosting — leave it disabled.
- **MECeF** is in sandbox mode until DGI issues real NIM/API credentials.
