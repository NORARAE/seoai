<?php

namespace App\Http\Middleware;

use App\Models\UserSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageVisit
{
    /** Route prefixes to skip entirely. */
    private const SKIP_PREFIXES = [
        'admin',
        'api',
        'livewire',
        '_ignition',
        'horizon',
        'telescope',
        'up',
    ];

    /** File extensions that are static assets — never record. */
    private const ASSET_EXTENSIONS = [
        'js',
        'css',
        'png',
        'jpg',
        'jpeg',
        'gif',
        'svg',
        'ico',
        'woff',
        'woff2',
        'ttf',
        'eot',
        'map',
        'webmanifest',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only record HTML page visits (GET requests that return 2xx HTML)
        if (!$this->shouldTrack($request, $response)) {
            return $response;
        }

        try {
            $this->record($request);
        } catch (\Throwable) {
            // Never break a page request due to analytics errors
        }

        return $response;
    }

    private function shouldTrack(Request $request, Response $response): bool
    {
        if (!$request->isMethod('GET')) {
            return false;
        }

        if (!$response->isSuccessful()) {
            return false;
        }

        $path = ltrim($request->path(), '/');

        foreach (self::SKIP_PREFIXES as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                return false;
            }
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($ext !== '' && in_array($ext, self::ASSET_EXTENSIONS, true)) {
            return false;
        }

        // Skip Livewire / AJAX sub-requests
        if ($request->header('X-Livewire') || $request->expectsJson()) {
            return false;
        }

        return true;
    }

    private function record(Request $request): void
    {
        $token = session()->getId();
        $page = '/' . ltrim($request->path(), '/');
        $ip = $request->ip();
        $ua = mb_strimwidth((string) $request->userAgent(), 0, 512);
        $now = now();

        // Resolve which intent flags should be set to true for this path.
        $intentUpdates = $this->intentFlags($page);

        $existing = UserSession::where('session_token', $token)->first();

        if ($existing) {
            $updates = [
                'last_page' => $page,
                'last_activity_at' => $now,
            ];

            // Only flip true — never overwrite an already-true flag back to false.
            // When a flag first flips, also stamp its timestamp if not already set.
            foreach ($intentUpdates as $col => $tsCol) {
                if (!$existing->$col) {
                    $updates[$col] = true;
                    if ($tsCol && !$existing->$tsCol) {
                        $updates[$tsCol] = $now;
                    }
                }
            }

            $existing->update($updates);
        } else {
            $create = [
                'session_token' => $token,
                'ip_address' => $ip,
                'user_agent' => $ua,
                'country' => null,
                'city' => null,
                'first_page' => $page,
                'last_page' => $page,
                'last_activity_at' => $now,
            ];

            // New session — set flags and their first-seen timestamps together.
            foreach ($intentUpdates as $col => $tsCol) {
                $create[$col] = true;
                if ($tsCol) {
                    $create[$tsCol] = $now;
                }
            }

            UserSession::create($create);
        }
    }

    /**
     * Returns intent flags to set for the given path.
     *
     * Keys are boolean column names; values are their paired timestamp column
     * (or null if there is no associated timestamp).
     *
     * @return array<string, string|null>
     */
    private function intentFlags(string $page): array
    {
        $flags = [];

        if ($page === '/book') {
            $flags['visited_book'] = 'first_book_at';
        }

        if ($page === '/onboarding/start') {
            $flags['visited_onboarding'] = 'first_onboarding_at';
        }

        return $flags;
    }
}

