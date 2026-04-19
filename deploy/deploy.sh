#!/usr/bin/env bash
# ──────────────────────────────────────────────────────
# SEOAIco — Deploy Script
# Run from your LOCAL machine to push code to the droplet
# Usage: bash deploy/deploy.sh
# ──────────────────────────────────────────────────────
set -euo pipefail

# ── CONFIG (edit these) ──
SERVER_IP="${SEOAI_SERVER_IP:?Set SEOAI_SERVER_IP env var (e.g. export SEOAI_SERVER_IP=1.2.3.4)}"
SERVER_USER="root"
APP_DIR="/var/www/seoai"
REPO_DIR="$(cd "$(dirname "$0")/.." && pwd)"

echo "═══ SEOAIco Deploy → ${SERVER_IP} ═══"

# ── 0. Indexing preflight checks ──
echo "→ Running indexing preflight checks..."
INDEX_BASE_URL="${INDEX_BASE_URL:-https://seoaico.com}" bash "${REPO_DIR}/scripts/indexing-preflight.sh"

# ── 0. Build frontend assets locally ──
echo "→ Building frontend assets..."
cd "${REPO_DIR}"
npm run build 2>&1 | tail -5

# ── 1. Sync code ──
echo "→ Syncing code to server..."
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
  --exclude='.git/' \
  "${REPO_DIR}/" "${SERVER_USER}@${SERVER_IP}:${APP_DIR}/"

# ── 2. Remote setup ──
echo "→ Running remote setup..."
ssh "${SERVER_USER}@${SERVER_IP}" bash << 'REMOTE'
set -euo pipefail
cd /var/www/seoai

# Install dependencies
composer install --no-dev --optimize-autoloader --no-interaction 2>&1 | tail -3

# Create .env if missing
if [ ! -f .env ]; then
  cp .env.example .env
  php artisan key:generate --force
  echo "⚠  Created .env from example — you need to edit it with real values!"
fi

# Ensure SQLite database exists
touch database/database.sqlite

# Run migrations
php artisan migrate --force

# Cache config, routes, views
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Storage link
php artisan storage:link 2>/dev/null || true

# Permissions
chown -R www-data:www-data /var/www/seoai
chmod -R 775 storage bootstrap/cache database

# Restart services
systemctl restart php8.3-fpm

# Queue worker — booking confirmation emails and admin alerts are queued.
# Supervisor manages seoai-queue (see deploy/supervisor/seoai-runtime.conf).
# If supervisor is NOT installed, set QUEUE_CONNECTION=sync in .env so that
# Mail::queue() falls back to synchronous sending (no worker needed, but
# each booking request will block briefly on the mail server).
supervisorctl restart seoai-queue:* 2>/dev/null || echo "⚠  supervisorctl not available — ensure QUEUE_CONNECTION=sync in .env if no queue worker is running"
supervisorctl restart seoai-scheduler 2>/dev/null || true

echo ""
echo "✓ Deploy complete!"
REMOTE

echo "═══ Done! Site live at https://seoaico.com ═══"
