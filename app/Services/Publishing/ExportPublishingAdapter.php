<?php

namespace App\Services\Publishing;

use App\Contracts\PublishingAdapterInterface;
use App\DTOs\PublishResult;
use App\Models\PagePayload;
use App\Models\Site;
use Illuminate\Support\Facades\Storage;

class ExportPublishingAdapter implements PublishingAdapterInterface
{
    public function validateConnection(Site $site): bool
    {
        // Export mode doesn't require connection
        return true;
    }

    public function publish(PagePayload $payload): PublishResult
    {
        // "Publishing" means marking as ready for export
        $exportPath = $this->generateExportFile($payload);

        return PublishResult::success(
            remoteId: 'export-' . $payload->id,
            remoteUrl: Storage::url($exportPath),
            metadata: ['export_path' => $exportPath]
        );
    }

    public function update(PagePayload $payload): PublishResult
    {
        return $this->publish($payload);
    }

    public function delete(PagePayload $payload): bool
    {
        if ($payload->remote_id && str_starts_with($payload->remote_id, 'export-')) {
            $path = "exports/site-{$payload->site_id}/batch-{$payload->batch_id}/payload-{$payload->id}.json";
            return Storage::delete($path);
        }
        return true;
    }

    public function getStatus(PagePayload $payload): string
    {
        return 'exported';
    }

    public function export(PagePayload $payload, string $format = 'json'): string
    {
        // Use the PagePayload's built-in export functionality
        return $payload->toExportFormat($format);
    }

    public function supportsBatch(): bool
    {
        return true;
    }

    public function getCapabilities(): array
    {
        return [
            'native_publish' => false,
            'export_json' => true,
            'export_markdown' => true,
            'export_html' => true,
            'export_csv' => true,
        ];
    }

    protected function generateExportFile(PagePayload $payload): string
    {
        $exportData = $payload->toExportArray();
        $filename = "exports/site-{$payload->site_id}/batch-{$payload->batch_id}/payload-{$payload->id}.json";

        Storage::put($filename, json_encode($exportData, JSON_PRETTY_PRINT));

        return $filename;
    }

    protected function exportAsMarkdown(PagePayload $payload): string
    {
        $content = "# {$payload->title}\n\n";
        $content .= "**Slug:** `{$payload->slug}`\n\n";
        $content .= "**Meta Description:** {$payload->meta_description}\n\n";
        
        if ($payload->parent_page_slug) {
            $content .= "**Parent Page:** `{$payload->parent_page_slug}`\n\n";
        }
        
        $content .= "---\n\n";
        $content .= strip_tags($payload->body_content);
        
        if ($payload->internal_link_suggestions) {
            $content .= "\n\n## Suggested Internal Links\n\n";
            foreach ($payload->internal_link_suggestions as $link) {
                $anchor = $link['anchor'] ?? 'Link';
                $url = $link['url'] ?? '#';
                $content .= "- [{$anchor}]({$url})\n";
            }
        }

        if ($payload->schema_json_ld) {
            $content .= "\n\n## Schema.org JSON-LD\n\n```json\n";
            $content .= json_encode($payload->schema_json_ld, JSON_PRETTY_PRINT);
            $content .= "\n```\n";
        }

        return $content;
    }

    protected function exportAsHtml(PagePayload $payload): string
    {
        $html = "<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n";
        $html .= "    <meta charset=\"UTF-8\">\n";
        $html .= "    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";
        $html .= "    <title>{$payload->title}</title>\n";
        $html .= "    <meta name=\"description\" content=\"{$payload->meta_description}\">\n";
        
        if ($payload->canonical_url_suggestion) {
            $html .= "    <link rel=\"canonical\" href=\"{$payload->canonical_url_suggestion}\">\n";
        }
        
        if ($payload->schema_json_ld) {
            $html .= "    <script type=\"application/ld+json\">\n";
            $html .= json_encode($payload->schema_json_ld, JSON_PRETTY_PRINT);
            $html .= "    </script>\n";
        }
        
        $html .= "</head>\n<body>\n";
        $html .= "    <h1>{$payload->title}</h1>\n";
        $html .= "    <article>\n";
        $html .= $payload->body_content;
        $html .= "    </article>\n";
        $html .= "</body>\n</html>";
        
        return $html;
    }

    protected function exportAsCsv(PagePayload $payload): string
    {
        // Simple CSV row for spreadsheet import
        $data = [
            $payload->title,
            $payload->slug,
            $payload->meta_description,
            strip_tags($payload->body_content),
            $payload->parent_page_slug,
            json_encode($payload->internal_link_suggestions),
        ];
        
        return implode('","', array_map(fn($v) => str_replace('"', '""', $v), $data));
    }
}
