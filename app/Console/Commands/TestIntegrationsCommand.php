<?php

namespace App\Console\Commands;

use App\Mail\NewAccountInquiryAdminMail;
use App\Mail\NewAccountInquiryWelcomeMail;
use App\Models\Inquiry;
use App\Services\GoogleCalendarService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestIntegrationsCommand extends Command
{
    protected $signature = 'test:integrations
                            {--skip-mail   : Skip the email send tests}
                            {--skip-cal    : Skip the Google Calendar test}
                            {--email=      : Override the test email recipient}';

    protected $description = 'Verify Google Calendar + mail integrations are wired up correctly';

    public function handle(): int
    {
        $this->line('');
        $this->line('  <fg=yellow>SEOAIco — Integration Checks</>');
        $this->line('  ' . str_repeat('─', 42));

        $allOk = true;

        // ── Credential file ──────────────────────────────────────
        $credPath = config('services.google.credentials');
        $this->line('');
        $this->comment('  [1/4] Google credential file');
        if (file_exists($credPath)) {
            $this->line("        <fg=green>✓</> Found: {$credPath}");
        } else {
            $this->line("        <fg=red>✗</> Missing: {$credPath}");
            $allOk = false;
        }

        // ── Calendar ID ──────────────────────────────────────────
        $calId = config('services.google.calendar_id', '');
        $this->line('');
        $this->comment('  [2/4] GOOGLE_CALENDAR_ID');
        if (! empty($calId)) {
            $this->line("        <fg=green>✓</> Set: " . substr($calId, 0, 30) . '…');
        } else {
            $this->line('        <fg=red>✗</> Not set — add GOOGLE_CALENDAR_ID to .env');
            $allOk = false;
        }

        // ── Google Calendar ping ─────────────────────────────────
        $this->line('');
        $this->comment('  [3/4] Google Calendar API ping');
        if ($this->option('skip-cal')) {
            $this->line('        <fg=yellow>–</> Skipped (--skip-cal)');
        } elseif (! config('services.google.calendar_enabled', false)) {
            $this->line('        <fg=yellow>–</> GOOGLE_CALENDAR_ENABLED is false — skipping');
        } else {
            try {
                $svc = app(GoogleCalendarService::class);
                $ok = $svc->ping();
                if ($ok) {
                    $this->line('        <fg=green>✓</> Calendar API reachable');
                } else {
                    $this->line('        <fg=red>✗</> Ping returned false — check credentials / calendar ID');
                    $allOk = false;
                }
            } catch (\Throwable $e) {
                $this->line('        <fg=red>✗</> Exception: ' . $e->getMessage());
                $allOk = false;
            }
        }

        // ── Mail send ────────────────────────────────────────────
        $this->line('');
        $this->comment('  [4/4] Mail delivery');
        if ($this->option('skip-mail')) {
            $this->line('        <fg=yellow>–</> Skipped (--skip-mail)');
        } else {
            $to = $this->option('email')
                ?: config('services.inquiry.recipient_email', 'hello@seoaico.com');

            // Build a throw-away Inquiry object (not saved to DB)
            $inquiry = new Inquiry([
                'name'    => 'Test User',
                'company' => 'Artisan Test',
                'email'   => $to,
                'type'    => 'agency',
                'tier'    => 'starter',
                'message' => 'This is a test message sent by php artisan test:integrations.',
            ]);
            $inquiry->id         = 0;
            $inquiry->ip_address = '127.0.0.1';
            $inquiry->created_at = now();

            $this->line("        Sending to: {$to}");
            $this->line("        Mailer:      " . config('mail.default'));

            try {
                Mail::to($to)->send(new NewAccountInquiryWelcomeMail($inquiry));
                $this->line('        <fg=green>✓</> Welcome email sent');
            } catch (\Throwable $e) {
                $this->line('        <fg=red>✗</> Welcome mail failed: ' . $e->getMessage());
                $allOk = false;
            }

            try {
                Mail::to($to)->send(new NewAccountInquiryAdminMail($inquiry));
                $this->line('        <fg=green>✓</> Admin notification email sent');
            } catch (\Throwable $e) {
                $this->line('        <fg=red>✗</> Admin mail failed: ' . $e->getMessage());
                $allOk = false;
            }
        }

        // ── Summary ──────────────────────────────────────────────
        $this->line('');
        $this->line('  ' . str_repeat('─', 42));
        if ($allOk) {
            $this->line('  <fg=green>All checks passed.</>');
        } else {
            $this->line('  <fg=red>One or more checks failed — review the output above.</>');
        }
        $this->line('');

        return $allOk ? self::SUCCESS : self::FAILURE;
    }
}
