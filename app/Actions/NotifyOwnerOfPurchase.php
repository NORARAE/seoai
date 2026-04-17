<?php

namespace App\Actions;

use App\Mail\OwnerPurchaseNotification;
use App\Models\QuickScan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyOwnerOfPurchase
{
    public function execute(QuickScan $scan, string $tier, int $amountCents): void
    {
        if ($scan->owner_notified_at) {
            return;
        }

        $recipient = config('services.booking.owner_email', 'hello@seoaico.com');

        try {
            Mail::to($recipient)->send(
                new OwnerPurchaseNotification($scan, $tier, $amountCents)
            );

            $scan->update(['owner_notified_at' => now()]);
        } catch (\Throwable $e) {
            Log::error('Owner purchase notification failed', [
                'scan_id' => $scan->id,
                'tier' => $tier,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
