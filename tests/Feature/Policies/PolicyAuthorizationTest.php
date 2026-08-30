<?php

use App\Models\Car;
use App\Models\CarStand;
use App\Models\Feature;
use App\Models\Make;
use App\Models\User;

it('allows super administrators to manage protected domains', function () {
    $user = User::factory()->superAdmin()->create();
    $car = Car::factory()->create();

    expect($user->can('delete', $car))->toBeTrue()
        ->and($user->can('create', User::class))->toBeTrue()
        ->and($user->can('update', Make::factory()->create()))->toBeTrue();
});

it('gives inventory managers scoped operational permissions', function () {
    $user = User::factory()->inventoryManager()->create();
    $car = Car::factory()->create();
    $feature = Feature::factory()->create();
    $stand = CarStand::factory()->create();

    expect($user->can('update', $car))->toBeTrue()
        ->and($user->can('publish', $car))->toBeTrue()
        ->and($user->can('update', $feature))->toBeTrue()
        ->and($user->can('view', $stand))->toBeTrue()
        ->and($user->can('delete', $car))->toBeFalse()
        ->and($user->can('create', User::class))->toBeFalse()
        ->and($user->can('update', Make::factory()->create()))->toBeFalse();
});

it('denies inactive administrators', function () {
    $user = User::factory()->inventoryManager()->inactive()->create();

    expect($user->can('create', Car::class))->toBeFalse();
});
