<?php

namespace Database\Factories;

use App\Enums\CarStatus;
use App\Enums\FuelType;
use App\Enums\ListingCategory;
use App\Enums\MileageUnit;
use App\Enums\TransmissionType;
use App\Models\Car;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Car> */
class CarFactory extends Factory
{
    public function definition(): array
    {
        return [
            'listing_category' => fake()->randomElement(ListingCategory::cases()),
            'make' => fake()->randomElement(['Toyota', 'Lexus', 'Honda', 'Mercedes-Benz', 'Ford']),
            'model' => fake()->randomElement(['Camry', 'Corolla', 'RX 350', 'Accord', 'C300']),
            'trim' => fake()->optional()->randomElement(['LE', 'XLE', 'Sport', 'Limited']),
            'year' => fake()->numberBetween(2014, now()->year + 1),
            'body_type' => fake()->randomElement(['Sedan', 'SUV']),
            'price_amount' => fake()->numberBetween(8_000_000, 80_000_000),
            'mileage' => fake()->numberBetween(0, 180_000),
            'mileage_unit' => MileageUnit::Kilometres,
            'transmission' => TransmissionType::Automatic,
            'fuel_type' => FuelType::Petrol,
            'description' => fake()->paragraphs(2, true),
            'status' => CarStatus::Draft,
            'is_featured' => false,
        ];
    }
}
