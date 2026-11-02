<?php

namespace App\Forms\Disposition;

use App\DTOs\Disposition\CreateDispositionData;
use App\Models\Complaint;
use App\Models\User;
use Livewire\Form;

class DispositionForm extends Form
{
    public ?Complaint $complaint = null;

    public ?int $assigned_to = null;

    public string $instruction = '';

    public ?string $due_date = null;

    public function setComplaint(Complaint $complaint): void
    {
        $this->complaint = $complaint;
        $this->reset(['assigned_to', 'instruction', 'due_date']);
        $this->resetValidation();
    }

    public function rules(): array
    {
        return [
            'assigned_to' => ['required', 'exists:users,id'],
            'instruction' => ['required', 'string', 'min:5', 'max:1000'],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'assigned_to.required' => 'Penerima disposisi wajib dipilih.',
            'assigned_to.exists' => 'Penerima disposisi yang dipilih tidak terdaftar.',
            'instruction.required' => 'Instruksi disposisi wajib diisi.',
            'instruction.min' => 'Instruksi disposisi minimal 5 karakter.',
            'instruction.max' => 'Instruksi disposisi maksimal 1000 karakter.',
            'due_date.date' => 'Format tenggat waktu tidak valid.',
            'due_date.after_or_equal' => 'Tenggat waktu tidak boleh tanggal lampau.',
        ];
    }

    public function toDto(User $assignedBy): CreateDispositionData
    {
        return new CreateDispositionData(
            complaint: $this->complaint,
            assignedBy: $assignedBy,
            assignedToId: (int) $this->assigned_to,
            instruction: trim($this->instruction),
            dueDate: $this->due_date ?: null,
        );
    }
}
