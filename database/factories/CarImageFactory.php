<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\CarImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CarImage>
 */
class CarImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $filename = fake()->uuid().'.jpg';

        return [
            'car_id' => Car::factory(),
            'disk' => 'public',
            'path' => 'cars/'.$filename,
            'original_filename' => $filename,
            'mime_type' => 'image/jpeg',
            'width' => 1600,
            'height' => 1200,
            'file_size_bytes' => fake()->numberBetween(100_000, 3_000_000),
            'alt_text' => fake()->sentence(5),
            'display_order' => 0,
            'processing_status' => 'ready',
        ];
    }
}
