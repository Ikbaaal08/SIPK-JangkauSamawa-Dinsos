<?php

namespace App\Actions\Dashboard;

use App\Models\User;
use App\Repositories\Contracts\ComplaintRepositoryInterface;

class GetKabagDashboardDataAction
{
    public function __construct(
        protected ComplaintRepositoryInterface $repository
    ) {}

    /**
     * Compile dashboard metrics and datasets for Kepala Bidang (scoped to kabag's bidang).
     *
     * @return array<string, mixed>
     */
    public function handle(User $user): array
    {
        return [
            'statusCounts' => $this->repository->countByStatus($user),
            'actionNeededComplaints' => $this->repository->getActionNeeded($user, 6),
            'recentComplaints' => $this->repository->getRecent($user, 6),
            'categoryDistribution' => $this->repository->countByCategory($user),
            'overdueComplaints' => $this->repository->getOverdue($user, 6),
        ];
    }
}

