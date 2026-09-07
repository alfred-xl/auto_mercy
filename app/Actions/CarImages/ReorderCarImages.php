<?php

namespace App\Actions\CarImages;

use App\Models\Car;
use App\Models\CarImage;
use DomainException;
use Illuminate\Support\Facades\DB;

class ReorderCarImages
{
    /** @param list<int> $orderedImageIds */
    public function execute(Car $car, array $orderedImageIds): void
    {
        DB::transaction(function () use ($car, $orderedImageIds): void {
            $images = CarImage::query()
                ->whereBelongsTo($car)
                ->lockForUpdate()
                ->get();
            $existingIds = $images->pluck('id')->map(fn (int $id): int => $id)->all();

            if (count($existingIds) !== count($orderedImageIds)
                || array_diff($existingIds, $orderedImageIds) !== []
                || array_diff($orderedImageIds, $existingIds) !== []) {
                throw new DomainException('The image order must contain every image exactly once.');
            }

            $offset = $images->count() + 1;

            CarImage::query()
                ->whereBelongsTo($car)
                ->update(['sort_order' => DB::raw("sort_order + {$offset}")]);

            foreach ($orderedImageIds as $sortOrder => $imageId) {
                CarImage::query()->whereKey($imageId)->update(['sort_order' => $sortOrder]);
            }
        });
    }
}
