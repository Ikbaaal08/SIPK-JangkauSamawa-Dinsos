<?php

namespace App\Livewire\Auth;

use App\Actions\Auth\SendWhatsAppVerificationAction;
use App\Actions\Auth\VerifyWhatsAppOtpAction;
use App\DTOs\Auth\SendWhatsAppOtpDto;
use App\DTOs\Auth\VerifyWhatsAppOtpDto;
use App\Forms\Auth\VerifyOtpForm;
use App\Services\FeatureService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.auth')]
#[Title('Verifikasi WhatsApp - Jangkau Samawa')]
class VerifyWhatsApp extends Component
{
    public VerifyOtpForm $form;

    public int $cooldownSeconds = 0;

    public ?string $errorMessage = null;

    public ?string $successMessage = null;

    public function mount(SendWhatsAppVerificationAction $sendAction): void
    {
        $user = Auth::user();

        if (! $user) {
            $this->redirect(route('login'), navigate: true);

            return;
        }

        if (! app(FeatureService::class)->isEnabled('email_verification') || $user->email_verified_at) {
            $this->redirect($user->dashboardRoute(), navigate: true);

            return;
        }

        $otpKey = "whatsapp_otp_{$user->id}";

        if (! Cache::has($otpKey)) {
            $sendAction->handle(new SendWhatsAppOtpDto($user));
        }

        $this->syncCooldown();
    }

    public function verify(VerifyWhatsAppOtpAction $verifyAction)
    {
        $this->errorMessage = null;
        $this->successMessage = null;

        $this->form->validate();

        $user = Auth::user();

        if (! $user) {
            return $this->redirect(route('login'), navigate: true);
        }

        $success = $verifyAction->handle(new VerifyWhatsAppOtpDto($user, $this->form->otp));

        if ($success) {
            Auth::login($user);

            return $this->redirect($user->dashboardRoute(), navigate: true);
        }

        $this->errorMessage = 'Kode OTP tidak valid atau sudah kedaluwarsa. Silakan periksa kembali atau kirim ulang kode baru.';
        $this->form->reset('otp');
    }

    public function resend(SendWhatsAppVerificationAction $sendAction): void
    {
        $this->errorMessage = null;
        $this->successMessage = null;

        $remaining = $this->getRemainingCooldown();

        if ($remaining > 0) {
            $this->errorMessage = "Harap tunggu {$remaining} detik sebelum meminta kode OTP baru.";

            return;
        }

        $user = Auth::user();

        if (! $user) {
            $this->redirect(route('login'), navigate: true);

            return;
        }

        $sent = $sendAction->handle(new SendWhatsAppOtpDto($user), force: true);

        if ($sent) {
            $this->successMessage = 'Kode OTP baru telah berhasil dikirim ke nomor WhatsApp Anda.';
        } else {
            $this->errorMessage = 'Gagal mengirim kode OTP ke WhatsApp. Pastikan nomor WhatsApp Anda aktif.';
        }

        $this->syncCooldown();
    }

    public function syncCooldown(): void
    {
        $this->cooldownSeconds = $this->getRemainingCooldown();
    }

    protected function getRemainingCooldown(): int
    {
        $user = Auth::user();

        if (! $user) {
            return 0;
        }

        $cooldownExpiry = Cache::get("whatsapp_otp_cooldown_{$user->id}");

        if (! $cooldownExpiry) {
            return 0;
        }

        return max(0, (int) ($cooldownExpiry - now()->timestamp));
    }

    public function render(): View
    {
        $user = Auth::user();
        $phone = $user?->phone ?? '';

        $maskedPhone = '';
        if (strlen($phone) >= 8) {
            $maskedPhone = substr($phone, 0, 4).'••••'.substr($phone, -4);
        } else {
            $maskedPhone = $phone;
        }

        return view('livewire.auth.verify-whatsapp', [
            'maskedPhone' => $maskedPhone,
        ]);
    }
}
