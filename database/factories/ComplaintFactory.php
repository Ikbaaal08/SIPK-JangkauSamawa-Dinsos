<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Complaint;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Complaint>
 */
class ComplaintFactory extends Factory
{
    protected $model = Complaint::class;

    public function definition(): array
    {
        return [
            'ticket_code' => 'TKT-'.strtoupper(fake()->unique()->bothify('######')),
            'reporter_name' => fake()->name(),
            'reporter_phone' => '081234567890',
            'reporter_address' => fake()->address(),
            'category_id' => Category::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'village' => fake()->citySuffix(),
            'district' => fake()->city(),
            'status' => 'baru',
            'reported_at' => now(),
        ];
    }
}
