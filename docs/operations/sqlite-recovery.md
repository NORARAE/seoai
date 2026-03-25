# SQLite Recovery Runbook

## Symptoms observed
- Admin/Livewire requests returning `SQLSTATE[HY000]: General error: 11 database disk image is malformed`.
- Failures on reads and writes involving `page_content` and related crawl paths.
- Crawl jobs repeatedly retrying/failing due to malformed SQLite pages.

## Integrity check command
From project root:

```bash
sqlite3 database/database.sqlite "PRAGMA integrity_check;"
```

Healthy output should be:

```text
ok
```

Corrupted output includes errors such as:
- `database disk image is malformed`
- `btreeInitPage() returns error code 11`

## Backup procedure used
Before recovery, preserve the corrupted file:

```bash
ts=$(date +%Y%m%d_%H%M%S)
cp database/database.sqlite "database/database.sqlite.corrupt.$ts.bak"
```

## `.recover` workflow used
Create a logically recovered database from the corrupted source:

```bash
sqlite3 database/database.sqlite ".recover" | sqlite3 database/database.recovered.sqlite
```

## DB swap steps used
1. Validate recovered DB integrity:

```bash
sqlite3 database/database.recovered.sqlite "PRAGMA integrity_check;"
```

2. Keep a pre-swap copy and activate recovered DB:

```bash
mv database/database.sqlite database/database.sqlite.pre-recovery
mv database/database.recovered.sqlite database/database.sqlite
```

## Post-recovery verification steps
1. Confirm app-level access:

```bash
php artisan config:clear
php artisan tinker --execute="echo \Illuminate\Support\Facades\DB::table('page_content')->count();"
```

2. Confirm failing logical query path is healthy:

```bash
php artisan tinker --execute="echo \Illuminate\Support\Facades\DB::table('page_content')->join('url_inventory', 'page_content.url_id', '=', 'url_inventory.id')->where('url_inventory.site_id', 2)->where('page_content.word_count', '<', 250)->count();"
```

3. Confirm admin endpoint is not returning 500:

```bash
curl -s -o /dev/null -w "%{http_code}\n" http://127.0.0.1:8000/admin
```

(Expect `302` when auth redirect is active, or `200` when authenticated session is used.)

4. Restart worker/runtime and monitor logs:

```bash
composer runtime:backend
# or
php artisan queue:work --queue=crawl,generation,publishing,default --tries=3 --sleep=1 --timeout=120
```

```bash
tail -f storage/logs/laravel.log
```

## Notes
- Keep both backup files (`*.corrupt.*.bak` and `*.pre-recovery`) until confidence is high.
- If corruption reappears, move the active DB outside synced folders (see `docs/operations/sqlite-location.md`).
