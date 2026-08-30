<?php

namespace App\Actions\CarImages;

use App\Models\Car;
use App\Models\CarImage;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

class SetPrimaryCarImage
{
    public function execute(Car $car, CarImage $image, User $actor): Car
    {
        return DB::transaction(function () use ($car, $image, $actor): Car {
            $lockedCar = Car::query()->lockForUpdate()->findOrFail($car->getKey());
            $eligibleImage = CarImage::query()
                ->whereBelongsTo($lockedCar)
                ->whereKey($image->getKey())
                ->lockForUpdate()
                ->first();

            if ($eligibleImage === null || $eligibleImage->processing_status !== 'ready') {
                throw new DomainException('Only a ready image belonging to this car can be selected as primary.');
            }

            $lockedCar->forceFill([
                'primary_image_id' => $eligibleImage->getKey(),
                'updated_by' => $actor->getKey(),
            ])->save();

            return $lockedCar->refresh();
        });
    }
}
