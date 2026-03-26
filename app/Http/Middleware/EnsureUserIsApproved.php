<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Must be authenticated first — let the auth middleware handle unauthenticated.
        if (! $user) {
            return redirect()->route('login');
        }

        // Privileged staff always bypass the approval gate.
        if ($user->isPrivilegedStaff()) {
            return $next($request);
        }

        // Approved clients may proceed.
        if ($user->isApproved()) {
            return $next($request);
        }

        // Unapproved client — send to pending approval page.
        // Avoid redirect loop: if already on the pending page, allow it through.
        if ($request->routeIs('pending-approval')) {
            return $next($request);
        }

        return redirect()->route('pending-approval');
    }
}
