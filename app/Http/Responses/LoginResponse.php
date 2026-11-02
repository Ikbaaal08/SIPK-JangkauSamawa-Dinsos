<?php

namespace App\Http\Responses;

use App\Actions\Auth\SendWhatsAppVerificationAction;
use App\DTOs\Auth\SendWhatsAppOtpDto;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  Request  $request
     */
    public function toResponse($request): Response
    {
        $user = $request->user();

        if ($user && ! $user->email_verified_at) {
            app(SendWhatsAppVerificationAction::class)->handle(new SendWhatsAppOtpDto($user));

            return redirect()->route('verification.whatsapp');
        }

        $target = $user ? $user->dashboardRoute() : route('dashboard');

        return redirect()->intended($target);
    }
}
