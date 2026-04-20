<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;

class UnsubscribeController extends Controller
{
    /**
     * Mark a lead as unsubscribed from marketing emails.
     *
     * Supports two URL patterns:
     *   /unsubscribe/{48-char-token}   — token-based (secure, from Lead-aware emails)
     *   /unsubscribe/{encoded-email}   — email-based (from scan/checkout emails without Lead access)
     *
     * Only sets email_marketing_opt_in = false on matched User records.
     * Does NOT disable accounts, revoke auth, or affect transactional emails.
     * No auth required — the token or email itself is the credential.
     */
    public function unsubscribe(string $token)
    {
        // URL-decode the token so %40-encoded email addresses also work
        $token = urldecode($token);

        // Determine lookup strategy: email-based if contains @, otherwise token-based
        if (str_contains($token, '@')) {
            $lead = Lead::where('email', $token)->first();
        } else {
            $lead = Lead::where('unsubscribe_token', $token)->first();
        }

        $alreadyUnsubscribed = $lead?->email_unsubscribed_at !== null;

        if ($lead && !$alreadyUnsubscribed) {
            $lead->update(['email_unsubscribed_at' => now()]);
        }

        $email = $lead?->email ?? (str_contains($token, '@') ? $token : null);

        // Also set email_marketing_opt_in = false on the matching User record (if any).
        // This does NOT touch login, approval, billing, or any other user attribute.
        if ($email) {
            User::where('email', $email)
                ->where('email_marketing_opt_in', true)
                ->update(['email_marketing_opt_in' => false]);
        }

        return view('public.unsubscribed', [
            'lead' => $lead,
            'email' => $email,
            'alreadyUnsubscribed' => $alreadyUnsubscribed,
        ]);
    }
}

