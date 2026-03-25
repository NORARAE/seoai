<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeoOpportunity extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'scan_run_id',
        'client_id',
        'opportunity_category',
        'service_id',
        'location_id',
        'url_inventory_id',
        'search_volume',
        'competition_score',
        'rank_potential',
        'priority_score',
        'demand_score',
        'readiness_score',
        'business_value_score',
        'risk_score',
        'total_score',
        'score_components',
        'signals',
        'reason_summary',
        'recommended_action',
        'estimated_monthly_revenue',
        'service_value',
        'conversion_rate',
        'location_page_id',
        'page_exists',
        'current_position',
        'current_impressions',
        'current_clicks',
        'current_ctr',
        'opportunity_type',
        'status',
        'competitor_analysis',
        'keyword_data',
        'notes',
        'identified_at',
        'last_analyzed_at',
        'completed_at',
        'target_keyword',
        'suggested_url',
        'detection_source',
        'payload_id',
    ];

    protected $casts = [
        'search_volume' => 'integer',
        'competition_score' => 'decimal:2',
        'rank_potential' => 'decimal:2',
        'priority_score' => 'decimal:2',
        'demand_score' => 'decimal:2',
        'readiness_score' => 'decimal:2',
        'business_value_score' => 'decimal:2',
        'risk_score' => 'decimal:2',
        'total_score' => 'decimal:2',
        'score_components' => 'array',
        'signals' => 'array',
        'estimated_monthly_revenue' => 'decimal:2',
        'service_value' => 'decimal:2',
        'conversion_rate' => 'decimal:4',
        'page_exists' => 'boolean',
        'current_position' => 'integer',
        'current_impressions' => 'integer',
        'current_clicks' => 'integer',
        'current_ctr' => 'decimal:4',
        'competitor_analysis' => 'array',
        'keyword_data' => 'array',
        'identified_at' => 'datetime',
        'last_analyzed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function scanRun(): BelongsTo
    {
        return $this->belongsTo(ScanRun::class, 'scan_run_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
    public function urlInventory(): BelongsTo
    {
        return $this->belongsTo(UrlInventory::class, 'url_inventory_id');
    }

    public function payload(): BelongsTo
    {
        return $this->belongsTo(PagePayload::class, 'payload_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(City::class, 'location_id');
    }

    public function locationPage(): BelongsTo
    {
        return $this->belongsTo(LocationPage::class);
    }

    // Query Scopes

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeNewPages($query)
    {
        return $query->where('page_exists', false);
    }

    public function scopeExistingPages($query)
    {
        return $query->where('page_exists', true);
    }

    public function scopeHighPriority($query, int $threshold = 70)
    {
        return $query->where('priority_score', '>=', $threshold);
    }

    public function scopeQuickWins($query)
    {
        return $query->where('opportunity_type', 'quick_win')
            ->where('status', '!=', 'dismissed');
    }

    public function scopeHighRevenue($query, float $threshold = 100.00)
    {
        return $query->where('estimated_monthly_revenue', '>=', $threshold);
    }

    public function scopeTopOpportunities($query, int $limit = 20)
    {
        return $query->where('status', '!=', 'dismissed')
            ->where('status', '!=', 'completed')
            ->orderBy('priority_score', 'desc')
            ->limit($limit);
    }

    // Accessors

    public function getLocationNameAttribute(): string
    {
        return $this->location ? "{$this->location->name}, {$this->location->state->code}" : 'Unknown';
    }

    public function getServiceNameAttribute(): string
    {
        return $this->service?->name ?? 'Unknown Service';
    }

    public function getPriorityBadgeColorAttribute(): string
    {
        return match(true) {
            $this->priority_score >= 80 => 'success',
            $this->priority_score >= 60 => 'warning',
            default => 'gray'
        };
    }

    public function getOpportunityTypeColorAttribute(): string
    {
        return match($this->opportunity_type) {
            'quick_win' => 'success',
            'high_volume' => 'primary',
            'new_page' => 'info',
            'underperforming' => 'warning',
            'content_gap' => 'danger',
            default => 'gray'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'approved' => 'success',
            'in_progress' => 'warning',
            'completed' => 'gray',
            'monitoring' => 'info',
            'dismissed' => 'danger',
            default => 'gray'
        };
    }

    // Methods

    public function markAsCompleted(LocationPage $page): void
    {
        $this->update([
            'status' => 'completed',
            'page_exists' => true,
            'location_page_id' => $page->id,
            'completed_at' => now(),
        ]);
    }

    public function updatePerformanceMetrics(array $metrics): void
    {
        $this->update([
            'current_position' => $metrics['position'] ?? null,
            'current_impressions' => $metrics['impressions'] ?? null,
            'current_clicks' => $metrics['clicks'] ?? null,
            'current_ctr' => $metrics['ctr'] ?? null,
            'last_analyzed_at' => now(),
        ]);
    }

    public function calculateEstimatedRevenue(): float
    {
        if (!$this->search_volume || !$this->service_value) {
            return 0;
        }

        // Estimate CTR based on potential position
        $estimatedCtr = $this->estimateCtrByPosition();
        
        // Calculate: SearchVolume × EstimatedCTR × ConversionRate × ServiceValue
        $estimatedClicks = $this->search_volume * $estimatedCtr;
        $estimatedConversions = $estimatedClicks * $this->conversion_rate;
        $estimatedRevenue = $estimatedConversions * $this->service_value;

        return round($estimatedRevenue, 2);
    }

    protected function estimateCtrByPosition(): float
    {
        // Industry average CTR by position
        // These are rough estimates, can be refined with actual data
        $ctrByPosition = [
            1 => 0.318,  // 31.8%
            2 => 0.158,  // 15.8%
            3 => 0.110,  // 11.0%
            4 => 0.082,  // 8.2%
            5 => 0.065,  // 6.5%
            6 => 0.053,  // 5.3%
            7 => 0.044,  // 4.4%
            8 => 0.037,  // 3.7%
            9 => 0.032,  // 3.2%
            10 => 0.028, // 2.8%
        ];

        // Estimate potential position based on rank_potential
        $potentialPosition = match(true) {
            $this->rank_potential >= 90 => 1,
            $this->rank_potential >= 80 => 2,
            $this->rank_potential >= 70 => 3,
            $this->rank_potential >= 60 => 4,
            $this->rank_potential >= 50 => 5,
            default => 7,
        };

        return $ctrByPosition[$potentialPosition] ?? 0.02;
    }


    public function scopeMissingPages($query)
    {
        return $query->where('opportunity_category', 'missing_page');
    }

    public function scopeOptimizationCandidates($query)
    {
        return $query->where('opportunity_category', 'optimization_candidate');
    }

    public function scopeStructuralWeaknesses($query)
    {
        return $query->where('opportunity_category', 'structural_weakness');
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('opportunity_category', $category);
    }

    public function scopeScored($query)
    {
        return $query->whereNotNull('total_score');
    }

    public function getTotalScoreLabelAttribute(): string
    {
        $score = (float) ($this->total_score ?? 0);

        return match (true) {
            $score >= 75 => 'high',
            $score >= 45 => 'medium',
            default      => 'low',
        };
    }
}
