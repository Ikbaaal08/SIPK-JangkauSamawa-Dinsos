<?php

namespace Database\Factories;

use App\Models\Complaint;
use App\Models\ComplaintHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ComplaintHistory>
 */
class ComplaintHistoryFactory extends Factory
{
    protected $model = ComplaintHistory::class;

    public function definition(): array
    {
        return [
            'complaint_id' => Complaint::factory(),
            'user_id' => User::factory(),
            'previous_status' => 'baru',
            'new_status' => 'diproses',
            'note' => fake()->sentence(),
        ];
    }
}
