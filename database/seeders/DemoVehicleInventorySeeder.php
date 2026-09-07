<?php

namespace Database\Seeders;

use App\Actions\Cars\TransitionCarStatus;
use App\Actions\Reservations\CloseReservation;
use App\Actions\Reservations\CreateReservation;
use App\Enums\CarStatus;
use App\Enums\ImageProcessingStatus;
use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Enums\ListingCategory;
use App\Enums\MileageUnit;
use App\Enums\ReservationStatus;
use App\Models\Car;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class DemoVehicleInventorySeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            throw new RuntimeException('Demo inventory must not be seeded in production.');
        }

        User::query()->create([
            'name' => config('automercy.admin.name') ?: 'Auto Mercy Admin',
            'email' => config('automercy.admin.email') ?: 'admin@auto-mercy.test',
            'password' => Hash::make(config('automercy.admin.password') ?: 'ChangeMe!12345'),
        ]);

        $cars = collect($this->vehicles())->map(function (array $vehicle): Car {
            $targetStatus = $vehicle['status'];
            $assetSlug = $vehicle['asset_slug'];
            unset($vehicle['status'], $vehicle['asset_slug']);

            $car = Car::query()->create($vehicle);
            $this->seedImage($car, $assetSlug);

            if ($targetStatus !== CarStatus::Draft) {
                app(TransitionCarStatus::class)->execute($car, CarStatus::Available);
            }

            return $car->refresh()->setAttribute('seed_target_status', $targetStatus);
        });

        $generalLead = Lead::query()->create([
            'customer_name' => 'Chinedu Okafor',
            'phone' => '08031234567',
            'email' => 'chinedu@example.test',
            'message' => 'I need help choosing a reliable family SUV.',
            'source' => LeadSource::Website,
            'status' => LeadStatus::New,
            'follow_up_at' => now()->addDay(),
        ]);

        foreach ($cars->take(5) as $index => $car) {
            Lead::query()->create([
                'car_id' => $car->getKey(),
                'customer_name' => ['Ada Nwosu', 'Tunde Bello', 'Ifeoma Obi', 'Kunle Adebayo', 'Amaka Eze'][$index],
                'phone' => '0806000000'.($index + 1),
                'email' => "buyer{$index}@example.test",
                'message' => 'Please confirm availability and inspection options.',
                'source' => [LeadSource::WhatsApp, LeadSource::Phone, LeadSource::WalkIn, LeadSource::SocialMedia, LeadSource::Referral][$index],
                'status' => [LeadStatus::Contacted, LeadStatus::InspectionScheduled, LeadStatus::Negotiating, LeadStatus::Won, LeadStatus::New][$index],
                'follow_up_at' => now()->addDays($index + 1),
                'inspection_at' => $index === 1 ? now()->addDays(2) : null,
                'notes' => 'Seeded customer follow-up record.',
            ]);
        }

        foreach ($cars as $car) {
            $targetStatus = $car->getAttribute('seed_target_status');

            if ($targetStatus === CarStatus::Reserved) {
                $lead = Lead::query()->where('car_id', $car->getKey())->first() ?? $generalLead;
                app(CreateReservation::class)->execute($car, $lead, (int) config('automercy.reservation.amount'), 'Deposit confirmed by staff.');
            }

            if ($targetStatus === CarStatus::Sold) {
                $lead = Lead::query()->where('car_id', $car->getKey())->first() ?? $generalLead;
                $reservation = app(CreateReservation::class)->execute($car, $lead, (int) config('automercy.reservation.amount'), 'Completed seed sale.');
                app(CloseReservation::class)->execute($reservation, ReservationStatus::Completed);
            }
        }
    }

    private function seedImage(Car $car, string $assetSlug): void
    {
        $source = database_path("seeders/assets/cars/{$assetSlug}/primary.webp");

        if (! is_file($source)) {
            throw new RuntimeException("Missing local seed image: {$source}");
        }

        $contents = file_get_contents($source);

        if ($contents === false) {
            throw new RuntimeException("Unable to read local seed image: {$source}");
        }

        $directory = 'cars/'.strtolower($car->stock_number);
        $originalPath = "{$directory}/seed.webp";
        Storage::disk((string) config('automercy.media.disk'))->put($originalPath, $contents);
        $dimensions = getimagesize($source);
        $width = $dimensions[0] ?? 1200;
        $height = $dimensions[1] ?? 800;
        $derivatives = [];

        foreach (array_keys((array) config('automercy.media.variants')) as $variant) {
            $path = "{$directory}/derivatives/seed-{$variant}.webp";
            Storage::disk((string) config('automercy.media.disk'))->put($path, $contents);
            $derivatives[$variant]['webp'] = compact('path', 'width', 'height') + ['file_size_bytes' => strlen($contents)];
        }

        $car->images()->create([
            'path' => $originalPath,
            'derivatives' => $derivatives,
            'alt_text' => $car->display_name,
            'sort_order' => 0,
            'processing_status' => ImageProcessingStatus::Ready,
        ]);
    }

    /** @return list<array<string, mixed>> */
    private function vehicles(): array
    {
        return [
            $this->vehicle(ListingCategory::ForeignUsed, 'Toyota', 'Camry', 'XLE', 2018, 'Sedan', 30_500_000, 78_500, CarStatus::Available, '2018-toyota-camry-xle'),
            $this->vehicle(ListingCategory::BrandNew, 'Toyota', 'Corolla', 'LE', 2025, 'Sedan', 42_000_000, 20, CarStatus::Available, '2017-toyota-corolla-le'),
            $this->vehicle(ListingCategory::PreOrder, 'Toyota', 'RAV4', 'XLE', 2022, 'SUV', 45_000_000, 31_000, CarStatus::Available, '2020-toyota-rav4-xle'),
            $this->vehicle(ListingCategory::ForeignUsed, 'Lexus', 'RX 350', 'Premium', 2018, 'SUV', 48_000_000, 70_300, CarStatus::Available, '2018-lexus-rx-350-premium'),
            $this->vehicle(ListingCategory::BrandNew, 'Lexus', 'ES 350', 'Luxury', 2025, 'Sedan', 82_000_000, 15, CarStatus::Reserved, '2017-lexus-es-350-luxury'),
            $this->vehicle(ListingCategory::PreOrder, 'Mercedes-Benz', 'C300', '4MATIC', 2021, 'Sedan', 55_500_000, 39_000, CarStatus::Available, '2016-mercedes-benz-c300-4matic'),
            $this->vehicle(ListingCategory::ForeignUsed, 'Honda', 'Accord', 'Sport', 2019, 'Sedan', 30_000_000, 63_400, CarStatus::Reserved, '2019-honda-accord-sport'),
            $this->vehicle(ListingCategory::BrandNew, 'Honda', 'CR-V', 'EX-L', 2025, 'SUV', 68_000_000, 12, CarStatus::Available, '2018-honda-cr-v-ex-l'),
            $this->vehicle(ListingCategory::PreOrder, 'Toyota', 'Highlander', 'XLE', 2020, 'SUV', 58_000_000, 55_200, CarStatus::Available, '2019-toyota-highlander-xle'),
            $this->vehicle(ListingCategory::ForeignUsed, 'Ford', 'Explorer', 'XLT', 2017, 'SUV', 34_000_000, 96_500, CarStatus::Sold, '2017-ford-explorer-xlt'),
            $this->vehicle(ListingCategory::BrandNew, 'Hyundai', 'Sonata', 'SEL', 2025, 'Sedan', 46_500_000, 8, CarStatus::Draft, '2020-hyundai-sonata-sel'),
            $this->vehicle(ListingCategory::PreOrder, 'Nissan', 'Rogue', 'SV', 2022, 'SUV', 36_500_000, 28_600, CarStatus::Available, '2018-nissan-rogue-sv'),
        ];
    }

    /** @return array<string, mixed> */
    private function vehicle(ListingCategory $category, string $make, string $model, string $trim, int $year, string $bodyType, int $price, int $mileage, CarStatus $status, string $assetSlug): array
    {
        return [
            'listing_category' => $category,
            'make' => $make,
            'model' => $model,
            'trim' => $trim,
            'year' => $year,
            'body_type' => $bodyType,
            'price_amount' => $price,
            'previous_price_amount' => $status === CarStatus::Available ? $price + 2_000_000 : null,
            'mileage' => $mileage,
            'mileage_unit' => MileageUnit::Kilometres,
            'transmission' => 'automatic',
            'fuel_type' => 'petrol',
            'drivetrain' => 'fwd',
            'engine' => '2.5L 4-cylinder',
            'exterior_colour' => 'Black',
            'interior_colour' => 'Black leather',
            'features' => ['Air conditioning', 'Bluetooth', 'Reverse camera', 'Keyless entry'],
            'description' => "A carefully selected {$year} {$make} {$model} {$trim}, inspected and prepared for a straightforward Auto Mercy buying experience.",
            'is_featured' => $status === CarStatus::Available,
            'status' => $status,
            'asset_slug' => $assetSlug,
        ];
    }
}
