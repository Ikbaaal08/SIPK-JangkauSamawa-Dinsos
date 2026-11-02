<?php

namespace App\Repositories\Contracts;

use App\DTOs\Dashboard\DashboardStatisticsDto;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface ComplaintRepositoryInterface
{
    /**
     * Get complaint statistics tailored to the given user role (legacy DTO).
     */
    public function getStatisticsForUser(User $user): DashboardStatisticsDto;

    /**
     * Get complaint counts grouped by status for a given user scope.
     *
     * @return array<string, int>
     */
    public function countByStatus(?User $user = null): array;

    /**
     * Get recent complaints for a given user scope.
     */
    public function getRecent(?User $user = null, int $limit = 5): Collection;

    /**
     * Get complaint distribution grouped by category for a given user scope.
     *
     * @return array<int, array{name: string, code: string, count: int, percentage: float}>
     */
    public function countByCategory(?User $user = null): array;

    /**
     * Get complaint distribution grouped by bidang for a given user scope.
     *
     * @return array<string, array{label: string, count: int, percentage: float}>
     */
    public function countByBidang(?User $user = null): array;

    /**
     * Get overdue complaints for a given user scope.
     */
    public function getOverdue(?User $user = null, int $limit = 5): Collection;

    /**
     * Get complaints that currently need action depending on user role.
     */
    public function getActionNeeded(User $user, int $limit = 5): Collection;
}
