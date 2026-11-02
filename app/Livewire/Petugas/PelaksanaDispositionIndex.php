<?php

namespace App\Livewire\Petugas;

use App\Actions\Disposition\CompleteDispositionAction;
use App\Actions\Disposition\FollowUpDispositionAction;
use App\Models\Complaint;
use App\Models\Disposition;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Tugas Disposisi Pelaksana - Jangkau Samawa')]
class PelaksanaDispositionIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public bool $showActionModal = false;

    public bool $showDetailModal = false;

    public ?Disposition $selectedDisposition = null;

    public ?Complaint $selectedComplaint = null;

    public string $actionType = ''; // 'tindaklanjuti' or 'selesai'

    public string $actionNote = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function openActionModal(int $dispositionId, string $type): void
    {
        $this->selectedDisposition = Disposition::with(['complaint.category', 'assignedBy'])
            ->where('assigned_to', auth()->id())
            ->findOrFail($dispositionId);

        $this->actionType = $type;
        $this->actionNote = '';
        $this->showActionModal = true;
    }

    public function closeActionModal(): void
    {
        $this->showActionModal = false;
        $this->selectedDisposition = null;
        $this->actionType = '';
        $this->actionNote = '';
    }

    public function submitAction(FollowUpDispositionAction $followUpAction, CompleteDispositionAction $completeAction): void
    {
        if (! $this->selectedDisposition) {
            return;
        }

        try {
            $user = auth()->user();

            if ($this->actionType === 'tindaklanjuti') {
                $followUpAction->handle($this->selectedDisposition, $user, $this->actionNote ?: null);
                session()->flash('success', "Aduan #{$this->selectedDisposition->complaint->ticket_code} berhasil ditandai sedang ditindaklanjuti di lapangan.");
            } elseif ($this->actionType === 'selesai') {
                $completeAction->handle($this->selectedDisposition, $user, $this->actionNote ?: null);
                session()->flash('success', "Aduan #{$this->selectedDisposition->complaint->ticket_code} telah berhasil diselesaikan tuntas.");
            }

            $this->closeActionModal();
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal memproses aksi: '.$e->getMessage());
        }
    }

    public function openDetailModal(int $complaintId): void
    {
        // Petugas pelaksana hanya dapat melihat aduan yang didisposisikan kepadanya
        $userId = auth()->id();

        $this->selectedComplaint = Complaint::whereHas('dispositions', function ($q) use ($userId) {
            $q->where('assigned_to', $userId);
        })->with([
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
        // Petugas pelaksana HANYA melihat aduan yang didisposisikan kepadanya
        $userId = auth()->id();

        $query = Disposition::where('assigned_to', $userId)
            ->with(['complaint.category', 'assignedBy', 'assignedTo'])
            ->latest('id');

        if ($this->search !== '') {
            $search = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($search) {
                $q->where('instruction', 'like', $search)
                    ->orWhereHas('complaint', function ($cq) use ($search) {
                        $cq->where('ticket_code', 'like', $search)
                            ->orWhere('title', 'like', $search)
                            ->orWhere('reporter_name', 'like', $search);
                    });
            });
        }

        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }

        $dispositions = $query->paginate(10);

        return view('livewire.petugas.pelaksana-disposition-index', [
            'dispositions' => $dispositions,
        ]);
    }
}
