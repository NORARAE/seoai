<?php

namespace App\Jobs;

use App\Mail\HighTierActivation;
use App\Mail\ScanPaidNudge;
use App\Mail\UpgradeAnalysisComplete;
use App\Mail\UpgradeImplementOffer;
use App\Mail\UpgradeStrategySession;
use App\Models\QuickScan;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendUpgradeFunnelEmailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $backoff = 30;

    public function __construct(
        public readonly int $scanId,
        public readonly int $userId,
        public readonly string $tierSlug,
    ) {
    }

    public function handle(): void
    {
        $scan = QuickScan::find($this->scanId);
        $user = User::find($this->userId);

        if (!$scan || !$user) {
            Log::warning('SendUpgradeFunnelEmailsJob: scan or user not found', [
                'scan_id' => $this->scanId,
                'user_id' => $this->userId,
            ]);
            return;
        }

        $email = $user->email;

        match ($this->tierSlug) {
            'scan-basic' => $this->handleScanBasic($scan, $email),
            'signal-expansion', 'structural-leverage' => $this->handleMidTier($scan, $email),
            'system-activation' => $this->handleHighTier($scan, $user),
            default => Log::info('SendUpgradeFunnelEmailsJob: unknown tier', ['tier' => $this->tierSlug]),
        };

        Log::info('SendUpgradeFunnelEmailsJob: dispatched', [
            'scan_id' => $this->scanId,
            'tier' => $this->tierSlug,
        ]);
    }

    private function handleScanBasic(QuickScan $scan, string $email): void
    {
        // $2 users: next-day nudge to upgrade to $99
        try {
            Mail::to($email)->later(
                now()->addDay(),
                new ScanPaidNudge($scan)
            );
        } catch (\Throwable $e) {
            Log::warning('SendUpgradeFunnelEmailsJob: ScanPaidNudge failed', [
                'scan_id' => $scan->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function handleMidTier(QuickScan $scan, string $email): void
    {
        $tierLabel = match ($this->tierSlug) {
            'signal-expansion' => 'Signal Expansion',
            'structural-leverage' => 'Structural Leverage',
            default => 'Analysis',
        };

        // Email 1: Immediate — analysis complete
        try {
            Mail::to($email)->queue(
                new UpgradeAnalysisComplete($scan, $tierLabel)
            );
        } catch (\Throwable $e) {
            Log::warning('SendUpgradeFunnelEmailsJob: UpgradeAnalysisComplete failed', [
                'scan_id' => $scan->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Email 2: Day 2 — implementation offer
        try {
            Mail::to($email)->later(
                now()->addDays(2),
                new UpgradeImplementOffer($scan, $this->tierSlug)
            );
        } catch (\Throwable $e) {
            Log::warning('SendUpgradeFunnelEmailsJob: UpgradeImplementOffer failed', [
                'scan_id' => $scan->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Email 3: Day 5 — strategy session
        try {
            Mail::to($email)->later(
                now()->addDays(5),
                new UpgradeStrategySession($scan)
            );
        } catch (\Throwable $e) {
            Log::warning('SendUpgradeFunnelEmailsJob: UpgradeStrategySession failed', [
                'scan_id' => $scan->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function handleHighTier(QuickScan $scan, User $user): void
    {
        // $489+ users: immediate activation email
        try {
            Mail::to($user->email)->queue(
                new HighTierActivation($user, $scan)
            );
        } catch (\Throwable $e) {
            Log::warning('SendUpgradeFunnelEmailsJob: HighTierActivation failed', [
                'scan_id' => $scan->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
