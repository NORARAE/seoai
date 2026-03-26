<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingAvailability;
use Carbon\Carbon;
use Google\Client as GoogleClient;
use Google\Service\Calendar as GoogleCalendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use Google\Service\Calendar\ConferenceData;
use Google\Service\Calendar\CreateConferenceRequest;
use Google\Service\Calendar\ConferenceSolutionKey;
use Google\Service\Calendar\EventAttendee;
use Google\Service\Calendar\FreeBusyRequest;
use Google\Service\Calendar\FreeBusyRequestItem;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    private GoogleCalendar $calendar;
    private string $calendarId;

    public function __construct()
    {
        $credentialsPath = config('services.google.credentials');

        // Normalise shorthand paths that users may put in .env
        // e.g. "storage/google-credentials.json" or "/var/www/seoai/storage/google-credentials.json"
        if (! str_starts_with($credentialsPath, '/')) {
            // Relative to storage_path — strip leading "storage/" if present
            $rel = preg_replace('#^storage[/\\\\]#', '', $credentialsPath);
            $credentialsPath = storage_path($rel);
        }

        if (! file_exists($credentialsPath)) {
            Log::channel('booking')->error('Google Calendar: credentials file not found', [
                'path' => $credentialsPath,
            ]);
            throw new \RuntimeException("Google credentials file not found: {$credentialsPath}");
        }

        $calendarId = config('services.google.calendar_id');
        if (empty($calendarId)) {
            Log::channel('booking')->error('Google Calendar: GOOGLE_CALENDAR_ID is not set');
            throw new \RuntimeException('GOOGLE_CALENDAR_ID is not configured.');
        }

        $client = new GoogleClient();
        $client->setAuthConfig($credentialsPath);
        $client->addScope(GoogleCalendar::CALENDAR);
        $client->addScope(GoogleCalendar::CALENDAR_EVENTS);

        $this->calendar = new GoogleCalendar($client);
        $this->calendarId = $calendarId;
    }

    /**
     * Quickly verify the service can connect and the calendar is accessible.
     * Used by the test:integrations artisan command.
     */
    public function ping(): bool
    {
        try {
            $this->calendar->calendars->get($this->calendarId);
            Log::channel('booking')->info('Google Calendar ping: OK', ['calendar_id' => $this->calendarId]);
            return true;
        } catch (\Exception $e) {
            Log::channel('booking')->error('Google Calendar ping failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Get available booking slots for a given date.
     */
    public function getAvailableSlots(Carbon $date, int $durationMinutes): array
    {
        $dayOfWeek = (int) $date->dayOfWeek; // 0=Sun..6=Sat

        $availability = BookingAvailability::active()
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (! $availability) {
            return [];
        }

        $startOfDay = $date->copy()->setTimeFromTimeString($availability->start_time);
        $endOfDay = $date->copy()->setTimeFromTimeString($availability->end_time);

        // Fetch busy times from Google Calendar
        $busySlots = $this->getBusySlots($startOfDay, $endOfDay);

        // Fetch already-booked slots from DB
        $bookedSlots = Booking::where('preferred_date', $date->toDateString())
            ->whereIn('status', ['pending', 'confirmed'])
            ->get()
            ->map(fn (Booking $b) => [
                'start' => $date->copy()->setTimeFromTimeString($b->preferred_time),
                'end' => $date->copy()->setTimeFromTimeString($b->preferred_time)->addMinutes($b->consultType->duration_minutes),
            ])
            ->toArray();

        $allBusy = array_merge($busySlots, $bookedSlots);

        // Generate slots in 30-minute increments
        $slots = [];
        $cursor = $startOfDay->copy();

        while ($cursor->copy()->addMinutes($durationMinutes)->lte($endOfDay)) {
            $slotEnd = $cursor->copy()->addMinutes($durationMinutes);
            $isAvailable = true;

            foreach ($allBusy as $busy) {
                $busyStart = $busy['start'] instanceof Carbon ? $busy['start'] : Carbon::parse($busy['start']);
                $busyEnd = $busy['end'] instanceof Carbon ? $busy['end'] : Carbon::parse($busy['end']);

                if ($cursor->lt($busyEnd) && $slotEnd->gt($busyStart)) {
                    $isAvailable = false;
                    break;
                }
            }

            if ($isAvailable) {
                $slots[] = $cursor->format('H:i');
            }

            $cursor->addMinutes(30);
        }

        return $slots;
    }

    /**
     * Create a Google Calendar event with Meet link for a booking.
     */
    public function createBookingEvent(Booking $booking): array
    {
        $booking->loadMissing('consultType');

        $startDt = $booking->preferred_date
            ->copy()
            ->setTimeFromTimeString($booking->preferred_time);
        $endDt = $startDt->copy()->addMinutes($booking->consultType->duration_minutes);

        $event = new Event();
        $event->setSummary("{$booking->consultType->name} — {$booking->name} | seoaico.com");

        $description = "Consult: {$booking->consultType->name}\n"
            . "Duration: {$booking->consultType->duration_minutes} min\n"
            . "Client: {$booking->name}\n"
            . "Email: {$booking->email}\n";

        if ($booking->company) {
            $description .= "Company: {$booking->company}\n";
        }
        if ($booking->website) {
            $description .= "Website: {$booking->website}\n";
        }
        if ($booking->message) {
            $description .= "\nMessage:\n{$booking->message}\n";
        }

        $event->setDescription($description);

        $start = new EventDateTime();
        $start->setDateTime($startDt->toRfc3339String());
        $start->setTimeZone(config('app.timezone', 'America/Los_Angeles'));
        $event->setStart($start);

        $end = new EventDateTime();
        $end->setDateTime($endDt->toRfc3339String());
        $end->setTimeZone(config('app.timezone', 'America/Los_Angeles'));
        $event->setEnd($end);

        // Attendees
        $ownerAttendee = new EventAttendee();
        $ownerAttendee->setEmail(config('services.booking.owner_email', 'hello@seoaico.com'));
        $clientAttendee = new EventAttendee();
        $clientAttendee->setEmail($booking->email);
        $event->setAttendees([$ownerAttendee, $clientAttendee]);

        // Google Meet conference
        $confRequest = new CreateConferenceRequest();
        $confRequest->setRequestId('booking-' . $booking->id . '-' . time());
        $solutionKey = new ConferenceSolutionKey();
        $solutionKey->setType('hangoutsMeet');
        $confRequest->setConferenceSolutionKey($solutionKey);
        $confData = new ConferenceData();
        $confData->setCreateRequest($confRequest);
        $event->setConferenceData($confData);

        $created = $this->calendar->events->insert($this->calendarId, $event, [
            'conferenceDataVersion' => 1,
            'sendUpdates' => 'all',
        ]);

        $meetLink = '';
        if ($created->getConferenceData() && $created->getConferenceData()->getEntryPoints()) {
            foreach ($created->getConferenceData()->getEntryPoints() as $ep) {
                if ($ep->getEntryPointType() === 'video') {
                    $meetLink = $ep->getUri();
                    break;
                }
            }
        }

        return [
            'event_id' => $created->getId(),
            'meet_link' => $meetLink,
        ];
    }

    /**
     * Cancel/delete a Google Calendar event.
     */
    public function cancelEvent(string $eventId): bool
    {
        try {
            $this->calendar->events->delete($this->calendarId, $eventId, [
                'sendUpdates' => 'all',
            ]);
            return true;
        } catch (\Exception $e) {
            Log::channel('booking')->error('Failed to cancel calendar event', [
                'event_id' => $eventId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Query Google Calendar for busy periods.
     */
    private function getBusySlots(Carbon $start, Carbon $end): array
    {
        try {
            $request = new FreeBusyRequest();
            $request->setTimeMin($start->toRfc3339String());
            $request->setTimeMax($end->toRfc3339String());
            $request->setTimeZone(config('app.timezone', 'America/Los_Angeles'));

            $item = new FreeBusyRequestItem();
            $item->setId($this->calendarId);
            $request->setItems([$item]);

            $response = $this->calendar->freebusy->query($request);
            $calendars = $response->getCalendars();

            if (! isset($calendars[$this->calendarId])) {
                return [];
            }

            $busy = [];
            foreach ($calendars[$this->calendarId]->getBusy() as $period) {
                $busy[] = [
                    'start' => Carbon::parse($period->getStart()),
                    'end' => Carbon::parse($period->getEnd()),
                ];
            }

            return $busy;
        } catch (\Exception $e) {
            Log::channel('booking')->warning('Google Calendar freebusy query failed — allowing all slots', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }
}
