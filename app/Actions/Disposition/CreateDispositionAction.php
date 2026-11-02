<?php

namespace App\Actions\Disposition;

use App\DTOs\Disposition\CreateDispositionData;
use App\Models\ComplaintHistory;
use App\Models\Disposition;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateDispositionAction
{
    /**
     * Create a new disposition in the hierarchy chain using DB transaction.
     *
     * @throws ValidationException
     */
    public function handle(CreateDispositionData $data): Disposition
    {
        return DB::transaction(function () use ($data) {
            $assignee = User::findOrFail($data->assignedToId);
            $assignedBy = $data->assignedBy;

            // Validasi hierarki berjenjang:
            // 1. Kadis -> Kabag (petugas_penerima)
            // 2. Kabag -> Petugas (petugas_pelaksana)
            if ($assignedBy->isKepalaDinas() && ! $assignee->isPetugasPenerima()) {
                throw ValidationException::withMessages([
                    'assigned_to' => 'Kepala Dinas hanya dapat memberikan disposisi kepada Kabag / Petugas Penerima.',
                ]);
            }

            if ($assignedBy->isPetugasPenerima() && ! $assignee->isPetugasPelaksana()) {
                throw ValidationException::withMessages([
                    'assigned_to' => 'Kabag hanya dapat mendisposisikan aduan kepada Petugas Pelaksana.',
                ]);
            }

            // 1. Simpan data disposisi
            $disposition = Disposition::create([
                'complaint_id' => $data->complaint->id,
                'assigned_by' => $assignedBy->id,
                'assigned_to' => $assignee->id,
                'instruction' => $data->instruction,
                'due_date' => $data->dueDate,
                'status' => 'aktif',
            ]);

            // 2. Perbarui status aduan menjadi didisposisikan
            $previousStatus = $data->complaint->status;
            $data->complaint->update([
                'status' => 'didisposisikan',
            ]);

            // 3. Catat riwayat status aduan
            $notePrefix = $assignedBy->isKepalaDinas()
                ? "Disposisi Pimpinan (Kadis) kepada {$assignee->name} ({$assignee->roleLabel()}): "
                : "Disposisi Berjenjang (Kabag) kepada {$assignee->name} ({$assignee->roleLabel()}): ";

            ComplaintHistory::create([
                'complaint_id' => $data->complaint->id,
                'user_id' => $assignedBy->id,
                'previous_status' => $previousStatus,
                'new_status' => 'didisposisikan',
                'note' => $notePrefix.$data->instruction,
            ]);

            return $disposition;
        });
    }
}
