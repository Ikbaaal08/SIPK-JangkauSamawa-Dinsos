<?php

use App\Actions\Auth\SendWhatsAppVerificationAction;
use App\Actions\Auth\VerifyWhatsAppOtpAction;
use App\DTOs\Auth\SendWhatsAppOtpDto;
use App\DTOs\Auth\VerifyWhatsAppOtpDto;
use App\Livewire\Auth\VerifyWhatsApp;
use App\Models\User;
use App\Models\WhatsappLog;
use App\Services\FeatureService;
use App\Services\FonnteService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

beforeEach(function () {
    Cache::flush();
    app(FeatureService::class)->enable('email_verification', 1);
    app(FeatureService::class)->enable('email_verification', 1);
});

test('fonnte service sends request to api and records to whatsapp_logs', function () {
    Http::fake([
        'https://api.fonnte.com/send' => Http::response([
            'status' => true,
            'target' => ['081234567890'],
        ], 200),
    ]);

    $service = new FonnteService('test-token-fonnte');
    $result = $service->send('081234567890', 'Pesan uji coba OTP');

    expect($result)->toBeTrue();

    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.fonnte.com/send'
            && $request->hasHeader('Authorization', 'test-token-fonnte')
            && $request['target'] === '081234567890'
            && $request['message'] === 'Pesan uji coba OTP';
    });

    $log = WhatsappLog::latest()->first();
    expect($log)->not->toBeNull()
        ->and($log->phone)->toBe('081234567890')
        ->and($log->direction)->toBe('keluar')
        ->and($log->message)->toBe('Pesan uji coba OTP')
        ->and($log->status)->toBe('terkirim')
        ->and($log->sent_at)->not->toBeNull();
});

test('fonnte service handles failure and records status gagal in whatsapp_logs', function () {
    Http::fake([
        'https://api.fonnte.com/send' => Http::response([
            'status' => false,
            'reason' => 'device disconnected',
        ], 500),
    ]);

    $service = new FonnteService('test-token-fonnte');
    $result = $service->send('081234567890', 'Pesan gagal');

    expect($result)->toBeFalse();

    $log = WhatsappLog::latest()->first();
    expect($log)->not->toBeNull()
        ->and($log->status)->toBe('gagal');
});

test('send whatsapp verification action creates 6 digit otp valid for 10 minutes and sets 60s cooldown', function () {
    Http::fake([
        'https://api.fonnte.com/send' => Http::response(['status' => true], 200),
    ]);

    $user = User::factory()->petugasPenerima()->create([
        'phone' => '081234567890',
        'email_verified_at' => null,
    ]);

    $action = app(SendWhatsAppVerificationAction::class);
    $result = $action->handle(new SendWhatsAppOtpDto($user));

    expect($result)->toBeTrue();

    $cached = Cache::get("whatsapp_otp_{$user->id}");
    expect($cached)->not->toBeNull()
        ->and($cached['code'])->toMatch('/^\d{6}$/')
        ->and($cached['expires_at'])->toBeGreaterThan(now()->timestamp);

    expect(Cache::has("whatsapp_otp_cooldown_{$user->id}"))->toBeTrue();
});

test('send whatsapp verification action respects 60s cooldown unless forced', function () {
    Http::fake([
        'https://api.fonnte.com/send' => Http::response(['status' => true], 200),
    ]);

    $user = User::factory()->petugasPenerima()->create([
        'phone' => '081234567890',
        'email_verified_at' => null,
    ]);

    $action = app(SendWhatsAppVerificationAction::class);

    // Kirim pertama kali (sukses)
    expect($action->handle(new SendWhatsAppOtpDto($user)))->toBeTrue();

    // Kirim kedua kali saat masih cooldown tanpa force (harus ditolak)
    expect($action->handle(new SendWhatsAppOtpDto($user)))->toBeFalse();

    // Kirim dengan force (diperbolehkan)
    expect($action->handle(new SendWhatsAppOtpDto($user), force: true))->toBeTrue();
});

test('verify whatsapp otp action marks user as verified when code is correct', function () {
    $user = User::factory()->petugasPenerima()->create([
        'email_verified_at' => null,
    ]);

    Cache::put("whatsapp_otp_{$user->id}", [
        'code' => '654321',
        'expires_at' => now()->addMinutes(10)->timestamp,
    ], now()->addMinutes(10));

    Cache::put("whatsapp_otp_cooldown_{$user->id}", now()->addSeconds(60)->timestamp, 60);

    $action = app(VerifyWhatsAppOtpAction::class);
    $result = $action->handle(new VerifyWhatsAppOtpDto($user, '654321'));

    expect($result)->toBeTrue()
        ->and($user->fresh()->email_verified_at)->not->toBeNull();

    expect(Cache::has("whatsapp_otp_{$user->id}"))->toBeFalse();
    expect(Cache::has("whatsapp_otp_cooldown_{$user->id}"))->toBeFalse();
});

