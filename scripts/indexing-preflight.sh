#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
cd "$ROOT"

BASE_URL="${INDEX_BASE_URL:-https://seoaico.com}"
FAILED=0

pass() { echo "PASS: $1"; }
fail() { echo "FAIL: $1"; FAILED=1; }

require_file() {
  local file="$1"
  if [[ -f "$file" ]]; then
    pass "$file exists"
  else
    fail "$file missing"
  fi
}

require_contains() {
  local file="$1"
  local needle="$2"
  local label="$3"
  if grep -Fq "$needle" "$file"; then
    pass "$label"
  else
    fail "$label (missing: $needle)"
  fi
}

echo ""
echo "========================================="
echo "SEOAIco Indexing Preflight"
echo "Base URL: $BASE_URL"
echo "========================================="

echo ""
echo "[1] Asset model and required files"
require_file "public/robots.txt"
require_file "public/llms.txt"
require_file "app/Http/Controllers/MarketingSitemapController.php"
require_contains "routes/web.php" "Route::get('/sitemap.xml'" "sitemap.xml route is registered"
require_contains "routes/web.php" "Route::get('/sitemaps/marketing-{cluster}.xml'" "cluster sitemap route is registered"

echo ""
echo "[2] HTTP checks (fail loudly on 4xx/5xx)"
if command -v curl >/dev/null 2>&1; then
  for path in "/sitemap.xml" "/robots.txt" "/llms.txt"; do
    code=$(curl -s -o /dev/null -w "%{http_code}" --connect-timeout 10 "$BASE_URL$path" || true)
    if [[ "$code" == "200" ]]; then
      pass "$path returns 200"
    else
      fail "$path returned HTTP $code"
    fi
  done
else
  fail "curl is required for HTTP checks"
fi

echo ""
echo "[3] robots.txt policy checks"
ROBOTS="public/robots.txt"
require_contains "$ROBOTS" "User-agent: *" "robots has global agent"
require_contains "$ROBOTS" "Sitemap: https://seoaico.com/sitemap.xml" "robots includes sitemap line"

for disallow in \
  "Disallow: /admin" \
  "Disallow: /dashboard" \
  "Disallow: /login" \
  "Disallow: /register" \
  "Disallow: /pending-approval" \
  "Disallow: /setup" \
  "Disallow: /checkout/" \
  "Disallow: /quick-scan/result" \
  "Disallow: /quick-scan/upgrade" \
  "Disallow: /results/" \
  "Disallow: /onboarding/" \
  "Disallow: /booking/" \
  "Disallow: /preview/"; do
  require_contains "$ROBOTS" "$disallow" "robots blocks $disallow"
done

echo ""
echo "[4] llms.txt safety checks"
LLMS="public/llms.txt"
urls=$(grep -Eo 'https://seoaico\.com[^ )"<]*' "$LLMS" | sort -u || true)
if [[ -z "$urls" ]]; then
  fail "llms.txt does not contain public URLs"
else
  pass "llms.txt contains public URLs"
fi

private_pattern='^/(admin|dashboard|login|register|pending-approval|setup|checkout|quick-scan/result|quick-scan/status|quick-scan/cancelled|quick-scan/upgrade|results|onboarding|booking|preview|api|unsubscribe|email/click)'

while IFS= read -r url; do
  [[ -z "$url" ]] && continue
  if [[ "$url" != https://seoaico.com* ]]; then
    fail "llms contains non-canonical domain URL: $url"
    continue
  fi

  path="${url#https://seoaico.com}"
  [[ -z "$path" ]] && path="/"
  if [[ "$path" =~ $private_pattern ]]; then
    fail "llms contains private/blocked URL: $url"
  fi
done <<< "$urls"

echo ""
echo "[5] Key noindex template checks"
noindex_files=(
  "resources/views/public/quick-scan-result.blade.php"
  "resources/views/public/quick-scan-processing.blade.php"
  "resources/views/public/scan-start.blade.php"
  "resources/views/public/scan-process.blade.php"
  "resources/views/public/scan-preview.blade.php"
  "resources/views/auth/login.blade.php"
  "resources/views/auth/register.blade.php"
  "resources/views/pending-approval.blade.php"
)

for file in "${noindex_files[@]}"; do
  if [[ ! -f "$file" ]]; then
    fail "$file missing"
    continue
  fi

  if grep -Eqi 'meta[^>]+name="robots"[^>]+noindex' "$file"; then
    pass "$file contains robots noindex"
  else
    fail "$file missing robots noindex"
  fi
done

echo ""
echo "========================================="
if [[ "$FAILED" -eq 0 ]]; then
  echo "INDEXING PREFLIGHT PASSED"
  echo "========================================="
  exit 0
else
  echo "INDEXING PREFLIGHT FAILED"
  echo "========================================="
  exit 1
fi
