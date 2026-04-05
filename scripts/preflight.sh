#!/usr/bin/env bash
# ─────────────────────────────────────────────────────────────────────────────
# seoaico.com Pre-Flight Audit — run before EVERY deploy
# Usage: bash scripts/preflight.sh
# Exits non-zero (and prints FAIL) if any check fails.
# ─────────────────────────────────────────────────────────────────────────────
set -euo pipefail

# Ensure standard system tools are available
export PATH="/opt/homebrew/bin:/usr/local/bin:/usr/bin:/bin:/usr/sbin:/sbin:$PATH"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
cd "$ROOT"

SERVER_IP="${SEOAI_SERVER_IP:-147.182.198.59}"
HOST="seoaico.com"
FAILED=0

pass() { echo "  ✔  $1"; }
fail() { echo "  ✘  $1"; FAILED=1; }

echo ""
echo "════════════════════════════════════"
echo "  seoaico.com Pre-Flight Audit"
echo "════════════════════════════════════"

# ── 1. PHP syntax check on all PHP files changed since last commit ───────────
echo ""
echo "[ 1 ] PHP syntax — changed files"
CHANGED_PHP=$(git diff --name-only HEAD 2>/dev/null | grep '\.php$' || true)
if [ -z "$CHANGED_PHP" ]; then
  pass "No PHP files changed (nothing to lint)"
else
  SYNTAX_FAIL=0
  while IFS= read -r f; do
    if [ -f "$f" ]; then
      result=$(php -l "$f" 2>&1)
      if echo "$result" | grep -q "No syntax errors"; then
        pass "$f"
      else
        fail "$f — $result"
        SYNTAX_FAIL=1
      fi
    fi
  done <<< "$CHANGED_PHP"
  [ $SYNTAX_FAIL -eq 0 ] && pass "All changed PHP files pass syntax"
fi

# ── 2. Blade @context safety — no unescaped @context outside @verbatim ───────
echo ""
echo "[ 2 ] Blade @context safety"
# Find files containing literal "@context" string
BLADE_FILES=$(grep -rl '"@context"' resources/views --include="*.blade.php" 2>/dev/null || true)
BLADE_FAIL=0
if [ -z "$BLADE_FILES" ]; then
  pass "No @context found in views"
else
  while IFS= read -r f; do
    # Check if the @context is wrapped in @verbatim or escaped as @@context
    if grep -q '"@@context"' "$f"; then
      pass "$f — uses @@context (safe)"
    elif grep -q '@verbatim' "$f"; then
      pass "$f — wrapped in @verbatim (safe)"
    else
      fail "$f — unescaped @context detected! Use @@context or @verbatim to prevent Blade compilation."
      BLADE_FAIL=1
    fi
  done <<< "$BLADE_FILES"
  [ $BLADE_FAIL -eq 0 ] && pass "All JSON-LD blocks properly escaped"
fi

# ── 3. composer.json valid ────────────────────────────────────────────────────
echo ""
echo "[ 3 ] composer.json validity"
if php -r "json_decode(file_get_contents('composer.json')); exit(json_last_error());"; then
  pass "composer.json is valid JSON"
else
  fail "composer.json is invalid JSON"
fi

# ── 4. .env production guards ─────────────────────────────────────────────────
echo ""
echo "[ 4 ] .env production guards"
ENV_FILE=".env"
if [ -f "$ENV_FILE" ]; then
  CURRENT_ENV=$(grep "^APP_ENV=" "$ENV_FILE" | cut -d= -f2 | tr -d '[:space:]"')
  if [ "$CURRENT_ENV" = "local" ]; then
    echo "  ⚠   APP_ENV=local (local dev — skipping production guards)"
  else
    if [ "$CURRENT_ENV" = "production" ]; then
      pass "APP_ENV=production"
    else
      fail "APP_ENV=$CURRENT_ENV — expected 'production' for deploy"
    fi
    if grep -q "^APP_DEBUG=false" "$ENV_FILE"; then
      pass "APP_DEBUG=false"
    else
      fail "APP_DEBUG is not 'false' — debug output exposed in production"
    fi
  fi
else
  fail ".env file not found"
fi

# ── 5. Route smoke tests — live server HTTP status ───────────────────────────
echo ""
echo "[ 5 ] Route smoke tests — http://$SERVER_IP (Host: $HOST)"
ROUTES=(
  "/"
  "/book"
  "/growth-services"
  "/access-plans"
  "/wordpress-support"
  "/web-design-development"
  "/ads-management"
  "/branding-print"
  "/onboarding/start"
  "/onboarding/done"
  "/sitemap.xml"
  "/robots.txt"
)
ROUTE_FAIL=0
if ! command -v curl &>/dev/null; then
  fail "curl not found — skipping route tests"
  ROUTE_FAIL=1
else
  for path in "${ROUTES[@]}"; do
    code=$(curl -s -o /dev/null -w "%{http_code}" -L --connect-timeout 8 \
      -H "Host: $HOST" "http://$SERVER_IP$path")
    if [ "$code" -eq 200 ]; then
      pass "$code  $path"
    else
      fail "$code  $path — expected 200"
      ROUTE_FAIL=1
    fi
  done
fi

# ── 6. Queue config check ─────────────────────────────────────────────────────
echo ""
echo "[ 6 ] Queue configuration"
if grep -q "^QUEUE_CONNECTION=database\|^QUEUE_CONNECTION=redis" "${ENV_FILE:-.env}" 2>/dev/null; then
  pass "QUEUE_CONNECTION is database/redis (emails will be queued)"
else
  fail "QUEUE_CONNECTION may be 'sync' — queued emails will fire inline in web requests"
fi

# ── 7. Key email view files present ───────────────────────────────────────────
echo ""
echo "[ 7 ] Email view files"
EMAIL_VIEWS=(
  "resources/views/emails/booking-confirmed.blade.php"
  "resources/views/emails/booking-pre-call.blade.php"
  "resources/views/emails/booking-follow-up.blade.php"
  "resources/views/emails/audit-what-to-prepare.blade.php"
  "resources/views/emails/onboarding-received.blade.php"
  "resources/views/emails/onboarding-step2.blade.php"
  "resources/views/emails/onboarding-step3.blade.php"
)
for f in "${EMAIL_VIEWS[@]}"; do
  if [ -f "$f" ]; then
    pass "$f"
  else
    fail "$f — MISSING"
  fi
done

# ── 8. Critical Blade views present ───────────────────────────────────────────
echo ""
echo "[ 8 ] Critical Blade views"
BLADE_VIEWS=(
  "resources/views/public/landing.blade.php"
  "resources/views/public/book.blade.php"
  "resources/views/public/onboarding-start.blade.php"
  "resources/views/public/onboarding-done.blade.php"
  "resources/views/public/growth-services.blade.php"
  "resources/views/public/access-plans.blade.php"
)
for f in "${BLADE_VIEWS[@]}"; do
  if [ -f "$f" ]; then
    pass "$f"
  else
    fail "$f — MISSING"
  fi
done

# ── Result ────────────────────────────────────────────────────────────────────
echo ""
echo "════════════════════════════════════"
if [ $FAILED -eq 0 ]; then
  echo "  ALL CHECKS PASSED — safe to deploy"
  echo "════════════════════════════════════"
  echo ""
  exit 0
else
  echo "  PREFLIGHT FAILED — fix issues above before deploying"
  echo "════════════════════════════════════"
  echo ""
  exit 1
fi
