<?php

use App\Actions\Cars\DuplicateCarAsDraft;
use App\Enums\CarStatus;
use App\Models\Car;
use App\Models\CarImage;
use App\Models\Feature;
use App\Models\User;

it('duplicates editable details and features into a clean draft', function () {
    $actor = User::factory()->inventoryManager()->create();
    $feature = Feature::factory()->create();
    $source = Car::factory()->create(['is_featured' => true, 'meta_title' => 'Source SEO']);
    $source->features()->attach($feature);
    CarImage::factory()->for($source)->create();

    $duplicate = app(DuplicateCarAsDraft::class)->execute($source, $actor);

    expect($duplicate->status)->toBe(CarStatus::Draft)
        ->and($duplicate->is_featured)->toBeFalse()
        ->and($duplicate->meta_title)->toBeNull()
        ->and($duplicate->stock_number)->not->toBe($source->stock_number)
        ->and($duplicate->images()->count())->toBe(0)
        ->and($duplicate->features()->pluck('features.id')->all())->toBe([$feature->id])
        ->and($duplicate->created_by)->toBe($actor->id);
});
