<?php

namespace App\Actions\CarImages;

use App\Models\Car;
use App\Models\CarImage;
use App\Models\User;
use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class UploadCarImages
{
    /**
     * @param  list<UploadedFile>  $files
     * @return Collection<int, CarImage>
     */
    public function execute(Car $car, array $files, User $actor): Collection
    {
        Validator::make(['images' => $files], [
            'images' => ['required', 'array', 'min:1'],
            'images.*' => [
                'required', 'file', 'image', 'mimes:jpg,jpeg,png,webp',
                'extensions:jpg,jpeg,png,webp',
                'max:'.config('automercy.media.image_max_kilobytes'),
            ],
        ])->validate();

        $metadata = $this->inspectImages($files);
        $disk = (string) config('automercy.media.disk');
        $directory = 'cars/'.Str::lower((string) $car->stock_number);
        $processed = [];
        $storedPaths = [];

        try {
            foreach ($files as $index => $file) {
                $identifier = Str::uuid()->toString();
                $image = $this->decodeAndOrient($file, $metadata[$index]['mime_type']);
                $image = $this->resizeWithin(
                    $image,
                    (int) config('automercy.media.original_max_width', 2400),
                    (int) config('automercy.media.original_max_height', 1800),
                );

                $originalPath = "{$directory}/{$identifier}.{$metadata[$index]['extension']}";
                $originalContents = $this->encode($image, $metadata[$index]['mime_type']);
                $this->store($disk, $originalPath, $originalContents, $storedPaths);
                $derivatives = [];

                foreach ((array) config('automercy.media.variants') as $variant => $targetWidth) {
                    $variantImage = $this->resizeToWidth($image, (int) $targetWidth);
                    $width = imagesx($variantImage);
                    $height = imagesy($variantImage);
                    $webpPath = "{$directory}/derivatives/{$identifier}-{$variant}.webp";
                    $webpContents = $this->encode($variantImage, 'image/webp');
                    $this->store($disk, $webpPath, $webpContents, $storedPaths);
                    $derivatives[$variant]['webp'] = ['path' => $webpPath, 'width' => $width, 'height' => $height, 'file_size_bytes' => strlen($webpContents)];

                    if (function_exists('imageavif')) {
                        $avifPath = "{$directory}/derivatives/{$identifier}-{$variant}.avif";
                        $avifContents = $this->encode($variantImage, 'image/avif');
                        $this->store($disk, $avifPath, $avifContents, $storedPaths);
                        $derivatives[$variant]['avif'] = ['path' => $avifPath, 'width' => $width, 'height' => $height, 'file_size_bytes' => strlen($avifContents)];
                    }

                    if ($variantImage !== $image) {
                        imagedestroy($variantImage);
                    }
                }

                $processed[$index] = [
                    'path' => $originalPath,
                    'mime_type' => $metadata[$index]['mime_type'],
                    'width' => imagesx($image),
                    'height' => imagesy($image),
                    'file_size_bytes' => strlen($originalContents),
                    'derivatives' => $derivatives,
                ];

                imagedestroy($image);
            }

            return DB::transaction(function () use ($car, $processed): Collection {
                $nextOrder = ((int) ($car->images()->max('sort_order') ?? -1)) + 1;

                return collect($processed)->map(function (array $data) use ($car, &$nextOrder): CarImage {
                    $image = new CarImage;
                    $image->forceFill([
                        'car_id' => $car->getKey(),
                        'path' => $data['path'],
                        'derivatives' => $data['derivatives'],
                        'alt_text' => $car->display_name,
                        'sort_order' => $nextOrder++,
                        'processing_status' => 'ready',
                        'processing_error' => null,
                    ])->save();

                    return $image;
                });
            });
        } catch (Throwable $exception) {
            Storage::disk($disk)->delete($storedPaths);

            throw $exception;
        }
    }

    private function decodeAndOrient(UploadedFile $file, string $mimeType): GdImage
    {
        $image = match ($mimeType) {
            'image/jpeg' => imagecreatefromjpeg($file->getRealPath()),
            'image/png' => imagecreatefrompng($file->getRealPath()),
            'image/webp' => imagecreatefromwebp($file->getRealPath()),
            default => false,
        };

        if (! $image instanceof GdImage) {
            throw ValidationException::withMessages(['images' => 'The image could not be decoded safely.']);
        }

        if ($mimeType === 'image/jpeg' && function_exists('exif_read_data')) {
            $orientation = @exif_read_data($file->getRealPath())['Orientation'] ?? null;
            $angle = match ($orientation) {
                3 => 180, 6 => -90, 8 => 90, default => 0
            };

            if ($angle !== 0) {
                $rotated = imagerotate($image, $angle, 0);

                if ($rotated instanceof GdImage) {
                    imagedestroy($image);
                    $image = $rotated;
                }
            }
        }

        imagealphablending($image, false);
        imagesavealpha($image, true);

        return $image;
    }

    private function resizeWithin(GdImage $image, int $maximumWidth, int $maximumHeight): GdImage
    {
        $ratio = min($maximumWidth / imagesx($image), $maximumHeight / imagesy($image), 1);

        return $this->resample($image, (int) round(imagesx($image) * $ratio), (int) round(imagesy($image) * $ratio));
    }

    private function resizeToWidth(GdImage $image, int $targetWidth): GdImage
    {
        $width = min($targetWidth, imagesx($image));
        $height = (int) round(imagesy($image) * ($width / imagesx($image)));

        if ($width === imagesx($image)) {
            $clone = imagecreatetruecolor($width, $height);
            imagealphablending($clone, false);
            imagesavealpha($clone, true);
            imagecopy($clone, $image, 0, 0, 0, 0, $width, $height);

            return $clone;
        }

        return $this->resample($image, $width, $height, false);
    }

    private function resample(GdImage $source, int $width, int $height, bool $destroySource = true): GdImage
    {
        if ($width === imagesx($source) && $height === imagesy($source)) {
            return $source;
        }

        $target = imagecreatetruecolor($width, $height);
        imagealphablending($target, false);
        imagesavealpha($target, true);
        imagecopyresampled($target, $source, 0, 0, 0, 0, $width, $height, imagesx($source), imagesy($source));

        if ($destroySource) {
            imagedestroy($source);
        }

        return $target;
    }

    private function encode(GdImage $image, string $mimeType): string
    {
        ob_start();
        $encoded = match ($mimeType) {
            'image/jpeg' => imagejpeg($image, null, 88),
            'image/png' => imagepng($image, null, 6),
            'image/webp' => imagewebp($image, null, (int) config('automercy.media.webp_quality', 82)),
            'image/avif' => imageavif($image, null, (int) config('automercy.media.avif_quality', 68)),
            default => false,
        };
        $contents = ob_get_clean();

        if (! $encoded || ! is_string($contents)) {
            throw new RuntimeException('The image could not be safely re-encoded.');
        }

        return $contents;
    }

    /** @param list<string> $storedPaths */
    private function store(string $disk, string $path, string $contents, array &$storedPaths): void
    {
        if (! Storage::disk($disk)->put($path, $contents)) {
            throw new RuntimeException('The image could not be stored.');
        }

        $storedPaths[] = $path;
    }

    /**
     * @param  list<UploadedFile>  $files
     * @return array<int, array{extension: string, mime_type: string}>
     */
    private function inspectImages(array $files): array
    {
        $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $metadata = [];

        foreach ($files as $index => $file) {
            $dimensions = getimagesize($file->getRealPath());
            $mimeType = $file->getMimeType();

            if ($dimensions === false || ! isset($extensions[$mimeType])) {
                throw ValidationException::withMessages(["images.{$index}" => 'The file must be a valid JPEG, PNG, or WebP image.']);
            }

            if (($dimensions[0] * $dimensions[1]) > (int) config('automercy.media.image_max_pixels')) {
                throw ValidationException::withMessages(["images.{$index}" => 'The image must not exceed 40 megapixels.']);
            }

            $metadata[$index] = ['extension' => $extensions[$mimeType], 'mime_type' => $mimeType];
        }

        return $metadata;
    }
}
