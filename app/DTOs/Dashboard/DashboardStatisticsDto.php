<?php

namespace App\DTOs\Dashboard;

readonly class DashboardStatisticsDto
{
    /**
     * @param  array<string, mixed>  $extra
     */
    public function __construct(
        public int $totalAduan,
        public int $aduanBaru,
        public int $aduanDiproses,
        public int $aduanSelesai,
        public array $extra = [],
    ) {}
}
