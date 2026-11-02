<?php

namespace App\Livewire\KepalaDinas;

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
#[Title('Disposisi Pimpinan - Jangkau Samawa')]
class DispositionIndex extends Component
{
    use WithPagination;

    public DispositionForm $form;

    public string $search = '';

    public string $statusFilter = '';

    public bool $showDispositionModal = false;

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

    public function openDispositionModal(int $complaintId): void
    {
        $complaint = Complaint::with(['category', 'dispositions.assignedBy', 'dispositions.assignedTo'])->findOrFail($complaintId);
        $this->selectedComplaint = $complaint;
        $this->form->setComplaint($complaint);
        $this->showDispositionModal = true;
    }

    public function closeDispositionModal(): void
    {
        $this->showDispositionModal = false;
        $this->selectedComplaint = null;
    }

    public function submitDisposition(CreateDispositionAction $action): void
    {
        $this->form->validate();

        try {
            $dto = $this->form->toDto(auth()->user());
            $action->handle($dto);

            session()->flash('success', "Disposisi untuk aduan #{$this->selectedComplaint->ticket_code} berhasil diteruskan kepada Kabag.");
            $this->closeDispositionModal();
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal memproses disposisi: '.$e->getMessage());
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
        $query = Complaint::with(['category', 'dispositions.assignedBy', 'dispositions.assignedTo'])
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

        // Penerima disposisi dari Kadis adalah Kabag / Petugas Penerima
        $penerimaList = User::whereIn('role', ['petugas_penerima', 'kabag'])
            ->orderBy('name')
            ->get();

        return view('livewire.kepala-dinas.disposition-index', [
            'complaints' => $complaints,
            'penerimaList' => $penerimaList,
        ]);
    }
}
