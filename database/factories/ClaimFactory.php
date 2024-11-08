<?php

namespace Database\Factories;

use App\Models\Claim;
use App\Models\Payer;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClaimFactory extends Factory
{
    protected $model = Claim::class;

    public function definition(): array
    {
        return [
            'reference' => fake()->unique()->bothify('CLM-####-????'),
            'payer_id' => Payer::factory(),
            'authorization_notes' => fake()->paragraph(),
            'internal_notes' => fake()->paragraph(),
        ];
    }
}