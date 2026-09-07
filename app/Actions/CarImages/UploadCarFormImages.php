<?php

namespace App\Actions\CarImages;

use App\Models\Car;
use App\Models\CarImage;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class UploadCarFormImages
{
    public function __construct(
        private readonly UploadCarImages $uploadCarImages,
        private readonly ReorderCarImages $reorderCarImages,
    ) {}

    /**
     * @param  array<string|int, mixed>  $files
     * @param  array<string|int, array<string, mixed>>  $details
     * @return Collection<int, CarImage>
     */
    public function execute(Car $car, array $files, array $details, string|int|null $coverKey, User $actor): Collection
    {
        Gate::forUser($actor)->authorize('update', $car);
        Gate::forUser($actor)->authorize('create', CarImage::class);

        $detailsByUploadKey = collect($details)->keyBy(
            fn (array $item): string => (string) ($item['upload_key'] ?? ''),
        );
        $items = collect($files)
            ->filter(fn (mixed $file): bool => $file instanceof UploadedFile)
            ->map(function (UploadedFile $file, string|int $key) use ($detailsByUploadKey): array {
                return [
                    'key' => (string) $key,
                    'file' => $file,
                    'alt_text' => $detailsByUploadKey->get((string) $key, [])['alt_text'] ?? null,
                ];
            })
            ->values();

        if ($items->isEmpty()) {
            return collect();
        }

        $images = $this->uploadCarImages->execute($car, $items->pluck('file')->all(), $actor);

        foreach ($images as $index => $image) {
            $altText = trim((string) ($items[$index]['alt_text'] ?? ''));

            if ($altText !== '') {
                $image->update(['alt_text' => $altText]);
            }
        }

        $coverIndex = $items->search(fn (array $item): bool => $item['key'] === (string) $coverKey);

        if ($coverIndex !== false && $images->has($coverIndex)) {
            $coverId = $images->get($coverIndex)->getKey();
            $orderedIds = $car->images()->pluck('id')->map(fn (int $id): int => $id)->all();
            $orderedIds = [
                $coverId,
                ...array_values(array_filter($orderedIds, fn (int $id): bool => $id !== $coverId)),
            ];
            $this->reorderCarImages->execute($car, $orderedIds);
        }

        return $images;
    }
}
