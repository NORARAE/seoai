<?php

namespace App\DTOs;

class PublishResult
{
    public function __construct(
        public bool $success,
        public ?string $remoteId = null,
        public ?string $remoteUrl = null,
        public ?string $remoteEditUrl = null,
        public ?string $error = null,
        public ?array $metadata = null,
    ) {}

    public static function success(
        string $remoteId,
        string $remoteUrl,
        ?string $editUrl = null,
        ?array $metadata = null
    ): self {
        return new self(
            success: true,
            remoteId: $remoteId,
            remoteUrl: $remoteUrl,
            remoteEditUrl: $editUrl,
            metadata: $metadata
        );
    }

    public static function failure(string $error): self
    {
        return new self(success: false, error: $error);
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'remote_id' => $this->remoteId,
            'remote_url' => $this->remoteUrl,
            'remote_edit_url' => $this->remoteEditUrl,
            'error' => $this->error,
            'metadata' => $this->metadata,
        ];
    }
}
