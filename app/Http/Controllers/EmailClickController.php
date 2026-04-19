<?php

namespace App\Http\Controllers;

use App\Models\FunnelEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailClickController extends Controller
{
    /**
     * GET /email/click?t={type}&u={user_id}&s={scan_id}&d={destination}
     *
     * Logs the email CTA click then redirects to the destination URL.
     * All parameters are optional except destination.
     */
    public function __invoke(Request $request)
    {
        $destination = $request->query('d', '/dashboard');
        $emailType = $request->query('t', 'unknown');
        $userId = $request->query('u');
        $scanId = $request->query('s');

        // Validate destination is a safe relative path (no open redirect)
        if (!str_starts_with($destination, '/')) {
            $destination = '/dashboard';
        }

        FunnelEvent::fire(
            eventName: FunnelEvent::EMAIL_CTA_CLICK,
            userId: $userId ? (int) $userId : null,
            scanId: $scanId ? (int) $scanId : null,
            metadata: [
                'email_type' => $emailType,
                'destination' => $destination,
            ],
        );

        return redirect($destination);
    }
}
