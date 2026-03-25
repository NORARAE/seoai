# Crawl Runtime Operations (Local)

## Purpose
Use a single command to run local SEOAIco runtime with crawl support enabled:
- Laravel app server
- queue worker (including crawl queue)
- scheduler loop
- Vite frontend dev server (optional)
- Laravel log tailing

## Start local runtime
From the project root:

```bash
composer runtime
```

This starts:
- `php artisan serve`
- `php artisan queue:work --queue=crawl,generation,publishing,default --tries=3 --sleep=1 --timeout=120`
- `php artisan schedule:work`
- `npm run dev`
- `php artisan pail --timeout=0`

## Start runtime without frontend
For crawl/backend sessions where Vite is not needed:

```bash
composer runtime:backend
```

## Start crawl processing only (manual fallback)
If you only need queue processing:

```bash
php artisan queue:work --queue=crawl,generation,publishing,default --tries=3 --sleep=1 --timeout=120
```

## Resume a stuck crawl
1. Confirm active site and queue counts in admin (`/admin`).
2. Trigger dispatch for the target site:

```bash
php artisan crawl:dispatch --site_id=<SITE_ID> --limit=50
```

3. Ensure a queue worker is running (via `composer runtime` or manual worker command).

## What is still manual
- Selecting the correct active site in admin context.
- Manually triggering `crawl:dispatch` for known stuck site IDs when needed.
- Restarting runtime processes after machine sleep/reboot.

## Healthy crawl signals
A healthy crawl typically shows:
- `crawl_status=processing` while backlog exists.
- `queued` count trending down over time.
- `completed` count increasing steadily.
- periodic `Crawl dispatch released batch` and `Crawl queue item processed` log entries.
- eventual transition to `crawl_status=completed` with `queued=0` and `processing=0`.

## Production process management (recommendation only)
### Supervisor
- Good fit for current stack.
- Easy to run long-lived `queue:work` and `schedule:work` processes.
- Minimal architecture changes.

### systemd
- Also viable, especially on single-host Linux deployments.
- Good native service control, restart policies, and logs.
- Slightly more host-specific operational setup.

### Laravel Horizon
- Best when using Redis queues and needing queue dashboards/scaling controls.
- Current project uses `database` queue by default, so Horizon is not immediate plug-and-play.

### Recommended next step
Use **Supervisor** first for production hardening in this codebase, then consider Horizon later if/when queue backend moves to Redis.

See [docs/operations/supervisor-hardening.md](docs/operations/supervisor-hardening.md) for a ready-to-use baseline config and rollout steps.
