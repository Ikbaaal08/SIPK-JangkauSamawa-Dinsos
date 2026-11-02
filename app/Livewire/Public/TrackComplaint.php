<?php

namespace App\Livewire\Public;

use App\Actions\Public\TrackComplaintAction;
use App\Forms\Public\TrackComplaintForm;
use App\Models\Complaint;
use App\Services\FeatureService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.public')]
#[Title('Lacak Aduan - Jangkau Samawa')]
class TrackComplaint extends Component
{
    public TrackComplaintForm $form;

    public ?Complaint $complaint = null;

    public bool $hasSearched = false;

    public ?string $errorMessage = null;

    /**
     * Check feature flag and prefill search if query string present.
     */
    public function mount(FeatureService $featureService, TrackComplaintAction $action): mixed
    {
        if (! $featureService->isEnabled('public_tracking')) {
            return redirect()->route('login')
                ->with('status', 'Fitur pelacakan aduan sedang dinonaktifkan.');
        }

        $ticket = request()->query('ticket') ?? request()->query('code');
        if (filled($ticket)) {
            $this->form->ticket_code = trim((string) $ticket);
            $this->track($action);
        }

        return null;
    }

    /**
     * Perform the tracking search using Action and Form DTO.
     */
    public function track(TrackComplaintAction $action): void
    {
        $this->errorMessage = null;
        $this->complaint = null;
        $this->hasSearched = true;

        $dto = $this->form->toDTO();

        $this->complaint = $action->handle($dto);

        if (! $this->complaint) {
            $this->errorMessage = 'Aduan dengan kode tiket tersebut tidak ditemukan.';
        }
    }

    /**
     * Reset the search state.
     */
    public function resetSearch(): void
    {
        $this->form->reset();
        $this->complaint = null;
        $this->hasSearched = false;
        $this->errorMessage = null;
        $this->resetErrorBag();
    }

    /**
     * Render the Livewire component.
     */
    public function render()
    {
        return view('livewire.public.track-complaint');
    }
}
