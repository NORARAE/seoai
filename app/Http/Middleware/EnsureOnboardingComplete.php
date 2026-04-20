<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (
            $user
            && !$user->isPrivilegedStaff()
            && !$user->isFrontendDev()
            && $user->isApproved()
            && is_null($user->onboarding_completed_at)
        ) {
            // Avoid redirect loop if already on the setup route
            if ($request->routeIs('user.onboarding') || $request->routeIs('user.onboarding.store')) {
                return $next($request);
            }

            return redirect()->route('user.onboarding');
        }

        return $next($request);
    }
}
