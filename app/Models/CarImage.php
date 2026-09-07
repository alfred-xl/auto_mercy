<?php

namespace App\Models;

use App\Enums\ImageProcessingStatus;
use Database\Factories\CarImageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable(['path', 'derivatives', 'alt_text', 'sort_order', 'processing_status', 'processing_error'])]
class CarImage extends Model
{
    /** @use HasFactory<CarImageFactory> */
    use HasFactory;

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function getUrlAttribute(): string
    {
        return $this->variantUrl('large');
    }

    public function variantPath(string $variant, string $format = 'webp'): string
    {
        return data_get($this->derivatives, "{$variant}.{$format}.path")
            ?? data_get($this->derivatives, "{$variant}.path")
            ?? $this->path;
    }

    public function variantUrl(string $variant, string $format = 'webp'): string
    {
        return Storage::disk((string) config('automercy.media.disk'))->url($this->variantPath($variant, $format));
    }

    public function hasVariant(string $variant, string $format = 'webp'): bool
    {
        return filled(data_get($this->derivatives, "{$variant}.{$format}.path"));
    }

    public function srcset(string $format = 'webp'): string
    {
        return collect(['card', 'medium', 'large'])->map(function (string $variant) use ($format): ?string {
            $width = data_get($this->derivatives, "{$variant}.{$format}.width");

            return $width && $this->hasVariant($variant, $format)
                ? $this->variantUrl($variant, $format).' '.$width.'w'
                : null;
        })->filter()->implode(', ');
    }

    /** @return list<string> */
    public function storedPaths(): array
    {
        $derivativePaths = collect($this->derivatives ?? [])
            ->flatMap(fn (array $formats): array => collect($formats)->pluck('path')->filter()->values()->all());

        return $derivativePaths
            ->prepend($this->path)
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    protected function casts(): array
    {
        return [
            'derivatives' => 'array',
            'processing_status' => ImageProcessingStatus::class,
        ];
    }
}
