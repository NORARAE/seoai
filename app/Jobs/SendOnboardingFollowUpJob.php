<?php

namespace App\Jobs;

use App\Mail\OnboardingStep2Mail;
use App\Mail\OnboardingStep3Mail;
use App\Models\Lead;
use App\Models\OnboardingSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOnboardingFollowUpJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $backoff = 120;

    /**
     * @param  int  $leadId
     * @param  int  $submissionId
     * @param  int  $step  2 or 3
     */
    public function __construct(
        public readonly int $leadId,
        public readonly int $submissionId,
        public readonly int $step,
    ) {}

    public function handle(): void
    {
        $lead = Lead::find($this->leadId);
        $submission = OnboardingSubmission::find($this->submissionId);

        if (! $lead || ! $submission) {
            Log::channel('booking')->warning('OnboardingFollowUp skipped — record not found', [
                'lead_id' => $this->leadId,
                'submission_id' => $this->submissionId,
                'step' => $this->step,
            ]);
            return;
        }

        if (! $lead->email) {
            Log::channel('booking')->warning('OnboardingFollowUp skipped — no email on lead', [
                'lead_id' => $this->leadId,
                'step' => $this->step,
            ]);
            return;
        }

        $mailable = match ($this->step) {
            2 => new OnboardingStep2Mail($lead, $submission),
            3 => new OnboardingStep3Mail($lead, $submission),
            default => null,
        };

        if (! $mailable) {
            Log::channel('booking')->error('OnboardingFollowUp — unknown step', ['step' => $this->step]);
            return;
        }

        try {
            Mail::to($lead->email)->send($mailable);

            Log::channel('booking')->info('Onboarding follow-up sent', [
                'lead_id' => $lead->id,
                'step' => $this->step,
                'email' => $lead->email,
            ]);
        } catch (\Exception $e) {
            Log::channel('booking')->error('Onboarding follow-up email failed', [
                'lead_id' => $lead->id,
                'step' => $this->step,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
