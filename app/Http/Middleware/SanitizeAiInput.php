<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SanitizeAiInput
{
    public function handle(Request $request, Closure $next)
    {
        // Only apply to AI routes
        if ($request->is('ai/*')) {

            $input = $request->input('message');

            if ($input) {

                // Remove email addresses
                $input = preg_replace('/\b[\w\.-]+@[\w\.-]+\.\w+\b/', '[email]', $input);

                // Remove long numeric sequences (phones, IDs)
                $input = preg_replace('/\b\d{7,}\b/', '[number]', $input);

                // Normalize whitespace
                $input = trim(preg_replace('/\s+/', ' ', $input));

                // Limit length to prevent abuse
                $input = substr($input, 0, 2000);

                $request->merge([
                    'message' => $input,
                ]);
            }
        }

        return $next($request);
    }
}
