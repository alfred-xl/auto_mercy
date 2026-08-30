<?php

namespace App\Actions\Cars;

use App\Models\Car;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class DuplicateCarAsDraft
{
    /** @var list<string> */
    private const COPIED_ATTRIBUTES = [
        'make_id',
        'car_model_id',
        'body_type_id',
        'car_stand_id',
        'trim',
        'year',
        'price_amount',
        'currency',
        'mileage',
        'mileage_unit',
        'condition',
        'transmission',
        'fuel_type',
        'drivetrain',
        'engine',
        'exterior_colour',
        'interior_colour',
        'description',
        'supplemental_specs',
        'video_url',
    ];

    public function execute(Car $source, User $actor): Car
    {
        Gate::forUser($actor)->authorize('view', $source);
        Gate::forUser($actor)->authorize('update', $source);
        Gate::forUser($actor)->authorize('create', Car::class);

        return DB::transaction(function () use ($source, $actor): Car {
            $lockedSource = Car::query()->lockForUpdate()->findOrFail($source->getKey());
            $duplicate = new Car;
            $duplicate->forceFill([
                ...$lockedSource->only(self::COPIED_ATTRIBUTES),
                'is_featured' => false,
                'created_by' => $actor->getKey(),
                'updated_by' => $actor->getKey(),
            ])->save();

            $duplicate->features()->sync($lockedSource->features()->pluck('features.id'));

            return $duplicate->refresh();
        });
    }
}
