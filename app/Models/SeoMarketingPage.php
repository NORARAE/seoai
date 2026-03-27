<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SeoMarketingPage extends Model
{
    protected $table = 'seo_marketing_pages';

    protected $fillable = [
        'url_slug',
        'cluster',
        'search_intent',
        'nav_label',
        'primary_keyword',
        'secondary_keywords',
        'meta_title',
        'meta_description',
        'og_title',
        'og_description',
        'h1',
        'h2_structure',
        'hook',
        'system_explanation',
        'benefits',
        'exclusivity',
        'use_cases',
        'internal_linking_section',
        'cta_top',
        'cta_mid',
        'cta_bottom',
        'internal_links',
        'schema_json',
        'sitemap_priority',
        'sitemap_changefreq',
        'sitemap_file',
        'money_page_rank',
        'priority_level',
        'is_indexed',
    ];

    protected $casts = [
        'secondary_keywords'  => 'array',
        'h2_structure'        => 'array',
        'benefits'            => 'array',
        'use_cases'           => 'array',
        'cta_top'             => 'array',
        'cta_mid'             => 'array',
        'cta_bottom'          => 'array',
        'internal_links'      => 'array',
        'schema_json'         => 'array',
        'sitemap_priority'    => 'float',
        'money_page_rank'     => 'integer',
        'is_indexed'          => 'boolean',
    ];

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeIndexed(Builder $q): Builder
    {
        return $q->where('is_indexed', true);
    }

    public function scopeCluster(Builder $q, string $cluster): Builder
    {
        return $q->where('cluster', $cluster);
    }

    public function scopeMoneyPages(Builder $q): Builder
    {
        return $q->whereNotNull('money_page_rank')->orderBy('money_page_rank');
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    /** Full canonical URL for this page */
    public function getCanonicalUrlAttribute(): string
    {
        return url('/' . $this->url_slug);
    }

    /** Lateral links from the internal_links JSON */
    public function getLateralLinksAttribute(): array
    {
        return $this->internal_links['lateral'] ?? [];
    }

    /** Homepage CTAs from the internal_links JSON */
    public function getHomepageCtasAttribute(): array
    {
        return $this->internal_links['homepage_ctas'] ?? [];
    }

    /** Resolved schema_json or auto-built baseline if empty */
    public function getResolvedSchemaAttribute(): array
    {
        if (! empty($this->schema_json)) {
            return $this->schema_json;
        }

        return $this->buildBaselineSchema();
    }

    // ── Schema builder ────────────────────────────────────────────────────────

    /**
     * Build a baseline JSON-LD schema when no custom schema is stored.
     * Outputs: WebPage + SoftwareApplication (org context)
     */
    private function buildBaselineSchema(): array
    {
        $org = [
            '@type'  => 'Organization',
            '@id'    => 'https://seoaico.com/#org',
            'name'   => 'SEOAIco',
            'url'    => 'https://seoaico.com',
            'sameAs' => ['https://seoaico.com'],
        ];

        $webpage = [
            '@context'        => 'https://schema.org',
            '@graph'          => [
                $org,
                [
                    '@type'           => 'WebPage',
                    '@id'             => $this->canonical_url . '#webpage',
                    'url'             => $this->canonical_url,
                    'name'            => $this->meta_title,
                    'description'     => $this->meta_description,
                    'isPartOf'        => ['@id' => 'https://seoaico.com/#website'],
                    'about'           => ['@id' => 'https://seoaico.com/#org'],
                    'publisher'       => ['@id' => 'https://seoaico.com/#org'],
                    'inLanguage'      => 'en-US',
                    'primaryImageOfPage' => null,
                ],
            ],
        ];

        // FAQ block from h2s
        $h2s = $this->h2_structure ?? [];
        if (! empty($h2s)) {
            $faqs = array_map(fn ($q) => [
                '@type'          => 'Question',
                'name'           => $q,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => $this->hook ?? $q,
                ],
            ], array_slice($h2s, 0, 6));

            $webpage['@graph'][] = [
                '@type'      => 'FAQPage',
                '@id'        => $this->canonical_url . '#faqpage',
                'mainEntity' => $faqs,
            ];
        }

        return $webpage;
    }
}
