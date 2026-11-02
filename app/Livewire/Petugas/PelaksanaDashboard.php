<?php

namespace App\Livewire\Petugas;

use App\Actions\Dashboard\GetDashboardStatisticsAction;
use App\DTOs\Dashboard\GetDashboardStatisticsDto;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard Petugas Pelaksana - Jangkau Samawa')]
class PelaksanaDashboard extends Component
{
    public function render(GetDashboardStatisticsAction $action): View
    {
        $statistics = $action->handle(new GetDashboardStatisticsDto(auth()->user()));

        return view('livewire.petugas.pelaksana-dashboard', [
            'statistics' => $statistics,
        ]);
    }
}
