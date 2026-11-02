<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
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
}
