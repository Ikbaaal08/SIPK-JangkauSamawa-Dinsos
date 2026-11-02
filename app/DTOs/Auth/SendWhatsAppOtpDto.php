<?php

namespace App\DTOs\Auth;

use App\Models\User;

readonly class SendWhatsAppOtpDto
{
    public function __construct(
        public User $user,
    ) {}
}
