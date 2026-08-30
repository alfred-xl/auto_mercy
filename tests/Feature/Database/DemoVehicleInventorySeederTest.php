<?php

use App\Actions\Cars\EnsureCarReadyForPublication;
use App\Enums\CarStatus;
use App\Models\Car;
use App\Models\CarStand;
use App\Models\Make;
use Database\Seeders\CarStandSeeder;
use Database\Seeders\DemoVehicleInventorySeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

it('creates the complete publication-ready demo inventory', function () {
    Storage::fake('public');
    config()->set('automercy.media.disk', 'public');

    $this->seed(DemoVehicleInventorySeeder::class);

    $demoCars = Car::query()
        ->where('stock_number', 'like', 'DEMO-%')
        ->with(['make', 'carModel', 'carStand', 'primaryImage', 'features'])
        ->get();

    expect($demoCars)->toHaveCount(12)
        ->and($demoCars->where('status', CarStatus::Available))->toHaveCount(10)
        ->and($demoCars->where('status', CarStatus::Reserved))->toHaveCount(1)
        ->and($demoCars->where('status', CarStatus::Sold))->toHaveCount(1)
        ->and($demoCars->every(fn (Car $car): bool => $car->primaryImage !== null))->toBeTrue()
        ->and($demoCars->every(fn (Car $car): bool => $car->features->isNotEmpty()))->toBeTrue()
        ->and($demoCars->every(fn (Car $car): bool => str_contains($car->description, 'development and interface preview purposes')))->toBeTrue();

    foreach ($demoCars as $car) {
        Storage::disk('public')->assertExists($car->primaryImage->path);
    }

    $readiness = app(EnsureCarReadyForPublication::class);
    expect($demoCars->where('status', CarStatus::Available)->every(
        fn (Car $car): bool => $readiness->isReady($car),
    ))->toBeTrue();
});

it('remains idempotent and reuses canonical references', function () {
    Storage::fake('public');
    config()->set('automercy.media.disk', 'public');
    Make::factory()->create(['name' => 'Mercedes Benz', 'slug' => 'mercedes-benz']);
    $this->seed(CarStandSeeder::class);
    $standIds = CarStand::query()->whereIn('slug', ['iju', 'ogunnisi-road'])->pluck('id', 'slug');

    $this->seed(DemoVehicleInventorySeeder::class);
    $this->seed(DemoVehicleInventorySeeder::class);

    $demoCars = Car::query()->where('stock_number', 'like', 'DEMO-%')->get();
    $demoCarIds = $demoCars->pluck('id');

    expect($demoCars)->toHaveCount(12)
        ->and($demoCars->pluck('stock_number')->unique())->toHaveCount(12)
        ->and(Make::query()->whereRaw("REPLACE(REPLACE(LOWER(name), '-', ''), ' ', '') = 'mercedesbenz'")->count())->toBe(1)
        ->and(DB::table('car_models')->whereIn('make_id', Make::query()->whereIn('slug', ['toyota', 'lexus', 'mercedes-benz', 'honda', 'ford', 'hyundai', 'nissan'])->select('id'))->count())->toBe(12)
        ->and(DB::table('car_feature')->whereIn('car_id', $demoCarIds)->count())->toBe(100)
        ->and(CarStand::query()->whereIn('slug', ['iju', 'ogunnisi-road'])->pluck('id', 'slug')->all())->toBe($standIds->all())
        ->and($demoCars->where('car_stand_id', $standIds['iju'])->count())->toBe(6)
        ->and($demoCars->where('car_stand_id', $standIds['ogunnisi-road'])->count())->toBe(6);
});

it('does not modify real inventory', function () {
    Storage::fake('public');
    config()->set('automercy.media.disk', 'public');
    $realCar = Car::factory()->create(['trim' => 'Customer Inventory']);
    $protectedAttributes = ['stock_number', 'slug', 'trim', 'status', 'price_amount', 'updated_at'];
    $originalAttributes = collect($realCar->refresh()->getRawOriginal())->only($protectedAttributes)->all();

    $this->seed(DemoVehicleInventorySeeder::class);

    expect(collect($realCar->refresh()->getRawOriginal())->only($protectedAttributes)->all())->toBe($originalAttributes)
        ->and(Car::query()->where('stock_number', 'like', 'DEMO-%')->count())->toBe(12);
});

it('populates public pages and preserves status visibility rules', function () {
    Storage::fake('public');
    config()->set('automercy.media.disk', 'public');
    $this->seed(DemoVehicleInventorySeeder::class);

    $camry = Car::query()->where('stock_number', 'DEMO-CAMRY-2018-01')->firstOrFail();

    $this->get(route('home'))->assertSee('2018 Toyota Camry')->assertDontSee('2020 Hyundai Sonata')->assertDontSee('2018 Nissan Rogue');
    $this->get(route('cars.index'))->assertSee('2018 Toyota Camry')->assertDontSee('2020 Hyundai Sonata')->assertDontSee('2018 Nissan Rogue');
    $this->get(route('cars.index', ['make' => 'toyota']))->assertSee('2018 Toyota Camry')->assertDontSee('2018 Lexus RX 350');
    $this->get(route('cars.index', ['car_stand' => 'iju']))->assertSee('2018 Toyota Camry')->assertDontSee('2017 Toyota Corolla');
    $this->get(route('cars.show', $camry))->assertSee('DEMO-CAMRY-2018-01')->assertSee('Demo vehicle record');
});

it('refuses to run in production', function () {
    Storage::fake('public');
    $this->app['env'] = 'production';

    app(DemoVehicleInventorySeeder::class)->run();
})->throws(LogicException::class, 'Demo vehicle inventory cannot be seeded in production.');
