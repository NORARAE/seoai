<?php

namespace App\Contracts;

use App\DTOs\PublishResult;
use App\Models\PagePayload;
use App\Models\Site;

interface PublishingAdapterInterface
{
    /**
     * Validate connection to publishing destination
     */
    public function validateConnection(Site $site): bool;

    /**
     * Publish a page payload to the destination
     */
    public function publish(PagePayload $payload): PublishResult;

    /**
     * Update an already-published page
     */
    public function update(PagePayload $payload): PublishResult;

    /**
     * Delete a published page
     */
    public function delete(PagePayload $payload): bool;

    /**
     * Get the status of a published page
     */
    public function getStatus(PagePayload $payload): string;

    /**
     * Export payload as structured file/format
     */
    public function export(PagePayload $payload, string $format = 'json'): string;

    /**
     * Check if this adapter supports batch publishing
     */
    public function supportsBatch(): bool;

    /**
     * Get adapter capabilities
     */
    public function getCapabilities(): array;
}
