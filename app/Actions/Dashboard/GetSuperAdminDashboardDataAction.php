<?php

namespace App\Actions\Dashboard;

use App\Models\User;
use App\Repositories\Contracts\ComplaintRepositoryInterface;

class GetSuperAdminDashboardDataAction
{
    public function __construct(
        protected ComplaintRepositoryInterface $repository
    ) {}

    /**
     * Compile complete dashboard metrics and datasets for Super Admin.
     *
     * @return array<string, mixed>
     */
    public function handle(User $user): array
    {
        return [
            'statusCounts' => $this->repository->countByStatus($user),
            'recentComplaints' => $this->repository->getRecent($user, 6),
            'categoryDistribution' => $this->repository->countByCategory($user),
            'bidangDistribution' => $this->repository->countByBidang($user),
            'overdueComplaints' => $this->repository->getOverdue($user, 6),
        ];
    }
}
