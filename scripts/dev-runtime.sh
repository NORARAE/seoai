#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

if ! command -v npx >/dev/null 2>&1; then
  echo "npx is required for the local runtime helper. Run npm install first."
  exit 1
fi

APP_CMD="php artisan serve"
QUEUE_CMD="php artisan queue:work --queue=crawl,generation,publishing,default --tries=3 --sleep=1 --timeout=120 --memory=512"
SCHEDULER_CMD="php artisan schedule:work"
VITE_CMD="npm run dev"
LOGS_CMD="php artisan pail --timeout=0"

if [[ "${SEOAI_SKIP_VITE:-0}" == "1" ]]; then
  npx concurrently -c "#93c5fd,#c4b5fd,#fb7185,#10b981" \
    "$APP_CMD" \
    "$QUEUE_CMD" \
    "$SCHEDULER_CMD" \
    "$LOGS_CMD" \
    --names=app,queue,scheduler,logs \
    --kill-others
else
  npx concurrently -c "#93c5fd,#c4b5fd,#fb7185,#fdba74,#10b981" \
    "$APP_CMD" \
    "$QUEUE_CMD" \
    "$SCHEDULER_CMD" \
    "$VITE_CMD" \
    "$LOGS_CMD" \
    --names=app,queue,scheduler,vite,logs \
    --kill-others
fi
