<?php

namespace Database\Factories;

use App\Models\CarStand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CarStand>
 */
class CarStandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->company().' Car Stand';

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'address' => fake()->streetAddress(),
            'city' => 'Lagos',
            'state' => 'Lagos',
            'phone' => fake()->numerify('080########'),
            'whatsapp' => fake()->numerify('080########'),
            'opening_hours' => ['monday' => ['opens' => '08:00', 'closes' => '18:00']],
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
