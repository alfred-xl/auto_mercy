<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    /** @var list<string> */
    private const FEATURES = [
        'Clean Interior & Exterior',
        'Automatic Transmission',
        'Powerful & Efficient Engine',
        'Smooth Driving Experience',
        'Cold/Chilling AC',
        'Leather Seats',
        'Touchscreen Infotainment',
        'Bluetooth Connectivity',
        'Reverse Camera',
        'Parking Sensors',
        'Alloy Wheels',
        'Keyless Entry',
        'Push Start',
        'Cruise Control',
        'Steering Wheel Controls',
        'Power Windows',
        'Power Mirrors',
        'Central Locking',
        'Airbags',
        'ABS Braking System',
        'Stability & Traction Control',
        'Spacious Cabin',
        'Large Boot/Trunk Space',
        'Excellent Road Comfort',
        'Premium Sound System',
        'USB Charging Ports',
        'Navigation/GPS (where available)',
    ];

    public function run(): void
    {
        foreach (self::FEATURES as $name) {
            Feature::query()->firstOrCreate(
                ['normalized_name' => Feature::normalizeName($name)],
                ['name' => $name],
            );
        }
    }
}
