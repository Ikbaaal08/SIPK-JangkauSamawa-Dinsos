<?php

namespace Database\Factories;

use App\Models\Complaint;
use App\Models\Disposition;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Disposition>
 */
class DispositionFactory extends Factory
{
    protected $model = Disposition::class;

    public function definition(): array
    {
        return [
            'complaint_id' => Complaint::factory(),
            'assigned_by' => User::factory()->kepalaDinas(),
            'assigned_to' => User::factory()->petugasPenerima(),
            'instruction' => fake()->sentence(8),
            'due_date' => now()->addDays(5)->toDateString(),
            'status' => 'aktif',
        ];
    }

    public function selesai(): static
    {
        return $this->state(fn () => [
            'status' => 'selesai',
        ]);
    }
}
