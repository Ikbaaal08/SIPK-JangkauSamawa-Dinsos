<?php

namespace App\Actions\Feature;

use App\Models\User;
use App\Services\FeatureService;
use Illuminate\Validation\ValidationException;

class ToggleFeatureAction
{
    public function __construct(
        protected FeatureService $featureService,
    ) {}

    /**
     * Toggle the status of a feature flag.
     *
     * @throws ValidationException
     */
    public function handle(string $key, User $user): bool
    {
        if (! $user->isSuperAdmin()) {
            throw ValidationException::withMessages([
                'feature' => 'Hanya Super Administrator yang berhak mengubah konfigurasi fitur sistem.',
            ]);
        }

        return $this->featureService->toggle($key, $user->id);
    }
}
