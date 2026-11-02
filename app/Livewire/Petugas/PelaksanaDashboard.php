<?php

namespace App\Livewire\Petugas;

use App\Actions\Dashboard\GetDashboardStatisticsAction;
use App\Actions\Dashboard\GetPetugasDashboardDataAction;
use App\DTOs\Dashboard\GetDashboardStatisticsDto;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard Petugas Pelaksana - Jangkau Samawa')]
class PelaksanaDashboard extends Component
{
    public function render(
        GetPetugasDashboardDataAction $petugasAction,
        GetDashboardStatisticsAction $legacyAction
    ): View {
        $user = auth()->user();

        $data = $petugasAction->handle($user);
        $legacyStatistics = $legacyAction->handle(new GetDashboardStatisticsDto($user));

        return view('livewire.petugas.pelaksana-dashboard', [
            'data' => $data,
            'statistics' => $legacyStatistics,
            'statusCounts' => $data['statusCounts'],
            'actionNeededComplaints' => $data['actionNeededComplaints'],
            'recentComplaints' => $data['recentComplaints'],
            'overdueComplaints' => $data['overdueComplaints'],
        ]);
    }
}
