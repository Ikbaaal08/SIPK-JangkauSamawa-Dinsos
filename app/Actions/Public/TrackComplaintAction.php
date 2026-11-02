<?php

namespace App\Actions\Public;

use App\DTOs\Public\TrackComplaintData;
use App\Models\Complaint;

class TrackComplaintAction
{
    /**
     * Find a complaint by ticket code with category and reverse-chronological histories.
     */
    public function handle(TrackComplaintData $data): ?Complaint
    {
        $code = trim($data->ticket_code);

        if ($code === '') {
            return null;
        }

        return Complaint::with([
            'category',
            'histories' => function ($query) {
                $query->orderBy('created_at', 'desc')->orderBy('id', 'desc');
            },
        ])
            ->where('ticket_code', $code)
            ->first();
    }
}
