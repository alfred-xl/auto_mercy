<?php

namespace App\Actions\Cars;

use App\Enums\CarStatus;
use App\Models\Car;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class DuplicateCarAsDraft
{
    /** @var list<string> */
    private const ATTRIBUTES = [
        'listing_category', 'make', 'model', 'trim', 'year', 'body_type',
        'price_amount', 'previous_price_amount', 'mileage', 'mileage_unit',
        'transmission', 'fuel_type', 'drivetrain', 'engine', 'exterior_colour',
        'interior_colour', 'description',
    ];

    public function execute(Car $source, User $actor): Car
    {
        Gate::forUser($actor)->authorize('create', Car::class);

        return DB::transaction(function () use ($source): Car {
            $duplicate = new Car;
            $duplicate->forceFill([
                ...$source->only(self::ATTRIBUTES),
                'status' => CarStatus::Draft,
                'is_featured' => false,
            ])->save();
            $duplicate->features()->sync($source->features()->pluck('features.id'));

            return $duplicate->refresh();
        });
    }
}
