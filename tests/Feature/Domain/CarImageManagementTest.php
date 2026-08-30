<?php

use App\Actions\CarImages\DeleteCarImage;
use App\Actions\CarImages\ReorderCarImages;
use App\Actions\CarImages\SetPrimaryCarImage;
use App\Actions\CarImages\UploadCarImages;
use App\Models\Car;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

it('uploads safe images and manages primary and ordering', function () {
    Storage::fake('public');
    config()->set('automercy.media.disk', 'public');
    $actor = User::factory()->inventoryManager()->create();
    $car = Car::factory()->create();

    $images = app(UploadCarImages::class)->execute($car, [
        UploadedFile::fake()->image('front.jpg', 1200, 800),
        UploadedFile::fake()->image('rear.png', 1200, 800),
    ], $actor);

    expect($images)->toHaveCount(2)
        ->and($images[0]->display_order)->toBe(0)
        ->and($images[1]->display_order)->toBe(1);
    Storage::disk('public')->assertExists($images[0]->path);

    app(SetPrimaryCarImage::class)->execute($car, $images[1], $actor);
    expect($car->refresh()->primary_image_id)->toBe($images[1]->id);

    app(ReorderCarImages::class)->execute($car, [$images[1]->id, $images[0]->id]);
    expect($car->images()->pluck('id')->all())->toBe([$images[1]->id, $images[0]->id]);

    $replacement = app(DeleteCarImage::class)->execute($car, $images[1], $actor);
    expect($replacement?->id)->toBe($images[0]->id)
        ->and($car->refresh()->primary_image_id)->toBe($images[0]->id);
    Storage::disk('public')->assertMissing($images[1]->path);
});

it('rejects disguised non-image uploads', function () {
    Storage::fake('public');
    config()->set('automercy.media.disk', 'public');

    app(UploadCarImages::class)->execute(
        Car::factory()->create(),
        [UploadedFile::fake()->createWithContent('vehicle.jpg', '<svg><script>alert(1)</script></svg>')],
        User::factory()->inventoryManager()->create(),
    );
})->throws(ValidationException::class);
