<?php

use App\Actions\Cars\DuplicateCarAsDraft;
use App\Actions\Cars\TransitionCarStatus;
use App\Enums\CarStatus;
use App\Models\Car;
use App\Models\Feature;
use App\Models\User;
use Database\Seeders\FeatureSeeder;
use Illuminate\Database\QueryException;

it('normalizes feature names and rejects duplicates', function () {
    Feature::factory()->create(['name' => '  Bluetooth   Connectivity  ']);

    expect(fn () => Feature::factory()->create(['name' => 'bluetooth connectivity']))
        ->toThrow(QueryException::class);

    $this->assertDatabaseCount('features', 1);
    $this->assertDatabaseHas('features', [
        'name' => 'Bluetooth Connectivity',
        'normalized_name' => 'bluetooth connectivity',
    ]);
});

it('seeds the requested reusable feature catalogue once', function () {
    $this->seed(FeatureSeeder::class);
    $this->seed(FeatureSeeder::class);

    $this->assertDatabaseCount('features', 27);
    $this->assertDatabaseHas('features', ['name' => 'Clean Interior & Exterior']);
    $this->assertDatabaseHas('features', ['name' => 'Navigation/GPS (where available)']);
});

it('shows only selected features on the public vehicle page', function () {
    $car = createPublishableCar();
    $selectedFeature = Feature::factory()->create(['name' => 'Reverse Camera']);
    Feature::factory()->create(['name' => 'Premium Sound System']);
    $car->features()->attach($selectedFeature);
    app(TransitionCarStatus::class)->execute($car, CarStatus::Available);

    $response = $this->get(route('cars.show', $car));

    $response->assertSee('Reverse Camera')
        ->assertDontSee('Premium Sound System');
});

it('copies selected features when a vehicle is duplicated', function () {
    $source = Car::factory()->create();
    $features = Feature::factory()->count(2)->create();
    $source->features()->sync($features);

    $duplicate = app(DuplicateCarAsDraft::class)->execute($source, User::factory()->create());

    expect($duplicate->status)->toBe(CarStatus::Draft)
        ->and($duplicate->features()->pluck('features.id')->sort()->values()->all())
        ->toBe($features->modelKeys());
});

it('prevents deleting features that are assigned to a vehicle', function () {
    $user = User::factory()->create();
    $feature = Feature::factory()->create();

    expect($user->can('delete', $feature))->toBeTrue();

    $feature->cars()->attach(Car::factory()->create());

    expect($user->can('delete', $feature))->toBeFalse();
});

it('renders feature management and the searchable vehicle selector for admins', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('filament.admin.resources.features.index'))
        ->assertSee('Vehicle Features');

    $this->actingAs($user)
        ->get(route('filament.admin.resources.vehicles.create'))
        ->assertSee('Select existing features or create a new reusable feature.');
});
