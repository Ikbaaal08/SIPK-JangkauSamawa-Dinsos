<?php

use App\Livewire\Admin\Category\CategoryIndex;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\FeatureManagement;
use App\Livewire\Auth\VerifyWhatsApp;
use App\Livewire\KepalaDinas\Dashboard as KepalaDinasDashboard;
use App\Livewire\KepalaDinas\DispositionIndex as KepalaDinasDispositionIndex;
use App\Livewire\Petugas\Dashboard as PetugasDashboard;
use App\Livewire\Petugas\DispositionIndex as PetugasDispositionIndex;
use App\Livewire\Petugas\PelaksanaDashboard;
use App\Livewire\Petugas\PelaksanaDispositionIndex;
use App\Livewire\Public\TrackComplaint;
use App\Services\FeatureService;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('lacak', TrackComplaint::class)
    ->middleware('public_tracking.enabled')
    ->name('public.tracking.index');

Route::middleware(['auth'])->group(function () {
    Route::get('verify-whatsapp', VerifyWhatsApp::class)->name('verification.whatsapp');

    Route::get('dashboard', function () {
        $user = auth()->user();

        if (app(FeatureService::class)->isEnabled('email_verification') && $user && ! $user->email_verified_at) {
            return redirect()->route('verification.whatsapp');
        }

        return redirect()->to($user ? $user->dashboardRoute() : route('login'));
    })->name('dashboard');

    Route::middleware(['role:super_admin,admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', AdminDashboard::class)->name('dashboard');
        Route::get('categories', CategoryIndex::class)->name('categories.index');
    });

    Route::middleware(['role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('fitur', FeatureManagement::class)->name('features.index');
    });

    Route::middleware(['role:petugas_penerima,kabag'])->prefix('petugas')->name('petugas.')->group(function () {
        Route::get('dashboard', PetugasDashboard::class)->name('dashboard');
        Route::get('dispositions', PetugasDispositionIndex::class)->name('dispositions.index');
    });

    Route::middleware(['role:kepala_dinas,kadis'])->prefix('kepala-dinas')->name('kepala-dinas.')->group(function () {
        Route::get('dashboard', KepalaDinasDashboard::class)->name('dashboard');
        Route::get('dispositions', KepalaDinasDispositionIndex::class)->name('dispositions.index');
    });

    Route::middleware(['role:petugas_pelaksana,petugas'])->prefix('pelaksana')->name('pelaksana.')->group(function () {
        Route::get('dashboard', PelaksanaDashboard::class)->name('dashboard');
        Route::get('dispositions', PelaksanaDispositionIndex::class)->name('dispositions.index');
    });
});

require __DIR__.'/settings.php';
