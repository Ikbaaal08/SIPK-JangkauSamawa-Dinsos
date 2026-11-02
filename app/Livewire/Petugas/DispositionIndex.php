<?php

namespace App\Livewire\Petugas;

use App\Actions\Disposition\CreateDispositionAction;
use App\Forms\Disposition\DispositionForm;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Disposisi Bidang (Kabag) - Jangkau Samawa')]
class DispositionIndex extends Component
{
    use WithPagination;

    public DispositionForm $form;

    public string $search = '';

    public string $statusFilter = '';

    public bool $showForwardModal = false;

    public bool $showDetailModal = false;

    public ?Complaint $selectedComplaint = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function openForwardModal(int $complaintId): void
    {
        $complaint = Complaint::with(['category', 'dispositions.assignedBy', 'dispositions.assignedTo'])->findOrFail($complaintId);
        $this->selectedComplaint = $complaint;
        $this->form->setComplaint($complaint);
        $this->showForwardModal = true;
    }

    public function closeForwardModal(): void
    {
        $this->showForwardModal = false;
        $this->selectedComplaint = null;
    }

    public function submitForwardDisposition(CreateDispositionAction $action): void
    {
        $this->form->validate();

        try {
            $dto = $this->form->toDto(auth()->user());
            $action->handle($dto);

            session()->flash('success', "Disposisi lanjutan untuk aduan #{$this->selectedComplaint->ticket_code} berhasil diteruskan kepada Petugas Pelaksana.");
            $this->closeForwardModal();
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal memproses disposisi lanjutan: '.$e->getMessage());
        }
    }

    public function openDetailModal(int $complaintId): void
    {
        $this->selectedComplaint = Complaint::with([
            'category',
            'dispositions.assignedBy',
            'dispositions.assignedTo',
            'histories.user',
        ])->findOrFail($complaintId);

        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->selectedComplaint = null;
    }

    public function render(): View
    {
        // Kabag melihat aduan yang didisposisikan kepadanya oleh Kepala Dinas
        $userId = auth()->id();

        $query = Complaint::whereHas('dispositions', function ($q) use ($userId) {
            $q->where('assigned_to', $userId);
        })->with(['category', 'dispositions.assignedBy', 'dispositions.assignedTo'])
            ->latest('id');

        if ($this->search !== '') {
            $search = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($search) {
                $q->where('ticket_code', 'like', $search)
                    ->orWhere('title', 'like', $search)
                    ->orWhere('reporter_name', 'like', $search);
            });
        }

        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }

        $complaints = $query->paginate(10);

        // Penerima disposisi dari Kabag adalah Petugas Pelaksana
        $pelaksanaList = User::whereIn('role', ['petugas_pelaksana', 'petugas'])
            ->orderBy('name')
            ->get();

        return view('livewire.petugas.disposition-index', [
            'complaints' => $complaints,
            'pelaksanaList' => $pelaksanaList,
        ]);
    }
}
