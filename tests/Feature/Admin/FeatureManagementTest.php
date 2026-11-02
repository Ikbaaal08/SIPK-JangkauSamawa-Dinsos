<?php

use App\Livewire\Admin\FeatureManagement;
use App\Models\User;
use App\Services\FeatureService;
use Database\Seeders\FeatureFlagSeeder;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

beforeEach(function () {
    Cache::flush();
    $this->seed(FeatureFlagSeeder::class);
});

test('1. Super admin dapat mengakses halaman Kelola Fitur', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $response = $this->actingAs($superAdmin)->get(route('admin.features.index'));

    $response->assertOk();

    Livewire::actingAs($superAdmin)
        ->test(FeatureManagement::class)
        ->assertSee('Kelola Fitur Sistem')
        ->assertSee('Registrasi Mandiri')
        ->assertSee('Verifikasi Email');
});

test('2. Admin tidak dapat mengakses halaman Kelola Fitur', function () {
    $admin = User::factory()->admin()->create();
    $petugas = User::factory()->petugas()->create();
    $kadis = User::factory()->kadis()->create();

    $this->actingAs($admin)->get(route('admin.features.index'))->assertForbidden();
    $this->actingAs($petugas)->get(route('admin.features.index'))->assertForbidden();
    $this->actingAs($kadis)->get(route('admin.features.index'))->assertForbidden();
});

test('3. Super admin dapat mengaktifkan fitur registration', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $featureService = app(FeatureService::class);
    $featureService->disable('registration', $superAdmin->id);

    expect($featureService->isEnabled('registration'))->toBeFalse();

    Livewire::actingAs($superAdmin)
        ->test(FeatureManagement::class)
        ->call('toggle', 'registration')
        ->assertHasNoErrors();

    expect($featureService->isEnabled('registration'))->toBeTrue();

    $this->assertDatabaseHas('feature_flags', [
        'key' => 'registration',
        'is_enabled' => true,
        'updated_by' => $superAdmin->id,
    ]);
});

test('4. Super admin dapat menonaktifkan fitur registration', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $featureService = app(FeatureService::class);
    $featureService->enable('registration', $superAdmin->id);

    expect($featureService->isEnabled('registration'))->toBeTrue();

    Livewire::actingAs($superAdmin)
        ->test(FeatureManagement::class)
        ->call('toggle', 'registration')
        ->assertHasNoErrors();

    expect($featureService->isEnabled('registration'))->toBeFalse();

    $this->assertDatabaseHas('feature_flags', [
        'key' => 'registration',
        'is_enabled' => false,
        'updated_by' => $superAdmin->id,
    ]);
});

test('5. Ketika registration OFF, route register tidak dapat diakses', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    app(FeatureService::class)->disable('registration', $superAdmin->id);

    $response = $this->get(route('register'));

    $response->assertRedirect(route('login'))
        ->assertSessionHas('status', 'Pendaftaran mandiri sedang dinonaktifkan. Silakan hubungi administrator.');
});

test('6. Ketika registration OFF, link Daftar tidak muncul di halaman login', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    app(FeatureService::class)->disable('registration', $superAdmin->id);

    $response = $this->get(route('login'));

    $response->assertOk()
        ->assertDontSee('href="'.route('register').'"', false);
});

test('7. Ketika registration ON, route register dapat diakses kembali', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    app(FeatureService::class)->enable('registration', $superAdmin->id);

    // Route register terbuka
    $response = $this->get(route('register'));
    $response->assertOk();

    // Link daftar muncul di login
    $loginResponse = $this->get(route('login'));
    $loginResponse->assertOk()
        ->assertSee('href="'.route('register').'"', false);
});

test('8. Super admin dapat mengaktifkan dan menonaktifkan email_verification', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $featureService = app(FeatureService::class);

    // 1. Aktifkan dari kondisi awal
    $featureService->disable('email_verification', $superAdmin->id);
    Livewire::actingAs($superAdmin)
        ->test(FeatureManagement::class)
        ->call('toggle', 'email_verification')
        ->assertHasNoErrors();

    expect($featureService->isEnabled('email_verification'))->toBeTrue();

    // 2. Nonaktifkan (harus melalui konfirmasi modal)
    Livewire::actingAs($superAdmin)
        ->test(FeatureManagement::class)
        ->call('toggle', 'email_verification')
        ->assertSet('showConfirmModal', true)
        ->assertSet('confirmingKey', 'email_verification')
        ->call('confirmToggle')
        ->assertSet('showConfirmModal', false);

    expect($featureService->isEnabled('email_verification'))->toBeFalse();
});

test('9. Ketika email_verification OFF, pengguna belum terverifikasi dapat masuk dashboard', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    app(FeatureService::class)->disable('email_verification', $superAdmin->id);

    $unverifiedUser = User::factory()->petugasPelaksana()->unverified()->create();

    // Akses route /dashboard
    $response = $this->actingAs($unverifiedUser)->get(route('dashboard'));

    // Langsung diarahkan ke dashboard perannya, BUKAN ke verify-whatsapp
    $response->assertRedirect($unverifiedUser->dashboardRoute());
    $this->assertAuthenticatedAs($unverifiedUser);

    // Mengakses dashboardnya secara langsung diizinkan (200 OK)
    $dashboardResponse = $this->actingAs($unverifiedUser)->get(route('pelaksana.dashboard'));
    $dashboardResponse->assertOk();
});

test('10. Ketika email_verification ON, pengguna belum terverifikasi diarahkan ke verifikasi email', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    app(FeatureService::class)->enable('email_verification', $superAdmin->id);

    $unverifiedUser = User::factory()->petugasPelaksana()->unverified()->create();

    // Akses route /dashboard
    $response = $this->actingAs($unverifiedUser)->get(route('dashboard'));

    // Diarahkan ke halaman verifikasi
    $response->assertRedirect(route('verification.whatsapp'));

    // Akses langsung ke dashboard ditahan oleh middleware CheckRole dan dialihkan ke verifikasi
    $dashboardResponse = $this->actingAs($unverifiedUser)->get(route('pelaksana.dashboard'));
    $dashboardResponse->assertRedirect(route('verification.whatsapp'));

    // Akun yang sudah terverifikasi tetap dapat mengakses dashboard secara normal
    $verifiedUser = User::factory()->admin()->create();
    $this->actingAs($verifiedUser)->get(route('admin.dashboard'))->assertOk();
});
