<?php

namespace App\Livewire\Petugas;

use App\Actions\Dashboard\GetDashboardStatisticsAction;
use App\Actions\Dashboard\GetKabagDashboardDataAction;
use App\DTOs\Dashboard\GetDashboardStatisticsDto;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard Kepala Bidang - Jangkau Samawa')]
class Dashboard extends Component
{
    public function render(
        GetKabagDashboardDataAction $kabagAction,
        GetDashboardStatisticsAction $legacyAction
    ): View {
        $user = auth()->user();

        $data = $kabagAction->handle($user);
        $legacyStatistics = $legacyAction->handle(new GetDashboardStatisticsDto($user));

        return view('livewire.petugas.dashboard', [
            'data' => $data,
            'statistics' => $legacyStatistics,
            'statusCounts' => $data['statusCounts'],
            'actionNeededComplaints' => $data['actionNeededComplaints'],
            'recentComplaints' => $data['recentComplaints'],
            'categoryDistribution' => $data['categoryDistribution'],
            'overdueComplaints' => $data['overdueComplaints'],
        ]);
    }
}
