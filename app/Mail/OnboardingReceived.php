<?php

namespace App\Mail;

use App\Models\Lead;
use App\Models\OnboardingSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OnboardingReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Lead $lead,
        public readonly OnboardingSubmission $submission,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Onboarding Received — We\'ll Be in Touch | seoaico.com',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.onboarding-received',
        );
    }
}
