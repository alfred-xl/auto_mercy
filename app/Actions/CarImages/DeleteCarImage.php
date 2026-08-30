<?php

namespace App\Actions\CarImages;

use App\Models\Car;
use App\Models\CarImage;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class DeleteCarImage
{
    public function __construct(
        private readonly ReorderCarImages $reorderCarImages,
    ) {}

    public function execute(Car $car, CarImage $image, User $actor): ?CarImage
    {
        [$deletedImage, $replacement] = DB::transaction(function () use ($car, $image, $actor): array {
            $lockedCar = Car::query()->lockForUpdate()->findOrFail($car->getKey());
            $lockedImage = CarImage::query()
                ->whereBelongsTo($lockedCar)
                ->whereKey($image->getKey())
                ->lockForUpdate()
                ->firstOrFail();
            $replacement = null;

            if ($lockedCar->primary_image_id === $lockedImage->getKey()) {
                $replacement = CarImage::query()
                    ->whereBelongsTo($lockedCar)
                    ->whereKeyNot($lockedImage->getKey())
                    ->orderBy('display_order')
                    ->lockForUpdate()
                    ->first();

                $lockedCar->forceFill([
                    'primary_image_id' => $replacement?->getKey(),
                    'updated_by' => $actor->getKey(),
                ])->save();
            }

            $lockedImage->delete();

            return [$lockedImage, $replacement];
        });

        $this->reorderCarImages->execute(
            $car,
            $car->images()->pluck('id')->map(fn (int $id): int => $id)->all(),
        );

        $isReferencedElsewhere = CarImage::query()
            ->withTrashed()
            ->where('disk', $deletedImage->disk)
            ->where('path', $deletedImage->path)
            ->whereKeyNot($deletedImage->getKey())
            ->exists();

        if (! $isReferencedElsewhere && Storage::disk($deletedImage->disk)->exists($deletedImage->path)) {
            if (! Storage::disk($deletedImage->disk)->delete($deletedImage->path)) {
                throw new RuntimeException('The image record was removed, but its stored file could not be deleted.');
            }
        }

        return $replacement;
    }
}
