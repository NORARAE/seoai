<?php

namespace App\Http\Middleware;

use App\Support\FrontendDevAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Enforces URL-level access restrictions for the `frontend_dev` role.
 *
 * This middleware is registered in the Filament admin panel and runs on every
 * /admin/* request. It provides the hard security guarantee that navigation
 * hiding alone cannot — even if a frontend_dev user manually types a URL
 * they are not allowed to access, they will be denied.
 *
 * Allowed paths are configured centrally in App\Support\FrontendDevAccess.
 */
class FrontendDevAccessMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Only restrict frontend_dev users; all other roles pass through.
        if (!FrontendDevAccess::isRestricted()) {
            return $next($request);
        }

        // Derive the first path segment after /admin/.
        // e.g. /admin/seo-marketing-pages/123 → 'seo-marketing-pages'
        //      /admin                          → ''
        $adminPrefix = config('filament.path', 'admin');
        $path = ltrim($request->path(), '/');
        $afterAdmin = ltrim(substr($path, strlen($adminPrefix)), '/');
        $firstSegment = explode('/', $afterAdmin)[0] ?? '';

        // Allow if this path segment is in the permitted list.
        if (FrontendDevAccess::allowsPath($firstSegment)) {
            return $next($request);
        }

        // Block: redirect to dashboard with a one-time warning message.
        return redirect()
            ->to('/' . $adminPrefix)
            ->with('frontend_dev_blocked', 'Your account has restricted access. That area is not available to you.');
    }
}
