<?php

namespace Database\Seeders;

use App\Models\Claim;
use App\Models\ClaimStatus;
use App\Models\Payer;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
       // create 10 payers, each with 3-5 claims, each claim with 1-3 status
       Payer::factory()
       ->count(10)
       ->has(
           Claim::factory()
               ->count(fake()->numberBetween(3, 5))
               ->has(
                   ClaimStatus::factory()
                       ->count(fake()->numberBetween(1, 3))
               )
       )
       ->create();
    }
}