test('verify whatsapp otp action fails when code is incorrect', function () {
    $user = User::factory()->petugasPenerima()->create([
        'email_verified_at' => null,
    ]);

    Cache::put("whatsapp_otp_{$user->id}", [
        'code' => '654321',
        'expires_at' => now()->addMinutes(10)->timestamp,
    ], now()->addMinutes(10));

    $action = app(VerifyWhatsAppOtpAction::class);
    $result = $action->handle(new VerifyWhatsAppOtpDto($user, '999999'));

    expect($result)->toBeFalse()
        ->and($user->fresh()->email_verified_at)->toBeNull();

    expect(Cache::has("whatsapp_otp_{$user->id}"))->toBeTrue();
});

test('verify whatsapp otp action fails when otp is expired or not found', function () {
    $user = User::factory()->petugasPenerima()->create([
        'email_verified_at' => null,
    ]);

    $action = app(VerifyWhatsAppOtpAction::class);
    $result = $action->handle(new VerifyWhatsAppOtpDto($user, '123456'));

    expect($result)->toBeFalse()
        ->and($user->fresh()->email_verified_at)->toBeNull();
});

test('unverified user cannot access dashboard and is redirected to whatsapp verification', function () {
    $user = User::factory()->admin()->create([
        'email_verified_at' => null,
    ]);

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertRedirect(route('verification.whatsapp'));

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('verification.whatsapp'));
});

test('whatsapp verification page can be rendered by authenticated unverified user', function () {
    Http::fake();

    $user = User::factory()->petugasPenerima()->create([
        'phone' => '081234567890',
        'email_verified_at' => null,
    ]);

    $this->actingAs($user)
        ->get(route('verification.whatsapp'))
        ->assertOk()
        ->assertSee('Verifikasi Nomor WhatsApp')
        ->assertSee('0812••••7890');
});

test('already verified user visiting whatsapp verification is redirected to role dashboard', function () {
    $user = User::factory()->admin()->create([
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('verification.whatsapp'))
        ->assertRedirect(route('admin.dashboard'));
});

test('livewire verify whatsapp component successfully verifies and redirects to role dashboard', function () {
    $user = User::factory()->kepalaDinas()->create([
        'phone' => '081234567892',
        'email_verified_at' => null,
    ]);

    Cache::put("whatsapp_otp_{$user->id}", [
        'code' => '456789',
        'expires_at' => now()->addMinutes(10)->timestamp,
    ], now()->addMinutes(10));

    Livewire::actingAs($user)
        ->test(VerifyWhatsApp::class)
        ->set('form.otp', '456789')
        ->call('verify')
        ->assertHasNoErrors()
        ->assertRedirect(route('kepala-dinas.dashboard'));

    expect($user->fresh()->email_verified_at)->not->toBeNull();
});

test('livewire verify whatsapp component shows indonesian error on invalid otp', function () {
    $user = User::factory()->petugasPelaksana()->create([
        'phone' => '081234567893',
        'email_verified_at' => null,
    ]);

    Cache::put("whatsapp_otp_{$user->id}", [
        'code' => '111111',
        'expires_at' => now()->addMinutes(10)->timestamp,
    ], now()->addMinutes(10));

    Livewire::actingAs($user)
        ->test(VerifyWhatsApp::class)
        ->set('form.otp', '000000')
        ->call('verify')
        ->assertSee('Kode OTP tidak valid atau sudah kedaluwarsa');

    expect($user->fresh()->email_verified_at)->toBeNull();
});

test('livewire verify whatsapp component handles resend with cooldown', function () {
    Http::fake();

    $user = User::factory()->petugasPenerima()->create([
        'phone' => '081234567891',
        'email_verified_at' => null,
    ]);

    $component = Livewire::actingAs($user)
        ->test(VerifyWhatsApp::class);

    // Cooldown aktif (baru saja mount / dikirim)
    $component->call('resend')
        ->assertSee('Harap tunggu');

    // Hapus cooldown paksa
    Cache::forget("whatsapp_otp_cooldown_{$user->id}");

    $component->call('resend')
        ->assertSee('Kode OTP baru telah berhasil dikirim ke nomor WhatsApp Anda.');
});

test('verified user logging in is not asked for whatsapp verification', function () {
    $user = User::factory()->petugasPenerima()->create([
        'phone' => '081234567891',
        'password' => bcrypt('password'),
        'email_verified_at' => now(),
    ]);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('petugas.dashboard'));
    $this->assertAuthenticatedAs($user);
});
