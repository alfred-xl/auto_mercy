<?php

namespace Database\Factories;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Lead> */
class LeadFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_name' => fake()->name(),
            'phone' => fake()->numerify('080########'),
            'email' => fake()->optional()->safeEmail(),
            'message' => fake()->optional()->sentence(),
            'source' => fake()->randomElement(LeadSource::cases()),
            'status' => LeadStatus::New,
            'follow_up_at' => fake()->optional()->dateTimeBetween('now', '+2 weeks'),
            'inspection_at' => fake()->optional()->dateTimeBetween('now', '+2 weeks'),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
