<?php

namespace App\Livewire\KepalaDinas;

use App\Actions\Dashboard\GetDashboardStatisticsAction;
use App\DTOs\Dashboard\GetDashboardStatisticsDto;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard Kepala Dinas - Jangkau Samawa')]
class Dashboard extends Component
{
    public function render(GetDashboardStatisticsAction $action): View
    {
        $statistics = $action->handle(new GetDashboardStatisticsDto(auth()->user()));

        return view('livewire.kepala-dinas.dashboard', [
            'statistics' => $statistics,
        ]);
    }
}
