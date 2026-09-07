<?php

namespace App\Actions\Cars;

use App\Models\Car;
use App\Models\CarImage;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteCar
{
    public function execute(Car $car): void
    {
        $storedPaths = DB::transaction(function () use ($car): array {
            $lockedCar = Car::query()->lockForUpdate()->findOrFail($car->getKey());

            if ($lockedCar->reservations()->exists()) {
                throw new DomainException('Vehicles with reservation history cannot be deleted. Archive this vehicle instead.');
            }

            $storedPaths = $lockedCar->images()
                ->lockForUpdate()
                ->get()
                ->flatMap(fn (CarImage $image): array => $image->storedPaths())
                ->filter()
                ->unique()
                ->values()
                ->all();

            $lockedCar->delete();

            return $storedPaths;
        });

        if ($storedPaths !== []) {
            Storage::disk((string) config('automercy.media.disk'))->delete($storedPaths);
        }
    }
}
