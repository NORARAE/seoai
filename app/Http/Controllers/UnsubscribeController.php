<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;

class UnsubscribeController extends Controller
{
    /**
     * Mark a lead as unsubscribed from marketing emails.
     *
     * Token is a 48-character random string auto-generated on Lead::creating.
     * No auth required — the token itself is the credential.
     */
    public function unsubscribe(string $token)
    {
        $lead = Lead::where('unsubscribe_token', $token)->firstOrFail();

        if (!$lead->email_unsubscribed_at) {
            $lead->update(['email_unsubscribed_at' => now()]);
        }

        return view('public.unsubscribed', ['lead' => $lead]);
    }
}
