<?php

namespace App\DTOs\Disposition;

use App\Models\Disposition;
use App\Models\User;

readonly class UpdateDispositionStatusData
{
    public function __construct(
        public Disposition $disposition,
        public User $user,
        public string $status,
        public ?string $note = null,
    ) {}
}
