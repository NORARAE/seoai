<?php

namespace App\Http\Controllers;

use App\Models\UserSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    /**
     * POST /track/modal-open
     *
     * Called client-side when the booking modal opens. Sets opened_booking_modal = true
     * on the current visitor's session row. Never overwrites false back over true.
     */
    public function modalOpen(Request $request): JsonResponse
    {
        try {
            $token = session()->getId();

            $session = UserSession::where('session_token', $token)
                ->where('opened_booking_modal', false)
                ->first();

            if ($session) {
                $updates = ['opened_booking_modal' => true];
                if (!$session->first_modal_open_at) {
                    $updates['first_modal_open_at'] = now();
                }
                $session->update($updates);
            }
        } catch (\Throwable) {
            // Silently absorb — tracking must never break the UX
        }

        return response()->json(['ok' => true]);
    }
}
