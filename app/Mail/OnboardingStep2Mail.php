<?php

namespace App\Mail;

use App\Models\Lead;
use App\Models\OnboardingSubmission;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OnboardingStep2Mail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public readonly Lead $lead,
        public readonly OnboardingSubmission $submission,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'What We\'re Evaluating — Your SEO Position Review | seoaico.com',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.onboarding-step2',
        );
    }
}
