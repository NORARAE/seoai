<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptureUtmParameters
{
    private const UTM_KEYS = [
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $utms = array_filter(
            $request->only(self::UTM_KEYS),
            fn($v) => is_string($v) && $v !== ''
        );

        if (! empty($utms)) {
            // Sanitize — strip tags, cap per-value length
            $sanitized = array_map(
                fn($v) => substr(strip_tags($v), 0, 128),
                $utms
            );
            session(['utm' => $sanitized]);
        }

        return $next($request);
    }
}
