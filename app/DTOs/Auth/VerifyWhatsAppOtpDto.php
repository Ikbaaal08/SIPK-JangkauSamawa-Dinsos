<?php

namespace App\DTOs\Auth;

use App\Models\User;

readonly class VerifyWhatsAppOtpDto
{
    public function __construct(
        public User $user,
        public string $otp,
    ) {}
}
