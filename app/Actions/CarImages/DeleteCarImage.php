<?php

namespace App\Actions\CarImages;

use App\Enums\CarStatus;
use App\Models\Car;
use App\Models\CarImage;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class DeleteCarImage
{
    public function __construct(private readonly ReorderCarImages $reorderCarImages) {}

    public function execute(Car $car, CarImage $image, User $actor): void
    {
        Gate::forUser($actor)->authorize('update', $car);
        Gate::forUser($actor)->authorize('delete', $image);

        $paths = DB::transaction(function () use ($car, $image): array {
            $lockedCar = Car::query()->lockForUpdate()->findOrFail($car->getKey());
            $lockedImage = CarImage::query()
                ->whereBelongsTo($lockedCar)
                ->whereKey($image->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $readyImageCount = $lockedCar->images()
                ->where('processing_status', 'ready')
                ->lockForUpdate()
                ->count();

            if (in_array($lockedCar->status, [CarStatus::Available, CarStatus::Reserved], true)
                && $lockedImage->processing_status->value === 'ready'
                && $readyImageCount === 1) {
                throw new DomainException('An active vehicle must retain at least one ready image.');
            }

            $paths = $lockedImage->storedPaths();
            $lockedImage->delete();
            $orderedIds = $lockedCar->images()->pluck('id')->map(fn (int $id): int => $id)->all();
            $this->reorderCarImages->execute($lockedCar, $orderedIds);

            return $paths;
        });

        Storage::disk((string) config('automercy.media.disk'))->delete($paths);
    }
}
