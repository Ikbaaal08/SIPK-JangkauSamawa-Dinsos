<?php

namespace App\DTOs\Dashboard;

use App\Models\User;

readonly class GetDashboardStatisticsDto
{
    public function __construct(
        public User $user,
    ) {}
}
