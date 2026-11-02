<?php

namespace App\Forms\Auth;

use Livewire\Attributes\Validate;
use Livewire\Form;

class VerifyOtpForm extends Form
{
    #[Validate('required', message: 'Kode OTP wajib diisi.')]
    #[Validate('digits:6', message: 'Kode OTP harus berupa 6 digit angka.')]
    public string $otp = '';
}
