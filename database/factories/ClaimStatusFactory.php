<?php

namespace Database\Factories;

use App\Models\Claim;
use App\Models\ClaimStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClaimStatusFactory extends Factory
{
    protected $model = ClaimStatus::class;

    public function definition(): array
    {
        return [
            'claim_id' => Claim::factory(),
            'status' => fake()->randomElement(['pending', 'approved', 'completed']),
            'date' => fake()->dateTimeThisYear(),
        ];
    }
}