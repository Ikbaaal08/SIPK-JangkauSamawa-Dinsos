<?php

use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Auth\VerifyWhatsApp;
use App\Livewire\KepalaDinas\Dashboard as KepalaDinasDashboard;
use App\Livewire\Petugas\Dashboard as PetugasDashboard;
use App\Livewire\Petugas\PelaksanaDashboard;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('verify-whatsapp', VerifyWhatsApp::class)->name('verification.whatsapp');

    Route::get('dashboard', function () {
        $user = auth()->user();

        if ($user && ! $user->email_verified_at) {
            return redirect()->route('verification.whatsapp');
        }

        return redirect()->to($user ? $user->dashboardRoute() : route('login'));
    })->name('dashboard');

    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', AdminDashboard::class)->name('dashboard');
    });

    Route::middleware(['role:petugas_penerima'])->prefix('petugas')->name('petugas.')->group(function () {
        Route::get('dashboard', PetugasDashboard::class)->name('dashboard');
    });

    Route::middleware(['role:kepala_dinas'])->prefix('kepala-dinas')->name('kepala-dinas.')->group(function () {
        Route::get('dashboard', KepalaDinasDashboard::class)->name('dashboard');
    });

    Route::middleware(['role:petugas_pelaksana'])->prefix('pelaksana')->name('pelaksana.')->group(function () {
        Route::get('dashboard', PelaksanaDashboard::class)->name('dashboard');
    });
});

require __DIR__.'/settings.php';
