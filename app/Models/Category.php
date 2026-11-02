<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class);
    }

    public function getBidangAttribute(): string
    {
        if (isset($this->attributes['bidang']) && ! empty($this->attributes['bidang'])) {
            return (string) $this->attributes['bidang'];
        }

        return match (strtoupper($this->code ?? '')) {
            'BANSOS' => 'Bidang Perlindungan dan Jaminan Sosial (Linjamsos)',
            'DIFABEL' => 'Bidang Rehabilitasi Sosial (Rehsos)',
            'LANSIA' => 'Bidang Rehabilitasi Sosial (Rehsos)',
            'ANPER' => 'Bidang Perlindungan dan Pemberdayaan Sosial',
            'KEMSOS' => 'Bidang Pemberdayaan Sosial dan Penanganan Fakir Miskin (Dayasos)',
            default => ! empty($this->name) ? 'Bidang Pelayanan '.$this->name : 'Bidang Penanganan Teknis Terkait',
        };
    }
}
