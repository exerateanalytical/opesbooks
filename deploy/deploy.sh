#!/usr/bin/env bash
# OPESBooks — one-shot deploy/update script for cPanel with SSH access.
# Run from the Laravel app root:  bash deploy/deploy.sh
set -e

echo "→ Pulling latest code…"
git pull --ff-only

echo "→ Installing PHP dependencies (production)…"
composer install --no-dev --optimize-autoloader --no-interaction

echo "→ Running migrations…"
php artisan migrate --force

echo "→ Seeding reference data (idempotent)…"
php artisan db:seed --class=CountryConfigSeeder --force || true
php artisan db:seed --class=FeatureFlagSeeder  --force || true
php artisan db:seed --class=PlanConfigSeeder   --force || true

echo "→ Linking storage…"
php artisan storage:link || true

echo "→ Caching config/routes/views…"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache || true

echo "→ Clearing compiled caches that must stay fresh…"
php artisan queue:restart || true

echo "✓ Deploy complete."
