<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class QuickScan extends Model
{
    private const UPGRADE_PLAN_ALIASES = [
        'diagnostic' => 'diagnostic',
        'signal-expansion' => 'diagnostic',
        'fix-strategy' => 'fix-strategy',
        'structural-leverage' => 'fix-strategy',
        'optimization' => 'optimization',
        'system-activation' => 'optimization',
    ];

    private const UPGRADE_PLAN_RANK = [
        'diagnostic' => 2,
        'fix-strategy' => 3,
        'optimization' => 4,
    ];

    protected $fillable = [
        'public_scan_id',
        'email',
        'url',
        'domain',
        'url_input',
        'ip_address',
        'user_id',
        'stripe_session_id',
        'paid',
        'score',
        'last_score',
        'score_change',
        'issues',
        'strengths',
        'fastest_fix',
        'raw_checks',
        'categories',
        'page_count',
        'broken_links',
        'status',
        'emails_sent',
        'owner_notified_at',
        'is_internal',
        'source',
        'suppress_emails',
        'is_repeat_scan',
        'domain_scan_count',
        'initiated_by',
        'scanned_at',
        'upgrade_plan',
        'upgrade_status',
        'upgrade_stripe_session_id',
        'upgraded_at',
        'onboarding_submission_id',
        'dimensions',
        'intelligence',
        'site_id',
    ];

    protected $casts = [
        'paid' => 'boolean',
        'emails_sent' => 'boolean',
        'is_internal' => 'boolean',
        'suppress_emails' => 'boolean',
        'is_repeat_scan' => 'boolean',
        'domain_scan_count' => 'integer',
        'score' => 'integer',
        'last_score' => 'integer',
        'score_change' => 'integer',
        'issues' => 'array',
        'strengths' => 'array',
        'raw_checks' => 'array',
        'categories' => 'array',
        'broken_links' => 'array',
        'page_count' => 'integer',
        'scanned_at' => 'datetime',
        'upgraded_at' => 'datetime',
        'dimensions' => 'array',
        'intelligence' => 'array',
        'owner_notified_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_SCANNED = 'scanned';
    const STATUS_ERROR = 'error';

    protected static function booted(): void
    {
        static::created(function (QuickScan $scan): void {
            if (!empty($scan->public_scan_id)) {
                return;
            }

            $scan->forceFill([
                'public_scan_id' => self::buildPublicScanId((int) $scan->id),
            ])->saveQuietly();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Return the most recent ScanRun triggered by this QuickScan.
     */
    public function scanRun(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ScanRun::class)->latestOfMany('started_at');
    }

    public function onboardingSubmission(): BelongsTo
    {
        return $this->belongsTo(OnboardingSubmission::class);
    }

    public function isUpgraded(): bool
    {
        return $this->normalizedUpgradePlan() !== null && $this->upgrade_status === 'paid';
    }

    public function normalizedUpgradePlan(): ?string
    {
        if (!is_string($this->upgrade_plan) || $this->upgrade_plan === '') {
            return null;
        }

        $key = strtolower(trim($this->upgrade_plan));

        return self::UPGRADE_PLAN_ALIASES[$key] ?? null;
    }

    public static function normalizeUpgradePlan(?string $plan): ?string
    {
        if (!is_string($plan) || $plan === '') {
            return null;
        }

        $key = strtolower(trim($plan));

        return self::UPGRADE_PLAN_ALIASES[$key] ?? null;
    }

    public static function rankForUpgradePlan(?string $plan): int
    {
        $normalized = self::normalizeUpgradePlan($plan);

        return $normalized ? (self::UPGRADE_PLAN_RANK[$normalized] ?? 0) : 0;
    }

    /**
     * Returns entitlement rank: 0=no access, 1=baseline scan, 2-4=upgrade tiers.
     */
    public function upgradeTierRank(): int
    {
        $upgradeRank = ($this->upgrade_status === 'paid')
            ? self::rankForUpgradePlan($this->upgrade_plan)
            : 0;

        if ($upgradeRank > 0) {
            return $upgradeRank;
        }

        return ($this->paid || in_array($this->status, [self::STATUS_PAID, self::STATUS_SCANNED], true)) ? 1 : 0;
    }

    /**
     * Returns the domain for this scan.
     * Uses the stored domain column when present; falls back to parsing the
     * host from the url field so callers always receive a usable string.
     */
    public function domain(): string
    {
        $stored = $this->attributes['domain'] ?? null;
        return ($stored !== null && $stored !== '')
            ? $stored
            : (parse_url($this->url, PHP_URL_HOST) ?? $this->url ?? '');
    }

    public function publicScanId(): string
    {
        return is_string($this->public_scan_id) && $this->public_scan_id !== ''
            ? strtoupper($this->public_scan_id)
            : self::buildPublicScanId((int) $this->id);
    }

    public function aiScanId(): string
    {
        return 'AI-' . strtoupper(str_pad(base_convert((string) $this->id, 10, 36), 4, '0', STR_PAD_LEFT));
    }

    public static function buildPublicScanId(int $id): string
    {
        return 'SCAN-' . str_pad((string) max(1, $id), 5, '0', STR_PAD_LEFT);
    }

    /**
     * Resolve internal ID from SCAN-xxxxx, AI-xxxx, AVS-xx-xxxx, or numeric references.
     */
    public static function idFromPublicReference(string $reference): ?int
    {
        $value = strtoupper(trim($reference));

        if ($value === '') {
            return null;
        }

        if (preg_match('/^\d+$/', $value)) {
            $id = (int) $value;
            return $id > 0 ? $id : null;
        }

        if (str_starts_with($value, 'AVS-')) {
            return self::idFromSystemScanId($value);
        }

        if (preg_match('/^SCAN-(\d+)$/', $value, $matches)) {
            $id = (int) $matches[1];
            return $id > 0 ? $id : null;
        }

        if (preg_match('/^AI-([0-9A-Z]{2,10})$/', $value, $matches)) {
            $id = (int) base_convert(strtolower($matches[1]), 36, 10);
            return $id > 0 ? $id : null;
        }

        $id = (int) DB::table('quick_scans')
            ->whereRaw('UPPER(public_scan_id) = ?', [$value])
            ->value('id');

        return $id > 0 ? $id : null;
    }

    public function systemScanId(): string
    {
        $scorePart = str_pad((string) max(0, min(99, (int) ($this->score ?? 0))), 2, '0', STR_PAD_LEFT);
        $idPart = strtoupper(str_pad(base_convert((string) $this->id, 10, 36), 3, '0', STR_PAD_LEFT));
        $check = strtoupper(substr(hash_hmac('sha256', (string) $this->id, (string) config('app.key', 'seoai')), 0, 2));

        return "AVS-{$scorePart}-{$idPart}{$check}";
    }

    public static function idFromSystemScanId(string $systemScanId): ?int
    {
        if (!preg_match('/^AVS-\d{2}-([0-9A-Z]{3,10})([0-9A-F]{2})$/', strtoupper($systemScanId), $matches)) {
            return null;
        }

        $idPart = strtoupper($matches[1]);
        $check = strtoupper($matches[2]);
        $id = (int) base_convert(strtolower($idPart), 36, 10);

        if ($id <= 0) {
            return null;
        }

        $expectedCheck = strtoupper(substr(hash_hmac('sha256', (string) $id, (string) config('app.key', 'seoai')), 0, 2));

        return hash_equals($expectedCheck, $check) ? $id : null;
    }

    public function publicShareKey(): string
    {
        $idPart = base_convert((string) $this->id, 10, 36);
        $stamp = (string) ($this->created_at?->timestamp ?? 0);
        $secret = (string) config('app.key', 'seoai');
        $signature = substr(hash_hmac('sha256', $this->id . '|' . $stamp, $secret), 0, 10);

        return strtolower($idPart . '-' . $signature);
    }

    public static function idFromPublicShareKey(string $shareKey): ?int
    {
        if (!preg_match('/^([0-9a-z]+)-([0-9a-f]{10})$/i', $shareKey, $matches)) {
            return null;
        }

        $id = (int) base_convert(strtolower($matches[1]), 36, 10);

        return $id > 0 ? $id : null;
    }

    public function matchesPublicShareKey(string $shareKey): bool
    {
        return hash_equals($this->publicShareKey(), strtolower($shareKey));
    }

    /**
     * Returns the real page count for this scan.
     *
     * Phase 4 fix: once the full-site crawl triggered by this QuickScan has run,
     * the url_inventory count is a far more accurate measure than the homepage
     * link count stored in page_count at scan time.
     *
     * Falls back to the stored page_count when no crawl data exists yet.
     */
    public function effectivePageCount(): int
    {
        if ($this->site_id) {
            $crawledCount = UrlInventory::where('site_id', $this->site_id)
                ->where('status', 'completed')
                ->count();

            if ($crawledCount > 0) {
                return $crawledCount;
            }
        }

        return $this->page_count ?? 0;
    }
}
