<?php

namespace Database\Seeders;

use App\Models\CarStand;
use Illuminate\Database\Seeder;

class CarStandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $openingHours = collect(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'])
            ->mapWithKeys(fn (string $day): array => [$day => ['opens' => '08:00', 'closes' => '18:00']])
            ->put('sunday', null)
            ->all();

        $stands = [
            [
                'name' => 'Iju Road',
                'slug' => 'iju',
                'address' => '9 Moshalashi Alao Street, Oyemukun Bus Stop, Iju Road, Lagos',
                'sort_order' => 1,
            ],
            [
                'name' => 'Bamboo Plaza',
                'slug' => 'ogunnisi-road',
                'address' => '6/8 Ogunnisi Road, Bamboo Plaza, adjacent Omole Phase 1',
                'sort_order' => 2,
            ],
        ];

        foreach ($stands as $stand) {
            CarStand::query()->updateOrCreate(
                ['slug' => $stand['slug']],
                [
                    ...$stand,
                    'city' => 'Lagos',
                    'state' => 'Lagos',
                    'phone' => '08061731673',
                    'whatsapp' => '08061731673',
                    'opening_hours' => $openingHours,
                    'map_url' => null,
                    'latitude' => null,
                    'longitude' => null,
                    'is_active' => true,
                ],
            );
        }
    }
}
