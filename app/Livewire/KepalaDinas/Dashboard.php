<?php

namespace App\Livewire\KepalaDinas;

use App\Actions\Dashboard\GetDashboardStatisticsAction;
use App\Actions\Dashboard\GetKadisDashboardDataAction;
use App\DTOs\Dashboard\GetDashboardStatisticsDto;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard Kepala Dinas - Jangkau Samawa')]
class Dashboard extends Component
{
    public function render(
        GetKadisDashboardDataAction $kadisAction,
        GetDashboardStatisticsAction $legacyAction
    ): View {
        $user = auth()->user();

        $data = $kadisAction->handle($user);
        $legacyStatistics = $legacyAction->handle(new GetDashboardStatisticsDto($user));

        return view('livewire.kepala-dinas.dashboard', [
            'data' => $data,
            'statistics' => $legacyStatistics,
            'statusCounts' => $data['statusCounts'],
            'actionNeededComplaints' => $data['actionNeededComplaints'],
            'recentComplaints' => $data['recentComplaints'],
            'bidangDistribution' => $data['bidangDistribution'],
            'overdueComplaints' => $data['overdueComplaints'],
        ]);
    }
}
