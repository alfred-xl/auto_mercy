<?php

use App\Models\Car;

it('generates stock numbers from committed ids and readable unique slugs', function () {
    $first = Car::factory()->create(['year' => 2021, 'trim' => 'Limited']);
    $second = Car::factory()->create();

    expect($first->stock_number)->toBe('AM-'.str_pad((string) $first->id, 4, '0', STR_PAD_LEFT))
        ->and($first->slug)->toStartWith('2021-')
        ->and($first->slug)->toContain('limited')
        ->and($first->slug)->toEndWith(strtolower($first->stock_number))
        ->and($second->stock_number)->not->toBe($first->stock_number)
        ->and($second->slug)->not->toBe($first->slug);
});

it('keeps stock numbers and slugs stable after ordinary edits', function () {
    $car = Car::factory()->create();
    $identity = [$car->stock_number, $car->slug];

    $car->update(['trim' => 'Changed Trim', 'year' => 2024]);

    expect([$car->stock_number, $car->slug])->toBe($identity);
});
