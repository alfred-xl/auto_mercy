<?php

namespace Database\Factories;

use App\Enums\FuelType;
use App\Enums\MileageUnit;
use App\Enums\TransmissionType;
use App\Enums\VehicleCondition;
use App\Models\BodyType;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\CarStand;
use App\Models\Make;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Car>
 */
class CarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'make_id' => Make::factory(),
            'car_model_id' => fn (array $attributes) => CarModel::factory()->create([
                'make_id' => $attributes['make_id'],
            ]),
            'body_type_id' => BodyType::factory(),
            'car_stand_id' => CarStand::factory(),
            'trim' => fake()->randomElement(['LE', 'SE', 'Sport', 'Limited']),
            'year' => fake()->numberBetween(2012, 2025),
            'price_amount' => fake()->numberBetween(8_000_000, 65_000_000),
            'currency' => 'NGN',
            'mileage' => fake()->numberBetween(10_000, 180_000),
            'mileage_unit' => MileageUnit::Kilometres,
            'condition' => VehicleCondition::ForeignUsed,
            'transmission' => TransmissionType::Automatic,
            'fuel_type' => FuelType::Petrol,
            'exterior_colour' => fake()->safeColorName(),
            'interior_colour' => fake()->safeColorName(),
            'description' => fake()->paragraphs(2, true),
            'is_featured' => false,
        ];
    }
}
