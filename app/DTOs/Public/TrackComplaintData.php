<?php

namespace App\DTOs\Public;

readonly class TrackComplaintData
{
    public function __construct(
        public string $ticket_code
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            ticket_code: trim((string) ($data['ticket_code'] ?? ''))
        );
    }
}
