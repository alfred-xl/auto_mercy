<?php

use App\Models\CarStand;
use Database\Seeders\CarStandSeeder;

it('seeds exactly the two approved stands idempotently', function () {
    $this->seed(CarStandSeeder::class);
    $this->seed(CarStandSeeder::class);

    expect(CarStand::query()->count())->toBe(2);

    $iju = CarStand::query()->where('slug', 'iju')->firstOrFail();
    $ogunnisi = CarStand::query()->where('slug', 'ogunnisi-road')->firstOrFail();

    expect($iju->name)->toBe('Iju Road')
        ->and($iju->address)->toBe('9 Moshalashi Alao Street, Oyemukun Bus Stop, Iju Road, Lagos')
        ->and($ogunnisi->name)->toBe('Bamboo Plaza')
        ->and($ogunnisi->address)->toBe('6/8 Ogunnisi Road, Bamboo Plaza, adjacent Omole Phase 1')
        ->and($iju->phone)->toBe('08061731673')
        ->and($iju->whatsapp)->toBe('08061731673')
        ->and($iju->opening_hours['monday'])->toBe(['opens' => '08:00', 'closes' => '18:00'])
        ->and($iju->opening_hours['saturday'])->toBe(['opens' => '08:00', 'closes' => '18:00'])
        ->and($iju->opening_hours['sunday'])->toBeNull()
        ->and($iju->map_url)->toBeNull()
        ->and($iju->latitude)->toBeNull()
        ->and($iju->longitude)->toBeNull();
});
