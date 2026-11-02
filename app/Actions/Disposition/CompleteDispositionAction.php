<?php

namespace App\Actions\Disposition;

use App\Models\ComplaintHistory;
use App\Models\Disposition;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CompleteDispositionAction
{
    /**
     * Mark a disposition task and complaint as completed.
     *
     * @throws ValidationException
     */
    public function handle(Disposition $disposition, User $user, ?string $note = null): Disposition
    {
        return DB::transaction(function () use ($disposition, $user, $note) {
            if ($disposition->assigned_to !== $user->id && ! $user->isAdmin()) {
                throw ValidationException::withMessages([
                    'disposition' => 'Anda tidak memiliki wewenang untuk menyelesaikan disposisi ini.',
                ]);
            }

            $complaint = $disposition->complaint;
            $previousStatus = $complaint->status;

            // 1. Selesaikan disposisi
            $disposition->update([
                'status' => 'selesai',
            ]);

            // 2. Selesaikan aduan dan isi completed_at secara otomatis
            $complaint->update([
                'status' => 'selesai',
                'completed_at' => now(),
            ]);

            // 3. Catat riwayat status
            ComplaintHistory::create([
                'complaint_id' => $complaint->id,
                'user_id' => $user->id,
                'previous_status' => $previousStatus,
                'new_status' => 'selesai',
                'note' => $note ?: "Tindak lanjut penanganan aduan telah selesai dituntaskan oleh {$user->name} ({$user->roleLabel()}).",
            ]);

            return $disposition;
        });
    }
}
