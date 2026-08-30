<?php

namespace App\Actions\CarImages;

use App\Models\Car;
use App\Models\CarImage;
use App\Models\User;
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
        Validator::make(
            ['images' => $files],
            [
                'images' => ['required', 'array', 'min:1'],
                'images.*' => [
                    'required',
                    'file',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'extensions:jpg,jpeg,png,webp',
                    'max:'.config('automercy.media.image_max_kilobytes'),
                ],
            ],
        )->validate();

        $validatedImages = $this->inspectImages($files);
        $disk = (string) config('automercy.media.disk');
        $directory = 'cars/'.Str::lower((string) $car->stock_number);
        $storedPaths = [];
        $storedSizes = [];

        try {
            foreach ($validatedImages as $index => $metadata) {
                $filename = Str::uuid()->toString().'.'.$metadata['extension'];
                $path = $directory.'/'.$filename;
                $contents = $this->reencode($files[$index], $metadata['mime_type']);

                if (! Storage::disk($disk)->put($path, $contents)) {
                    throw new RuntimeException('The image could not be stored.');
                }

                $storedPaths[$index] = $path;
                $storedSizes[$index] = strlen($contents);
            }

            return DB::transaction(function () use ($car, $files, $actor, $disk, $storedPaths, $storedSizes, $validatedImages): Collection {
                $maximumOrder = $car->images()->withTrashed()->max('display_order');
                $nextOrder = $maximumOrder === null ? 0 : ((int) $maximumOrder) + 1;
                $images = collect();

                foreach ($storedPaths as $index => $path) {
                    $image = new CarImage;
                    $image->forceFill([
                        'car_id' => $car->getKey(),
                        'disk' => $disk,
                        'path' => $path,
                        'original_filename' => Str::limit(basename($files[$index]->getClientOriginalName()), 255, ''),
                        'mime_type' => $validatedImages[$index]['mime_type'],
                        'width' => $validatedImages[$index]['width'],
                        'height' => $validatedImages[$index]['height'],
                        'file_size_bytes' => $storedSizes[$index],
                        'display_order' => $nextOrder++,
                        'processing_status' => 'ready',
                        'created_by' => $actor->getKey(),
                    ])->save();

                    $images->push($image);
                }

                return $images;
            });
        } catch (Throwable $exception) {
            foreach ($storedPaths as $path) {
                Storage::disk($disk)->delete($path);
            }

            throw $exception;
        }
    }

    private function reencode(UploadedFile $file, string $mimeType): string
    {
        $image = match ($mimeType) {
            'image/jpeg' => imagecreatefromjpeg($file->getRealPath()),
            'image/png' => imagecreatefrompng($file->getRealPath()),
            'image/webp' => imagecreatefromwebp($file->getRealPath()),
            default => false,
        };

        if ($image === false) {
            throw ValidationException::withMessages(['images' => 'The image could not be decoded safely.']);
        }

        imagealphablending($image, false);
        imagesavealpha($image, true);
        ob_start();
        $encoded = match ($mimeType) {
            'image/jpeg' => imagejpeg($image, null, 88),
            'image/png' => imagepng($image, null, 6),
            'image/webp' => imagewebp($image, null, 85),
        };
        $contents = ob_get_clean();
        imagedestroy($image);

        if (! $encoded || ! is_string($contents)) {
            throw new RuntimeException('The image could not be safely re-encoded.');
        }

        return $contents;
    }

    /**
     * @param  list<UploadedFile>  $files
     * @return array<int, array{extension: string, mime_type: string, width: int, height: int}>
     */
    private function inspectImages(array $files): array
    {
        $mimeExtensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];
        $metadata = [];

        foreach ($files as $index => $file) {
            $dimensions = getimagesize($file->getRealPath());
            $mimeType = $file->getMimeType();

            if ($dimensions === false || ! isset($mimeExtensions[$mimeType])) {
                throw ValidationException::withMessages(["images.{$index}" => 'The file must be a valid JPEG, PNG, or WebP image.']);
            }

            [$width, $height] = $dimensions;

            if (($width * $height) > (int) config('automercy.media.image_max_pixels')) {
                throw ValidationException::withMessages(["images.{$index}" => 'The image must not exceed 40 megapixels.']);
            }

            $metadata[$index] = [
                'extension' => $mimeExtensions[$mimeType],
                'mime_type' => $mimeType,
                'width' => $width,
                'height' => $height,
            ];
        }

        return $metadata;
    }
}
