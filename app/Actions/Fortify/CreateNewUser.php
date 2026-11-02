<?php

namespace App\Actions\Fortify;

use App\Actions\Auth\SendWhatsAppVerificationAction;
use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\DTOs\Auth\SendWhatsAppOtpDto;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'phone' => ['required', 'string', 'max:20'],
            'password' => $this->passwordRules(),
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'password' => $input['password'],
            'role' => 'petugas_penerima',
            'email_verified_at' => null,
        ]);

        app(SendWhatsAppVerificationAction::class)->handle(new SendWhatsAppOtpDto($user));

        return $user;
    }
}
