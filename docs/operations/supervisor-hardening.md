# Supervisor Hardening Runbook

Use Supervisor to keep queue workers and the Laravel scheduler alive across deploys, crashes, and host restarts.

## 1) Install config

Copy [deploy/supervisor/seoai-runtime.conf](deploy/supervisor/seoai-runtime.conf) to your host Supervisor config directory.

Example (Ubuntu/Debian):

```bash
sudo cp deploy/supervisor/seoai-runtime.conf /etc/supervisor/conf.d/seoai-runtime.conf
```

Update these values for your host before enabling:
- `command` PHP binary path
- `directory` app root
- `user`
- `stdout_logfile` / `stderr_logfile` paths
- queue backend in `environment` if not `database`

## 2) Enable and start

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start seoai-queue:* seoai-scheduler
sudo supervisorctl status
```

## 3) Runtime profile in this config

- Queue worker command:
  - `queue:work --queue=crawl,generation,publishing,default --tries=3 --sleep=1 --timeout=120 --memory=512 --max-time=3600`
- Scheduler command:
  - `schedule:work`

Why these flags:
- `--memory=512` avoids crawl parser OOM at default `128M`.
- `--max-time=3600` forces periodic worker recycle to mitigate memory growth.
- Queue order keeps crawl high priority while still servicing generation/publishing.

## 4) Operational checks

```bash
sudo supervisorctl status
tail -f storage/logs/supervisor-queue.log
tail -f storage/logs/supervisor-queue-error.log
```

If crawl appears stalled, also verify app logs and queue depth:

```bash
php artisan tinker --execute="echo json_encode(['jobs'=>\Illuminate\Support\Facades\DB::table('jobs')->selectRaw('queue,count(*) as count')->groupBy('queue')->get()->toArray()], JSON_PRETTY_PRINT);"
```