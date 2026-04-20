<?php

namespace App\Jobs;

use App\Mail\InactiveUserNudge;
use App\Models\Lead;
use App\Models\QuickScan;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendInactiveUserNudgeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $backoff = 30;

    /**
     * Find users who paid for a scan but took no further action
     * (no upgrade within 3 days) and send a nudge email.
     */
    public function handle(): void
    {
        $inactiveScans = QuickScan::where('paid', true)
            ->where('status', QuickScan::STATUS_SCANNED)
            ->whereNull('upgrade_plan')
            ->where('scanned_at', '<=', now()->subDays(3))
            ->where('scanned_at', '>=', now()->subDays(4)) // 3-4 day window
            ->where(function ($q) {
                $q->whereNull('inactive_nudge_sent')
                    ->orWhere('inactive_nudge_sent', false);
            })
            ->get();

        $count = 0;

        foreach ($inactiveScans as $scan) {
            try {
                // Skip if the recipient has unsubscribed (Lead) or opted out of marketing (User)
                $lead = Lead::where('email', $scan->email)->first();
                if ($lead?->email_unsubscribed_at) {
                    continue;
                }
                if (User::where('email', $scan->email)->where('email_marketing_opt_in', false)->exists()) {
                    continue;
                }

                Mail::to($scan->email)->queue(new InactiveUserNudge($scan));
                $scan->update(['inactive_nudge_sent' => true]);
                $count++;
            } catch (\Throwable $e) {
                Log::warning('SendInactiveUserNudgeJob: failed for scan', [
                    'scan_id' => $scan->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('SendInactiveUserNudgeJob: completed', ['sent' => $count]);
    }
}
