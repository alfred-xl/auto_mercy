<?php

namespace Database\Seeders;

use App\Actions\CarImages\DeleteCarImage;
use App\Actions\CarImages\ReorderCarImages;
use App\Actions\CarImages\SetPrimaryCarImage;
use App\Actions\CarImages\UploadCarImages;
use App\Actions\Cars\TransitionCarStatus;
use App\Enums\CarStatus;
use App\Enums\FuelType;
use App\Enums\MileageUnit;
use App\Enums\TransmissionType;
use App\Enums\UserRole;
use App\Enums\VehicleCondition;
use App\Models\BodyType;
use App\Models\Car;
use App\Models\CarImage;
use App\Models\CarModel;
use App\Models\CarStand;
use App\Models\Feature;
use App\Models\Make;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use LogicException;
use RuntimeException;
use Throwable;

class DemoVehicleInventorySeeder extends Seeder
{
    private const ACTOR_EMAIL = 'demo-inventory@auto-mercy.invalid';

    public function __construct(
        private readonly UploadCarImages $uploadCarImages,
        private readonly SetPrimaryCarImage $setPrimaryCarImage,
        private readonly DeleteCarImage $deleteCarImage,
        private readonly ReorderCarImages $reorderCarImages,
        private readonly TransitionCarStatus $transitionCarStatus,
    ) {}

    public function run(): void
    {
        if (app()->isProduction()) {
            throw new LogicException('Demo vehicle inventory cannot be seeded in production.');
        }

        $this->call(CarStandSeeder::class);

        $actor = $this->demoActor();
        $bodyTypes = $this->bodyTypes();
        $features = $this->features();
        $stands = CarStand::query()->whereIn('slug', ['iju', 'ogunnisi-road'])->get()->keyBy('slug');

        foreach ($this->vehicles() as $index => $vehicle) {
            try {
                $make = $this->make($vehicle['make']);
                $model = $this->carModel($make, $vehicle['model']);
                $publishedAt = CarbonImmutable::parse('2026-08-20 10:00:00')->subDays($index);
                $car = $this->upsertCar(
                    $vehicle,
                    $make,
                    $model,
                    $bodyTypes[$vehicle['body_type']],
                    $stands->get($vehicle['stand']),
                    $actor,
                    $publishedAt,
                );

                $this->syncImage($car, $vehicle, $actor);
                $car->features()->sync($features->only($vehicle['features'])->pluck('id')->all());
                $car = $this->syncStatus($car->refresh(), $vehicle['status'], $actor);
                $this->setDeterministicDates($car, $vehicle['status'], $publishedAt);
            } catch (Throwable $exception) {
                throw new RuntimeException(
                    "Failed to seed demo vehicle {$vehicle['stock_number']}: {$exception->getMessage()}",
                    0,
                    $exception,
                );
            }
        }
    }

    private function demoActor(): User
    {
        $actor = User::query()->where('email', self::ACTOR_EMAIL)->first();

        if ($actor !== null) {
            return $actor;
        }

        $actor = new User;
        $actor->forceFill([
            'name' => 'Demo Inventory Seeder',
            'email' => self::ACTOR_EMAIL,
            'email_verified_at' => now(),
            'password' => Hash::make(Str::random(64)),
            'role' => UserRole::InventoryManager,
            'is_active' => true,
        ])->save();

        return $actor;
    }

