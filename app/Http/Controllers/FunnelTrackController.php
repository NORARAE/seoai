<?php

namespace App\Http\Controllers;

use App\Models\FunnelEvent;
use Illuminate\Http\Request;

class FunnelTrackController extends Controller
{
    /**
     * POST /api/v1/track
     * Lightweight endpoint for client-side funnel events.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event' => [
                'required',
                'string',
                'in:homepage_cta_click,deployment_cta_click,dashboard_cta_click,checkout_cancelled',
            ],
            'metadata' => ['nullable', 'array'],
            'metadata.*' => ['string', 'max:255'],
        ]);

        FunnelEvent::fire(
            $validated['event'],
            metadata: $validated['metadata'] ?? [],
        );

        return response()->json(['ok' => true], 200);
    }
}
