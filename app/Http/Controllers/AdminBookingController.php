<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingAvailability;
use App\Models\ConsultType;
use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminBookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with('consultType')->latest();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }
        if ($typeId = $request->query('type')) {
            $query->where('consult_type_id', $typeId);
        }

        $bookings = $query->paginate(25);
        $types = ConsultType::orderBy('sort_order')->get();

        return view('admin.bookings.index', compact('bookings', 'types'));
    }

    public function show(Booking $booking)
    {
        $booking->load('consultType');

        return view('admin.bookings.show', compact('booking'));
    }

    public function cancel(Booking $booking)
    {
        if ($booking->google_event_id) {
            try {
                app(GoogleCalendarService::class)->cancelEvent($booking->google_event_id);
            } catch (\Exception $e) {
                Log::channel('booking')->error('Admin cancel: Google event delete failed', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return redirect()->route('admin.bookings.index')
            ->with('success', "Booking #{$booking->id} cancelled.");
    }

    public function availability()
    {
        $slots = BookingAvailability::orderBy('day_of_week')->get();

        return view('admin.bookings.availability', compact('slots'));
    }

    public function saveAvailability(Request $request)
    {
        $validated = $request->validate([
            'slots' => 'required|array',
            'slots.*.day_of_week' => 'required|integer|between:0,6',
            'slots.*.start_time' => 'required|date_format:H:i',
            'slots.*.end_time' => 'required|date_format:H:i|after:slots.*.start_time',
            'slots.*.is_active' => 'boolean',
        ]);

        foreach ($validated['slots'] as $slot) {
            BookingAvailability::updateOrCreate(
                ['day_of_week' => $slot['day_of_week']],
                [
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'],
                    'is_active' => $slot['is_active'] ?? false,
                ]
            );
        }

        return redirect()->route('admin.bookings.availability')
            ->with('success', 'Availability updated.');
    }

    public function consultTypes()
    {
        $types = ConsultType::orderBy('sort_order')->get();

        return view('admin.bookings.consult-types', compact('types'));
    }
}
