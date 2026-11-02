<?php

namespace App\Actions\Dashboard;

use App\DTOs\Dashboard\DashboardStatisticsDto;
use App\DTOs\Dashboard\GetDashboardStatisticsDto;
use App\Repositories\Contracts\ComplaintRepositoryInterface;

class GetDashboardStatisticsAction
{
    public function __construct(
        protected ComplaintRepositoryInterface $complaintRepository,
    ) {}

    public function handle(GetDashboardStatisticsDto $dto): DashboardStatisticsDto
    {
        return $this->complaintRepository->getStatisticsForUser($dto->user);
    }
}
