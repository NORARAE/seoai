<?php

namespace App\Http\Controllers;

use App\Models\BookingAvailability;
use App\Services\BookingRescheduleService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingManageController extends Controller
{
    public function __construct(
        private readonly BookingRescheduleService $rescheduleService,
    ) {
    }

    /**
     * Show the self-service booking management page.
     */
    public function show(string $token): View
    {
        $booking = $this->rescheduleService->findByToken($token);
        $canReschedule = $this->rescheduleService->canReschedule($booking);
        $availableDays = BookingAvailability::active()->pluck('day_of_week')->toArray();

        return view('booking.manage', compact('booking', 'canReschedule', 'availableDays'));
    }

    /**
     * Process a reschedule request submitted from the manage page.
     */
    public function reschedule(Request $request, string $token): RedirectResponse
    {
        $booking = $this->rescheduleService->findByToken($token);

        if (!$this->rescheduleService->canReschedule($booking)) {
            return redirect()->route('booking.manage', $token)
                ->with('error', 'This booking can no longer be rescheduled (within ' . 6 . ' hours of the appointment).');
        }

        $validated = $request->validate([
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'required|date_format:H:i',
        ]);

        $error = $this->rescheduleService->reschedule(
            $booking,
            $validated['preferred_date'],
            $validated['preferred_time'],
        );

        if ($error) {
            return redirect()->route('booking.manage', $token)->with('error', $error);
        }

        return redirect()->route('booking.manage', $token)
            ->with('success', 'Your booking has been rescheduled. A confirmation email has been sent.');
    }

    /**
     * Cancel the booking from the manage page.
     */
    public function cancel(string $token): RedirectResponse
    {
        $booking = $this->rescheduleService->findByToken($token);

        if ($booking->isCancelled()) {
            return redirect()->route('booking.manage', $token)
                ->with('error', 'This booking is already cancelled.');
        }

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return redirect()->route('booking.manage', $token)
            ->with('success', 'Your booking has been cancelled.');
    }
}
