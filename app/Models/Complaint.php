<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_code',
        'reporter_name',
        'reporter_phone',
        'reporter_address',
        'category_id',
        'title',
        'description',
        'village',
        'district',
        'status',
        'reported_at',
        'completed_at',
    ];

    protected $casts = [
        'reported_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function dispositions()
    {
        return $this->hasMany(Disposition::class);
    }

    public function histories()
    {
        return $this->hasMany(ComplaintHistory::class);
    }

    public function whatsappLogs()
    {
        return $this->hasMany(WhatsappLog::class);
    }

    public function getBidangAttribute(): string
    {
        return $this->category?->bidang ?? 'Bidang Penanganan Terkait';
    }

    public function getMaskedPhoneAttribute(): string
    {
        $phone = (string) $this->reporter_phone;
        $length = strlen($phone);

        if ($length <= 7) {
            return substr($phone, 0, 2).'****'.substr($phone, -2);
        }

        return substr($phone, 0, 5).'****'.substr($phone, -3);
    }

    public function getIsOverdueAttribute(): bool
    {
        if (in_array($this->status, ['selesai', 'ditolak'], true)) {
            return false;
        }

        $dispositions = $this->relationLoaded('dispositions')
            ? $this->dispositions
            : $this->dispositions()->get();

        return $dispositions
            ->whereNotNull('due_date')
            ->contains(function ($disp) {
                $dueDate = $disp->due_date instanceof CarbonInterface
                    ? $disp->due_date->toDateString()
                    : (string) $disp->due_date;

                return $dueDate < now()->toDateString();
            });
    }

    public function getActiveDueDateAttribute(): ?string
    {
        $dispositions = $this->relationLoaded('dispositions')
            ? $this->dispositions
            : $this->dispositions()->get();

        $disp = $dispositions->whereNotNull('due_date')->sortByDesc('due_date')->first();

        return $disp?->due_date ? Carbon::parse($disp->due_date)->translatedFormat('d M Y') : null;
    }
}
