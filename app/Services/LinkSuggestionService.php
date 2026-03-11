<?php

namespace App\Services;

use App\Models\LinkSuggestion;
use App\Models\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LinkSuggestionService
{
    /**
     * Generate link suggestions for a weak page
     */
    public function generateSuggestionsForPage(Page $page, int $maxSuggestions = 5): Collection
    {
        // Only generate for pages with weak link profile
        if ($page->incoming_links_count >= 2) {
            return collect();
        }

        // Find candidate source pages on the same site
        $candidates = $this->findCandidateSourcePages($page);

        // Generate suggestions
        $suggestions = collect();
        
        foreach ($candidates->take($maxSuggestions) as $sourcePage) {
            // Skip if suggestion already exists
            if ($this->suggestionExists($sourcePage->id, $page->id)) {
                continue;
            }

            $anchorText = $this->generateAnchorText($page);
            $reason = $this->generateReason($sourcePage, $page);

            $suggestion = LinkSuggestion::create([
                'site_id' => $page->site_id,
                'source_page_id' => $sourcePage->id,
                'target_page_id' => $page->id,
                'suggested_anchor_text' => $anchorText,
                'reason' => $reason,
                'status' => 'pending',
            ]);

            $suggestions->push($suggestion);
        }

        return $suggestions;
    }

    /**
     * Find candidate pages that could link to the target
     */
    private function findCandidateSourcePages(Page $targetPage): Collection
    {
        $keywords = $this->extractKeywords($targetPage);

        return Page::where('site_id', $targetPage->site_id)
            ->where('id', '!=', $targetPage->id)
            ->where('crawl_status', 'completed')
            ->where('status_code', '>=', 200)
            ->where('status_code', '<', 300)
            // Prioritize pages with more outgoing links (content-rich)
            ->orderByDesc('outgoing_links_count')
            ->limit(20)
            ->get()
            ->filter(function ($page) use ($targetPage, $keywords) {
                // Check if page doesn't already link to target
                $alreadyLinks = DB::table('internal_links')
                    ->where('source_page_id', $page->id)
                    ->where('target_url', $targetPage->url)
                    ->exists();

                if ($alreadyLinks) {
                    return false;
                }

                // Prefer pages with related content (keyword match in path)
                foreach ($keywords as $keyword) {
                    if (Str::contains(strtolower($page->path), strtolower($keyword))) {
                        return true;
                    }
                }

                return false;
            })
            ->sortByDesc(function ($page) {
                // Sort by content richness
                return $page->outgoing_links_count;
            });
    }

    /**
     * Extract keywords from page URL for matching
     */
    private function extractKeywords(Page $page): array
    {
        $path = trim($page->path, '/');
        
        // Split by common separators
        $segments = preg_split('/[\/\-_]/', $path);
        
        // Filter out common words and short segments
        $stopWords = ['the', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'a', 'an'];
        
        return array_filter($segments, function ($segment) use ($stopWords) {
            $segment = strtolower($segment);
            return strlen($segment) > 3 && !in_array($segment, $stopWords);
        });
    }

    /**
     * Generate anchor text for the link
     */
    private function generateAnchorText(Page $targetPage): string
    {
        // Use existing title if available
        if (!empty($targetPage->title)) {
            return Str::limit($targetPage->title, 60, '');
        }

        // Use suggested title if available
        if (!empty($targetPage->suggested_title)) {
            return Str::limit($targetPage->suggested_title, 60, '');
        }

        // Generate from URL slug
        $path = trim($targetPage->path, '/');
        $segments = explode('/', $path);
        $lastSegment = end($segments);
        
        // Clean up slug
        $slug = preg_replace('/\.(html|php|htm)$/i', '', $lastSegment);
        $anchorText = str_replace(['-', '_'], ' ', $slug);
        $anchorText = Str::title($anchorText);

        return Str::limit($anchorText, 60, '');
    }

    /**
     * Generate a human-readable reason for the suggestion
     */
    private function generateReason(Page $sourcePage, Page $targetPage): string
    {
        $reasons = [];

        // Check for keyword relevance
        $targetKeywords = $this->extractKeywords($targetPage);
        foreach ($targetKeywords as $keyword) {
            if (Str::contains(strtolower($sourcePage->path), strtolower($keyword))) {
                $reasons[] = "Related content (keyword: {$keyword})";
                break;
            }
        }

        // Note link profile
        if ($targetPage->incoming_links_count === 0) {
            $reasons[] = "Target is orphaned (0 links)";
        } else {
            $reasons[] = "Target has weak link profile ({$targetPage->incoming_links_count} links)";
        }

        // Note source page quality
        if ($sourcePage->outgoing_links_count > 5) {
            $reasons[] = "Source is content-rich ({$sourcePage->outgoing_links_count} outgoing links)";
        }

        return implode('. ', $reasons);
    }

    /**
     * Check if a suggestion already exists
     */
    private function suggestionExists(int $sourcePageId, int $targetPageId): bool
    {
        return LinkSuggestion::where('source_page_id', $sourcePageId)
            ->where('target_page_id', $targetPageId)
            ->exists();
    }
}
