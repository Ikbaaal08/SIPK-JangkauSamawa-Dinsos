<?php

namespace App\Actions\Auth;

use App\DTOs\Auth\VerifyWhatsAppOtpDto;
use Illuminate\Support\Facades\Cache;

class VerifyWhatsAppOtpAction
{
    /**
     * Verify the 6-digit WhatsApp OTP.
     */
    public function handle(VerifyWhatsAppOtpDto $dto): bool
    {
        $user = $dto->user;
        $otpKey = "whatsapp_otp_{$user->id}";
        $cached = Cache::get($otpKey);

        if (! $cached || ! isset($cached['code'])) {
            return false;
        }

        if ((string) $cached['code'] !== trim($dto->otp)) {
            return false;
        }

        // Tandai pengguna sebagai terverifikasi
        $user->forceFill([
            'email_verified_at' => now(),
        ])->save();

        Cache::forget($otpKey);
        Cache::forget("whatsapp_otp_cooldown_{$user->id}");

        return true;
    }
}
