<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'bidang',
        'phone',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            'super_admin' => 'Super Admin',
            'admin' => 'Administrator',
            'kabag', 'petugas_penerima' => 'Petugas Penerima',
            'kadis', 'kepala_dinas' => 'Kepala Dinas',
            'petugas', 'petugas_pelaksana' => 'Petugas Pelaksana',
            default => 'Pengguna',
        };
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin'], true);
    }

    public function isPetugasPenerima(): bool
    {
        return in_array($this->role, ['petugas_penerima', 'kabag'], true);
    }

    public function isKepalaDinas(): bool
    {
        return in_array($this->role, ['kepala_dinas', 'kadis'], true);
    }

    public function isPetugasPelaksana(): bool
    {
        return in_array($this->role, ['petugas_pelaksana', 'petugas'], true);
    }

    public function dashboardRoute(): string
    {
        return match ($this->role) {
            'super_admin', 'admin' => route('admin.dashboard'),
            'kabag', 'petugas_penerima' => route('petugas.dashboard'),
            'kadis', 'kepala_dinas' => route('kepala-dinas.dashboard'),
            'petugas', 'petugas_pelaksana' => route('pelaksana.dashboard'),
            default => route('dashboard'),
        };
    }

    public function complaintsAssigned()
    {
        return $this->hasMany(Disposition::class, 'assigned_to');
    }

    public function dispositionsGiven()
    {
        return $this->hasMany(Disposition::class, 'assigned_by');
    }

    public function histories()
    {
        return $this->hasMany(ComplaintHistory::class);
    }
}
