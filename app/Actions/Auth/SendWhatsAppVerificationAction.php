<?php

namespace App\Actions\Auth;

use App\DTOs\Auth\SendWhatsAppOtpDto;
use App\Services\FonnteService;
use Illuminate\Support\Facades\Cache;

class SendWhatsAppVerificationAction
{
    public function __construct(
        protected FonnteService $fonnteService,
    ) {}

    /**
     * Generate and send a 6-digit WhatsApp OTP valid for 10 minutes.
     */
    public function handle(SendWhatsAppOtpDto $dto, bool $force = false): bool
    {
        $user = $dto->user;
        $otpKey = "whatsapp_otp_{$user->id}";
        $cooldownKey = "whatsapp_otp_cooldown_{$user->id}";

        if (! $force && Cache::has($cooldownKey)) {
            return false;
        }

        $otp = (string) random_int(100000, 999999);

        // Simpan OTP berlaku 10 menit (600 detik)
        Cache::put($otpKey, [
            'code' => $otp,
            'expires_at' => now()->addMinutes(10)->timestamp,
        ], now()->addMinutes(10));

        // Set pembatasan waktu (cooldown) 60 detik untuk kirim ulang
        Cache::put($cooldownKey, now()->addSeconds(60)->timestamp, now()->addSeconds(60));

        $phone = $user->phone ?? '';

        if (empty($phone)) {
            return false;
        }

        $message = "Halo {$user->name}, kode verifikasi (OTP) Jangkau Samawa Anda adalah *{$otp}*.\n\nKode ini berlaku selama 10 menit. Jangan bagikan kode ini kepada siapapun.";

        return $this->fonnteService->send($phone, $message);
    }
}
