# SQLite Location Strategy (Local Development)

## Why move SQLite outside Dropbox sync
The repository lives in a Dropbox-synced folder. SQLite is file-based and sensitive to concurrent file-level interference during active writes (queue workers, scheduler, app server). Sync clients can increase risk of:
- partial-write visibility during sync cycles,
- lock contention and journal/WAL instability,
- corruption after abrupt process termination while sync is active.

For crawl-heavy local runtime, this is a practical corruption risk.

## Current local strategy
- Keep project code in Dropbox if desired.
- Keep the **active SQLite DB file outside Dropbox** in a non-synced local path.
- Keep in-repo DB copies as backup/reference only.

### Active DB path used on this machine

```text
/Users/noragenetti/Library/Application Support/seoai/database/database.sqlite
```

## `.env` configuration
Set local `.env`:

```dotenv
DB_CONNECTION=sqlite
DB_DATABASE="/Users/noragenetti/Library/Application Support/seoai/database/database.sqlite"
```

Then clear config cache:

```bash
php artisan config:clear
```

## How to set this up on another machine
1. Create local non-synced folder:

```bash
mkdir -p "$HOME/Library/Application Support/seoai/database"
```

2. Create/copy SQLite file:

```bash
cp database/database.sqlite "$HOME/Library/Application Support/seoai/database/database.sqlite"
# or
# touch "$HOME/Library/Application Support/seoai/database/database.sqlite"
# php artisan migrate
```

3. Update `.env` with machine-local absolute path.
4. Run `php artisan config:clear`.

## Cautions
- Do not run active SQLite DB from cloud-synced folders for write-heavy workloads.
- Avoid multiple concurrent queue workers against SQLite unless necessary.
- Use graceful shutdown for workers to reduce journal/lock issues.

## Likely root cause assessment
Most likely factors in this incident:
1. Active SQLite file inside Dropbox-synced directory (high likelihood).
2. High write concurrency from queue jobs + scheduler + crawl flow (medium-high).
3. Prior lock pressure (`database is locked` events) and interrupted worker cycles (medium).

This combination materially increases corruption risk for SQLite.

## Medium-term database recommendation (not implemented yet)
### Recommended target: PostgreSQL
PostgreSQL is the better next target for this app because:
- stronger concurrency handling for queue-backed write-heavy workloads,
- robust transactional behavior under multi-process workers,
- better long-term fit for SEOAIco’s growing operational and analytics queries.

### Triggers to migrate soon
- recurring SQLite lock/corruption events,
- sustained multi-worker queue throughput needs,
- larger crawl/page datasets with frequent updates,
- increased admin/reporting query complexity.

### Areas beginning to outgrow SQLite
- crawl queue + discovery write path,
- `page_content` and related extraction persistence,
- high-frequency dashboard polling with concurrent queue updates.
