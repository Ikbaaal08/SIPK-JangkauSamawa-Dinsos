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
            'admin' => 'Administrator',
            'petugas_penerima' => 'Petugas Penerima',
            'kepala_dinas' => 'Kepala Dinas',
            'petugas_pelaksana' => 'Petugas Pelaksana',
            default => 'Pengguna',
        };
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPetugasPenerima(): bool
    {
        return $this->role === 'petugas_penerima';
    }

    public function isKepalaDinas(): bool
    {
        return $this->role === 'kepala_dinas';
    }

    public function isPetugasPelaksana(): bool
    {
        return $this->role === 'petugas_pelaksana';
    }

    public function dashboardRoute(): string
    {
        return match ($this->role) {
            'admin' => route('admin.dashboard'),
            'petugas_penerima' => route('petugas.dashboard'),
            'kepala_dinas' => route('kepala-dinas.dashboard'),
            'petugas_pelaksana' => route('pelaksana.dashboard'),
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
