<?php

namespace App\Services\Publishing;

use App\Contracts\PublishingAdapterInterface;
use App\DTOs\PublishResult;
use App\Models\PagePayload;
use App\Models\Site;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WordPressPublishingAdapter implements PublishingAdapterInterface
{
    public function validateConnection(Site $site): bool
    {
        if (!$site->wordpress_url || !$site->wordpress_app_password) {
            return false;
        }

        try {
            $response = Http::withBasicAuth(
                $site->wordpress_username,
                decrypt($site->wordpress_app_password)
            )->timeout(10)
            ->get($site->wordpress_url . '/wp-json/wp/v2/users/me');

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('WordPress connection validation failed', [
                'site_id' => $site->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function publish(PagePayload $payload): PublishResult
    {
        $site = $payload->site;

        if (!$this->validateConnection($site)) {
            return PublishResult::failure('WordPress connection not configured or invalid');
        }

        try {
            $postData = [
                'title' => $payload->title,
                'content' => $this->enrichContent($payload),
                'slug' => $payload->slug,
                'status' => 'draft', // Start as draft for safety
                'meta' => [
                    '_seoaico_payload_id' => $payload->id,
                ],
            ];

            // Add meta description if Yoast/RankMath detected
            if ($this->hasYoastOrRankMath($site)) {
                $postData['yoast_meta'] = [
                    'yoast_wpseo_metadesc' => $payload->meta_description,
                ];
            }

            $response = Http::withBasicAuth(
                $site->wordpress_username,
                decrypt($site->wordpress_app_password)
            )->timeout(30)
            ->post($site->wordpress_url . '/wp-json/wp/v2/pages', $postData);

            if ($response->successful()) {
                $data = $response->json();
                $remoteId = (string) $data['id'];
                $remoteUrl = $data['link'];
                $editUrl = $site->wordpress_url . '/wp-admin/post.php?post=' . $remoteId . '&action=edit';

                return PublishResult::success($remoteId, $remoteUrl, $editUrl, $data);
            }

            return PublishResult::failure('WordPress API error: ' . $response->body());
        } catch (\Exception $e) {
            return PublishResult::failure($e->getMessage());
        }
    }

    public function update(PagePayload $payload): PublishResult
    {
        if (!$payload->remote_id) {
            return $this->publish($payload);
        }

        $site = $payload->site;

        try {
            $response = Http::withBasicAuth(
                $site->wordpress_username,
                decrypt($site->wordpress_app_password)
            )->timeout(30)
            ->post($site->wordpress_url . '/wp-json/wp/v2/pages/' . $payload->remote_id, [
                'title' => $payload->title,
                'content' => $this->enrichContent($payload),
                'slug' => $payload->slug,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return PublishResult::success(
                    $payload->remote_id,
                    $data['link'],
                    $payload->remote_edit_url,
                    $data
                );
            }

            return PublishResult::failure('WordPress update failed: ' . $response->body());
        } catch (\Exception $e) {
            return PublishResult::failure($e->getMessage());
        }
    }

    public function delete(PagePayload $payload): bool
    {
        if (!$payload->remote_id) {
            return false;
        }

        $site = $payload->site;

        try {
            $response = Http::withBasicAuth(
                $site->wordpress_username,
                decrypt($site->wordpress_app_password)
            )->delete($site->wordpress_url . '/wp-json/wp/v2/pages/' . $payload->remote_id);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('WordPress page deletion failed', [
                'payload_id' => $payload->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function getStatus(PagePayload $payload): string
    {
        if (!$payload->remote_id) {
            return 'not_published';
        }

        $site = $payload->site;

        try {
            $response = Http::withBasicAuth(
                $site->wordpress_username,
                decrypt($site->wordpress_app_password)
            )->get($site->wordpress_url . '/wp-json/wp/v2/pages/' . $payload->remote_id);

            if ($response->successful()) {
                return $response->json()['status'] ?? 'unknown';
            }
        } catch (\Exception $e) {
            Log::error('WordPress status check failed', [
                'payload_id' => $payload->id,
                'error' => $e->getMessage(),
            ]);
        }

        return 'unknown';
    }

    public function export(PagePayload $payload, string $format = 'json'): string
    {
        $exportData = $payload->toExportArray();
        $exportData['wordpress'] = [
            'post_type' => 'page',
            'post_status' => 'draft',
        ];

        return json_encode($exportData, JSON_PRETTY_PRINT);
    }

    public function supportsBatch(): bool
    {
        return true; // WordPress REST API supports batch requests
    }

    public function getCapabilities(): array
    {
        return [
            'native_publish' => true,
            'draft_mode' => true,
            'schema_injection' => true,
            'featured_images' => true,
            'custom_fields' => true,
            'categories' => true,
            'tags' => true,
        ];
    }

    /**
     * Enrich content with schema and internal links if needed
     */
    protected function enrichContent(PagePayload $payload): string
    {
        $content = $payload->body_content;

        // Optionally inject schema as HTML comment for debugging
        if ($payload->schema_json_ld) {
            $schemaScript = '<script type="application/ld+json">' . 
                json_encode($payload->schema_json_ld) . 
                '</script>';
            $content = $schemaScript . "\n\n" . $content;
        }

        return $content;
    }

    /**
     * Check if WordPress installation has Yoast SEO or RankMath
     */
    protected function hasYoastOrRankMath(Site $site): bool
    {
        // TODO: Implement plugin detection via WordPress API
        // For now, assume present
        return true;
    }
}