    /** @return array<string, BodyType> */
    private function bodyTypes(): array
    {
        return collect(['Sedan', 'SUV'])->mapWithKeys(function (string $name): array {
            $bodyType = BodyType::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'is_active' => true],
            );

            return [$name => $bodyType];
        })->all();
    }

    /** @return Collection<string, Feature> */
    private function features(): Collection
    {
        $definitions = [
            'air-conditioning' => ['Air Conditioning', 'Comfort'],
            'bluetooth' => ['Bluetooth', 'Technology'],
            'reverse-camera' => ['Reverse Camera', 'Safety'],
            'keyless-entry' => ['Keyless Entry', 'Comfort'],
            'cruise-control' => ['Cruise Control', 'Comfort'],
            'leather-seats' => ['Leather Seats', 'Comfort'],
            'alloy-wheels' => ['Alloy Wheels', 'Exterior'],
            'navigation' => ['Navigation', 'Technology'],
            'parking-sensors' => ['Parking Sensors', 'Safety'],
            'push-button-start' => ['Push-Button Start', 'Technology'],
        ];

        return collect($definitions)->mapWithKeys(function (array $definition, string $slug): array {
            $feature = Feature::query()->firstOrCreate(
                ['slug' => $slug],
                ['name' => $definition[0], 'category' => $definition[1], 'is_active' => true],
            );

            return [$slug => $feature];
        });
    }

    private function make(string $name): Make
    {
        $normalizedName = Str::of($name)->lower()->remove(['-', ' '])->toString();
        $make = Make::query()
            ->whereRaw("REPLACE(REPLACE(LOWER(name), '-', ''), ' ', '') = ?", [$normalizedName])
            ->first();

        return $make ?? Make::query()->create([
            'name' => $name,
            'slug' => Str::slug($name),
            'is_active' => true,
        ]);
    }

    private function carModel(Make $make, string $name): CarModel
    {
        $normalizedName = Str::of($name)->lower()->remove(['-', ' '])->toString();
        $model = CarModel::query()
            ->whereBelongsTo($make)
            ->whereRaw("REPLACE(REPLACE(LOWER(name), '-', ''), ' ', '') = ?", [$normalizedName])
            ->first();

        return $model ?? CarModel::query()->create([
            'make_id' => $make->getKey(),
            'name' => $name,
            'slug' => Str::slug($name),
            'is_active' => true,
        ]);
    }

    /** @param array<string, mixed> $vehicle */
    private function upsertCar(
        array $vehicle,
        Make $make,
        CarModel $model,
        BodyType $bodyType,
        ?CarStand $stand,
        User $actor,
        CarbonImmutable $publishedAt,
    ): Car {
        if ($stand === null) {
            throw new RuntimeException("The {$vehicle['stand']} demo car stand is missing.");
        }

        $attributes = [
            'make_id' => $make->getKey(),
            'car_model_id' => $model->getKey(),
            'body_type_id' => $bodyType->getKey(),
            'car_stand_id' => $stand->getKey(),
            'trim' => $vehicle['trim'],
            'year' => $vehicle['year'],
            'price_amount' => $vehicle['price'],
            'currency' => 'NGN',
            'mileage' => $vehicle['mileage'],
            'mileage_unit' => MileageUnit::Kilometres,
            'condition' => VehicleCondition::ForeignUsed,
            'transmission' => TransmissionType::Automatic,
            'fuel_type' => FuelType::Petrol,
            'engine' => $vehicle['engine'],
            'exterior_colour' => $vehicle['colour'],
            'description' => $this->description($vehicle),
            'supplemental_specs' => ['demo_record' => true],
            'is_featured' => false,
            'meta_title' => "{$vehicle['year']} {$vehicle['make']} {$vehicle['model']} {$vehicle['trim']}",
            'meta_description' => "Development preview for a {$vehicle['year']} {$vehicle['make']} {$vehicle['model']} {$vehicle['trim']} foreign-used car.",
        ];

        $car = Car::query()->withTrashed()->where('stock_number', $vehicle['stock_number'])->first();

        if ($car === null) {
            $car = new Car;
            $car->fill($attributes);
            $car->forceFill(['created_by' => $actor->getKey(), 'updated_by' => $actor->getKey()])->save();
            $car->forceFill([
                'stock_number' => $vehicle['stock_number'],
                'slug' => Str::slug("{$vehicle['year']} {$vehicle['make']} {$vehicle['model']} {$vehicle['trim']} {$vehicle['stock_number']}"),
            ])->saveQuietly();
        } else {
            if ($car->trashed()) {
                $car->restore();
            }

            $car->fill($attributes);
            $car->forceFill(['updated_by' => $actor->getKey()])->save();
        }

        $car->refresh();
        $this->setDeterministicDates($car, $car->status, $publishedAt);

        return $car->refresh();
    }

    /** @param array<string, mixed> $vehicle */
    private function syncImage(Car $car, array $vehicle, User $actor): void
    {
        $assetPath = database_path("seeders/assets/cars/{$vehicle['asset_slug']}/primary.webp");

        if (! is_file($assetPath)) {
            throw new RuntimeException("The primary image asset does not exist at {$assetPath}.");
        }

        $seedImages = $car->images()->where('original_filename', 'primary.webp')->get();
        $primaryImage = $seedImages->first(fn (CarImage $image): bool => Storage::disk($image->disk)->exists($image->path));

        foreach ($seedImages as $seedImage) {
            if ($primaryImage?->is($seedImage)) {
                continue;
            }

            $this->deleteCarImage->execute($car, $seedImage, $actor);
        }

        if ($primaryImage === null) {
            $primaryImage = $this->uploadCarImages->execute($car, [
                new UploadedFile($assetPath, 'primary.webp', 'image/webp', null, true),
            ], $actor)->firstOrFail();
        }

        $primaryImage->update([
            'alt_text' => "{$vehicle['year']} {$vehicle['colour']} {$vehicle['make']} {$vehicle['model']} {$vehicle['trim']} demo vehicle",
        ]);
        $this->setPrimaryCarImage->execute($car, $primaryImage, $actor);

        $orderedIds = $car->images()->get()
            ->sortByDesc(fn (CarImage $image): bool => $image->is($primaryImage))
            ->pluck('id')
            ->map(fn (int $id): int => $id)
            ->values()
            ->all();
        $this->reorderCarImages->execute($car, $orderedIds);
    }

    private function syncStatus(Car $car, CarStatus $target, User $actor): Car
    {
        if ($car->status === $target) {
            return $car;
        }

        if ($car->status === CarStatus::Reserved && in_array($target, [CarStatus::Available, CarStatus::Sold], true)) {
            return $this->transitionCarStatus->execute($car, $target, null, $actor);
        }

        if ($car->status === CarStatus::Available && in_array($target, [CarStatus::Reserved, CarStatus::Sold], true)) {
            return $this->transitionCarStatus->execute($car, $target, now()->addDays(14), $actor);
        }

        if ($car->status !== CarStatus::Draft) {
            if ($car->status !== CarStatus::Archived) {
                $car = $this->transitionCarStatus->execute($car, CarStatus::Archived, null, $actor);
            }

            $car = $this->transitionCarStatus->execute($car, CarStatus::Draft, null, $actor);
        }

        $car = $this->transitionCarStatus->execute($car, CarStatus::Available, null, $actor);

        return $target === CarStatus::Available
            ? $car
            : $this->transitionCarStatus->execute($car, $target, now()->addDays(14), $actor);
    }

    private function setDeterministicDates(Car $car, CarStatus $status, CarbonImmutable $publishedAt): void
    {
        $timestamps = $car->timestamps;
        $car->timestamps = false;
        $car->forceFill([
            'published_at' => $publishedAt,
            'reserved_at' => $status === CarStatus::Reserved ? $publishedAt->addDay() : null,
            'sold_at' => $status === CarStatus::Sold ? $publishedAt->addDay() : null,
            'created_at' => $publishedAt->subDays(2),
            'updated_at' => $publishedAt,
        ])->saveQuietly();
        $car->timestamps = $timestamps;
    }

    /** @param array<string, mixed> $vehicle */
    private function description(array $vehicle): string
    {
        return "This foreign-used {$vehicle['year']} {$vehicle['make']} {$vehicle['model']} {$vehicle['trim']} is presented in {$vehicle['colour']} with an automatic transmission, petrol fuel type, {$vehicle['engine']} engine specification, and ".number_format($vehicle['mileage'])." km recorded for this preview.\n\nDemo vehicle record created for development and interface preview purposes.";
    }

    /** @return list<array<string, mixed>> */
    private function vehicles(): array
    {
        $standardFeatures = ['air-conditioning', 'bluetooth', 'reverse-camera', 'keyless-entry', 'cruise-control', 'alloy-wheels'];
        $premiumFeatures = [...$standardFeatures, 'leather-seats', 'navigation', 'parking-sensors', 'push-button-start'];

        return [
            $this->vehicle('DEMO-CAMRY-2018-01', 2018, 'Toyota', 'Camry', 'XLE', 'Sedan', 'Black', 78_500, '2.5L', 30_500_000, CarStatus::Available, 'iju', '2018-toyota-camry-xle', $premiumFeatures),
            $this->vehicle('DEMO-COROLLA-2017-01', 2017, 'Toyota', 'Corolla', 'LE', 'Sedan', 'White', 91_200, '1.8L', 18_500_000, CarStatus::Available, 'ogunnisi-road', '2017-toyota-corolla-le', $standardFeatures),
            $this->vehicle('DEMO-RAV4-2020-01', 2020, 'Toyota', 'RAV4', 'XLE', 'SUV', 'Silver', 54_600, '2.5L', 38_000_000, CarStatus::Available, 'iju', '2020-toyota-rav4-xle', $premiumFeatures),
            $this->vehicle('DEMO-RX350-2018-01', 2018, 'Lexus', 'RX 350', 'Premium', 'SUV', 'Pearl White', 70_300, '3.5L', 48_000_000, CarStatus::Available, 'ogunnisi-road', '2018-lexus-rx-350-premium', $premiumFeatures),
            $this->vehicle('DEMO-ES350-2017-01', 2017, 'Lexus', 'ES 350', 'Luxury', 'Sedan', 'Black', 82_100, '3.5L', 32_000_000, CarStatus::Available, 'iju', '2017-lexus-es-350-luxury', $premiumFeatures),
            $this->vehicle('DEMO-C300-2016-01', 2016, 'Mercedes-Benz', 'C300', '4MATIC', 'Sedan', 'Grey', 88_700, '2.0L', 31_500_000, CarStatus::Available, 'ogunnisi-road', '2016-mercedes-benz-c300-4matic', $premiumFeatures),
            $this->vehicle('DEMO-ACCORD-2019-01', 2019, 'Honda', 'Accord', 'Sport', 'Sedan', 'Red', 63_400, '1.5L Turbo', 30_000_000, CarStatus::Available, 'iju', '2019-honda-accord-sport', $standardFeatures),
            $this->vehicle('DEMO-CRV-2018-01', 2018, 'Honda', 'CR-V', 'EX-L', 'SUV', 'Blue', 75_900, '1.5L Turbo', 34_000_000, CarStatus::Available, 'ogunnisi-road', '2018-honda-cr-v-ex-l', $premiumFeatures),
            $this->vehicle('DEMO-HIGHLANDER-2019-01', 2019, 'Toyota', 'Highlander', 'XLE', 'SUV', 'Black', 68_200, '3.5L', 52_000_000, CarStatus::Available, 'iju', '2019-toyota-highlander-xle', $premiumFeatures),
            $this->vehicle('DEMO-EXPLORER-2017-01', 2017, 'Ford', 'Explorer', 'XLT', 'SUV', 'White', 96_500, '3.5L', 34_000_000, CarStatus::Available, 'ogunnisi-road', '2017-ford-explorer-xlt', $standardFeatures),
            $this->vehicle('DEMO-SONATA-2020-01', 2020, 'Hyundai', 'Sonata', 'SEL', 'Sedan', 'Silver', 58_300, '2.5L', 25_500_000, CarStatus::Reserved, 'iju', '2020-hyundai-sonata-sel', $standardFeatures),
            $this->vehicle('DEMO-ROGUE-2018-01', 2018, 'Nissan', 'Rogue', 'SV', 'SUV', 'Grey', 84_600, '2.5L', 23_500_000, CarStatus::Sold, 'ogunnisi-road', '2018-nissan-rogue-sv', $standardFeatures),
        ];
    }

    /** @param list<string> $features */
    private function vehicle(string $stockNumber, int $year, string $make, string $model, string $trim, string $bodyType, string $colour, int $mileage, string $engine, int $price, CarStatus $status, string $stand, string $assetSlug, array $features): array
    {
        return compact('stockNumber', 'year', 'make', 'model', 'trim', 'bodyType', 'colour', 'mileage', 'engine', 'price', 'status', 'stand', 'assetSlug', 'features') + [
            'stock_number' => $stockNumber,
            'body_type' => $bodyType,
            'asset_slug' => $assetSlug,
        ];
    }
}
