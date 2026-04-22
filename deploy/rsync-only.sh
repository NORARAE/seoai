#!/usr/bin/env bash
# ──────────────────────────────────────────────────────
# SEOAIco — Manual rsync deploy (bypasses preflight)
# Use when deploy.sh is blocked by preflight failures.
# Usage: bash deploy/rsync-only.sh
# ──────────────────────────────────────────────────────
set -euo pipefail

SERVER_IP="${SEOAI_SERVER_IP:?Set SEOAI_SERVER_IP env var (e.g. export SEOAI_SERVER_IP=1.2.3.4)}"
SERVER_USER="root"
APP_DIR="/var/www/seoai"
REPO_DIR="$(cd "$(dirname "$0")/.." && pwd)"

echo "═══ SEOAIco rsync-only deploy → ${SERVER_IP} ═══"

# ── Build frontend ──
echo "→ Building frontend assets..."
cd "${REPO_DIR}"
npm run build 2>&1 | tail -5

# ── Rsync ──
echo "→ Syncing code..."
rsync -azP --delete \
  --exclude='.env' \
  --exclude='vendor/' \
  --exclude='node_modules/' \
  --exclude='storage/logs/*' \
  --exclude='storage/framework/cache/data/*' \
  --exclude='storage/framework/sessions/*' \
  --exclude='storage/framework/views/*' \
  --exclude='database/database.sqlite' \
  --exclude='database/database.sqlite-journal' \
  --exclude='bootstrap/cache/' \
  --exclude='.git/' \
  "${REPO_DIR}/" "${SERVER_USER}@${SERVER_IP}:${APP_DIR}/"

# ── Remote cache & permission fix ──
echo "→ Fixing permissions and rebuilding caches..."
ssh "${SERVER_USER}@${SERVER_IP}" bash << 'REMOTE'
set -euo pipefail
cd /var/www/seoai

# Fix ownership — rsync from macOS sets 501:staff, www-data needs write access
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
# SQLite dir must be writable for journal file creation
chown www-data:www-data database
chmod 775 database

# Regen packages cache if stale (laravel/pail is dev-only, not in prod vendor)
if php artisan package:discover --ansi 2>&1 | grep -q 'not found'; then
  rm -f bootstrap/cache/packages.php
  php artisan package:discover
fi

php artisan view:clear
php artisan config:cache
php artisan route:cache

echo "✓ Remote setup done"
REMOTE

echo "═══ Done! Site live at https://seoaico.com ═══"
