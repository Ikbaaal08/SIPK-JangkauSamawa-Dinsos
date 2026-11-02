<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappLog extends Model
{
    protected $fillable = [
        'complaint_id',
        'phone',
        'direction',
        'message',
        'message_type',
        'status',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }
}
