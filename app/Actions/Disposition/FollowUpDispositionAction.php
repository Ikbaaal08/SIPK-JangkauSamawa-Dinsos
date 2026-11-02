<?php

namespace App\Actions\Disposition;

use App\Models\ComplaintHistory;
use App\Models\Disposition;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FollowUpDispositionAction
{
    /**
     * Mark a disposition task as in-progress / being followed up.
     *
     * @throws ValidationException
     */
    public function handle(Disposition $disposition, User $user, ?string $note = null): Disposition
    {
        return DB::transaction(function () use ($disposition, $user, $note) {
            if ($disposition->assigned_to !== $user->id && ! $user->isAdmin()) {
                throw ValidationException::withMessages([
                    'disposition' => 'Anda tidak memiliki wewenang untuk menindaklanjuti disposisi ini.',
                ]);
            }

            $complaint = $disposition->complaint;
            $previousStatus = $complaint->status;

            $complaint->update([
                'status' => 'ditindaklanjuti',
            ]);

            ComplaintHistory::create([
                'complaint_id' => $complaint->id,
                'user_id' => $user->id,
                'previous_status' => $previousStatus,
                'new_status' => 'ditindaklanjuti',
                'note' => $note ?: "Disposisi sedang ditindaklanjuti oleh {$user->name} ({$user->roleLabel()}).",
            ]);

            return $disposition;
        });
    }
}
