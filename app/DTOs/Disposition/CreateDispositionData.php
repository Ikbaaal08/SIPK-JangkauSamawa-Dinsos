<?php

namespace App\DTOs\Disposition;

use App\Models\Complaint;
use App\Models\User;

readonly class CreateDispositionData
{
    public function __construct(
        public Complaint $complaint,
        public User $assignedBy,
        public int $assignedToId,
        public string $instruction,
        public ?string $dueDate = null,
    ) {}
}
