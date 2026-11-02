<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRegistrationIsEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('register') || $request->is('register/*') || $request->routeIs('register*')) {
            if (! config('auth.registration_enabled', true)) {
                return redirect()->route('login')
                    ->with('status', 'Pendaftaran mandiri sedang dinonaktifkan. Silakan hubungi administrator.');
            }
        }

        return $next($request);
    }
}
