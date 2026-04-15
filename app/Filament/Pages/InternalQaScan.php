<?php

namespace App\Filament\Pages;

use App\Jobs\RunQuickScanJob;
use App\Models\QuickScan;
use App\Services\QuickScanService;
use BackedEnum;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InternalQaScan extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBeaker;
    protected static ?string $navigationLabel = 'Internal QA Scan';
    protected static string|\UnitEnum|null $navigationGroup = 'Operations';
    protected static ?int $navigationSort = 7;
    protected string $view = 'filament.pages.internal-qa-scan';
    protected static ?string $title = 'Internal QA Scan';
    protected static ?string $slug = 'internal-qa-scan';

    public ?string $url = '';
    public ?string $email = '';
    public bool $send_emails = false;
    public bool $run_async = false;

    // Result state
    public ?int $lastScanId = null;
    public ?int $lastScore = null;
    public ?string $lastStatus = null;

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && $user->canApproveUsers();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('url')
                    ->label('Website URL')
                    ->placeholder('yoursite.com')
                    ->required()
                    ->maxLength(500)
                    ->helperText('Enter any domain or full URL. HTTPS is added automatically.'),

                TextInput::make('email')
                    ->label('Email Address')
                    ->placeholder(Auth::user()?->email ?? 'test@seoaico.com')
                    ->email()
                    ->maxLength(255)
                    ->helperText('Leave blank to use your account email.'),

                Checkbox::make('send_emails')
                    ->label('Send email sequence (result + drip)')
                    ->helperText('Default: OFF for QA. Enable to test the full email flow.'),

                Checkbox::make('run_async')
                    ->label('Run scan asynchronously via job queue')
                    ->helperText('Default: OFF (synchronous). Enable to test the webhook/processing flow.'),
            ]);
    }

    public function runScan(): void
    {
        $user = Auth::user();

        if (!$user || !$user->canApproveUsers()) {
            Notification::make()
                ->danger()
                ->title('Access denied')
                ->send();
            return;
        }

        // Normalize URL
        $url = trim($this->url);
        if ($url !== '' && !preg_match('#^https?://#i', $url)) {
            $url = 'https://' . $url;
        }
        $url = rtrim($url, '/');

        $email = trim($this->email) ?: $user->email;

        // Create the QA scan record
        $scan = QuickScan::create([
            'email' => strtolower($email),
            'url' => $url,
            'url_input' => trim($this->url),
            'ip_address' => request()->ip(),
            'user_id' => $user->id,
            'paid' => true,
            'status' => QuickScan::STATUS_PAID,
            'is_internal' => true,
            'source' => 'admin_bypass',
            'suppress_emails' => !$this->send_emails,
            'initiated_by' => $user->id,
        ]);

        Log::info('InternalQaScan: scan initiated', [
            'scan_id' => $scan->id,
            'url' => $url,
            'email' => $email,
            'initiated_by' => $user->id,
            'user_name' => $user->name,
            'send_emails' => $this->send_emails,
            'async' => $this->run_async,
        ]);

        if ($this->run_async) {
            // Dispatch to queue — mirrors webhook path
            RunQuickScanJob::dispatch($scan->id);

            $this->lastScanId = $scan->id;
            $this->lastStatus = 'queued';
            $this->lastScore = null;

            Notification::make()
                ->success()
                ->title('Scan queued')
                ->body("Scan #{$scan->id} dispatched to queue. Check the result page or processing view.")
                ->send();
        } else {
            // Run synchronously — mirrors the direct result path
            try {
                $scanner = app(QuickScanService::class);
                $result = $scanner->scan($url);

                $scan->update([
                    'score' => $result['score'],
                    'issues' => $result['issues'],
                    'strengths' => $result['strengths'],
                    'fastest_fix' => $result['fastest_fix'],
                    'raw_checks' => $result['raw_checks'],
                    'status' => QuickScan::STATUS_SCANNED,
                    'scanned_at' => now(),
                ]);

                // Dispatch job for CRM + optional emails (idempotent)
                RunQuickScanJob::dispatch($scan->id);

                $this->lastScanId = $scan->id;
                $this->lastScore = $result['score'];
                $this->lastStatus = 'scanned';

                Notification::make()
                    ->success()
                    ->title("Score: {$result['score']}/100")
                    ->body("Scan #{$scan->id} completed for " . parse_url($url, PHP_URL_HOST))
                    ->send();
            } catch (\Throwable $e) {
                Log::error('InternalQaScan: scan failed', [
                    'scan_id' => $scan->id,
                    'error' => $e->getMessage(),
                ]);

                $scan->update(['status' => QuickScan::STATUS_ERROR]);
                $this->lastScanId = $scan->id;
                $this->lastStatus = 'error';
                $this->lastScore = null;

                Notification::make()
                    ->danger()
                    ->title('Scan failed')
                    ->body($e->getMessage())
                    ->send();
            }
        }
    }

    public function getResultUrl(): ?string
    {
        if (!$this->lastScanId) return null;
        return url('/quick-scan/result') . '?session_id=internal_qa&scan_id=' . $this->lastScanId;
    }

    public function getProcessingUrl(): ?string
    {
        if (!$this->lastScanId) return null;
        return url('/quick-scan/result') . '?session_id=internal_qa&scan_id=' . $this->lastScanId;
    }

    public function getDashboardUrl(): ?string
    {
        if (!$this->lastScanId) return null;
        return url('/dashboard') . '#ai-scans';
    }

    public function getOAuthTestUrl(): ?string
    {
        if (!$this->lastScanId) return null;
        return url('/auth/google/redirect') . '?scan_id=' . $this->lastScanId;
    }
}
