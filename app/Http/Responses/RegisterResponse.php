<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Symfony\Component\HttpFoundation\Response;

class RegisterResponse implements RegisterResponseContract
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
            return redirect()->route('verification.whatsapp');
        }

        $target = $user ? $user->dashboardRoute() : route('dashboard');

        return redirect()->intended($target);
    }
}
