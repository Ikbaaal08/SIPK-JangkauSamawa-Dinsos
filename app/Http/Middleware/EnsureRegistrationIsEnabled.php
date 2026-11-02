<?php

namespace App\Http\Middleware;

use App\Services\FeatureService;
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
            $isRegistrationEnabled = app(FeatureService::class)->isEnabled('registration');

            if (! $isRegistrationEnabled) {
                return redirect()->route('login')
                    ->with('status', 'Pendaftaran mandiri sedang dinonaktifkan. Silakan hubungi administrator.');
            }
        }

        return $next($request);
    }
}
