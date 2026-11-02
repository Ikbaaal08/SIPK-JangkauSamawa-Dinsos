<?php

namespace App\Livewire\Admin;

use App\Actions\Dashboard\GetAdminDashboardDataAction;
use App\Actions\Dashboard\GetDashboardStatisticsAction;
use App\Actions\Dashboard\GetSuperAdminDashboardDataAction;
use App\DTOs\Dashboard\GetDashboardStatisticsDto;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard Administrator - Jangkau Samawa')]
class Dashboard extends Component
{
    public function render(
        GetSuperAdminDashboardDataAction $superAdminAction,
        GetAdminDashboardDataAction $adminAction,
        GetDashboardStatisticsAction $legacyAction
    ): View {
        $user = auth()->user();
        $isSuperAdmin = $user ? $user->isSuperAdmin() : false;

        $data = $isSuperAdmin
            ? $superAdminAction->handle($user)
            : $adminAction->handle($user);

        $legacyStatistics = $legacyAction->handle(new GetDashboardStatisticsDto($user));

        return view('livewire.admin.dashboard', [
            'isSuperAdmin' => $isSuperAdmin,
            'data' => $data,
            'statistics' => $legacyStatistics,
            'statusCounts' => $data['statusCounts'],
            'recentComplaints' => $data['recentComplaints'],
            'actionNeededComplaints' => $data['actionNeededComplaints'] ?? collect(),
            'categoryDistribution' => $data['categoryDistribution'] ?? [],
            'bidangDistribution' => $data['bidangDistribution'] ?? [],
            'overdueComplaints' => $data['overdueComplaints'] ?? collect(),
        ]);
    }
}
