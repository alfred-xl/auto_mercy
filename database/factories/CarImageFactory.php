<?php

namespace Database\Factories;

use App\Enums\ImageProcessingStatus;
use App\Models\Car;
use App\Models\CarImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CarImage> */
class CarImageFactory extends Factory
{
    public function definition(): array
    {
        $filename = fake()->uuid().'.webp';

        return [
            'car_id' => Car::factory(),
            'path' => 'cars/'.$filename,
            'derivatives' => null,
            'alt_text' => fake()->sentence(5),
            'sort_order' => 0,
            'processing_status' => ImageProcessingStatus::Ready,
            'processing_error' => null,
        ];
    }
}
