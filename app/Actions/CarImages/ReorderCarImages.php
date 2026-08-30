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
            $allImages = CarImage::query()
                ->withTrashed()
                ->whereBelongsTo($car)
                ->lockForUpdate()
                ->get();
            $activeIds = $allImages->whereNull('deleted_at')->pluck('id')->map(fn (int $id): int => $id)->all();

            if (count($activeIds) !== count($orderedImageIds)
                || array_diff($activeIds, $orderedImageIds) !== []
                || array_diff($orderedImageIds, $activeIds) !== []) {
                throw new DomainException('The image order must contain every active image exactly once.');
            }

            $offset = ((int) $allImages->max('display_order')) + $allImages->count() + 1;

            CarImage::query()
                ->withTrashed()
                ->whereBelongsTo($car)
                ->update(['display_order' => DB::raw("display_order + {$offset}")]);

            foreach ($orderedImageIds as $order => $imageId) {
                CarImage::query()->whereKey($imageId)->update(['display_order' => $order]);
            }

            $deletedOrder = count($orderedImageIds);

            foreach ($allImages->whereNotNull('deleted_at') as $deletedImage) {
                CarImage::query()->withTrashed()->whereKey($deletedImage->id)->update(['display_order' => $deletedOrder++]);
            }
        });
    }
}
