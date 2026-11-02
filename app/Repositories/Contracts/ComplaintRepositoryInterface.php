<?php

namespace App\Repositories\Contracts;

use App\DTOs\Dashboard\DashboardStatisticsDto;
use App\Models\User;

interface ComplaintRepositoryInterface
{
    /**
     * Get complaint statistics tailored to the given user role.
     */
    public function getStatisticsForUser(User $user): DashboardStatisticsDto;
}
