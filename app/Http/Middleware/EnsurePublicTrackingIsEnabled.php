<?php

namespace App\Http\Middleware;

use App\Services\FeatureService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePublicTrackingIsEnabled
{
    public function __construct(
        protected FeatureService $featureService
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->featureService->isEnabled('public_tracking')) {
            return redirect()->route('login')
                ->with('status', 'Fitur pelacakan aduan sedang dinonaktifkan.');
        }

        return $next($request);
    }
}
