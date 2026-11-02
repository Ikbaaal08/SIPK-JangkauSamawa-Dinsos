<?php

namespace Database\Factories;

use App\Models\FeatureFlag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FeatureFlag>
 */
class FeatureFlagFactory extends Factory
{
    protected $model = FeatureFlag::class;

    public function definition(): array
    {
        return [
            'key' => fake()->unique()->slug(2),
            'label' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'is_enabled' => false,
        ];
    }

    public function enabled(): static
    {
        return $this->state(fn () => [
            'is_enabled' => true,
        ]);
    }
}
