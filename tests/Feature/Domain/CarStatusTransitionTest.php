<?php

use App\Actions\Cars\TransitionCarStatus;
use App\Enums\CarStatus;
use App\Models\Car;

it('defines the approved lifecycle graph', function (CarStatus $from, array $targets) {
    expect(array_map(fn (CarStatus $status): string => $status->value, $from->allowedTransitions()))
        ->toBe($targets);
})->with([
    'draft' => [CarStatus::Draft, ['available', 'archived']],
    'available' => [CarStatus::Available, ['draft', 'reserved', 'sold', 'archived']],
    'reserved' => [CarStatus::Reserved, ['available', 'sold', 'archived']],
    'sold' => [CarStatus::Sold, ['archived']],
    'archived' => [CarStatus::Archived, ['draft']],
]);

it('publishes a complete draft and preserves publication time on ordinary edits', function () {
    $car = createPublishableCar();
    $published = app(TransitionCarStatus::class)->execute($car, CarStatus::Available);
    $publishedAt = $published->published_at;

    $published->update(['description' => 'Updated description without republishing.']);

    expect($published->refresh()->status)->toBe(CarStatus::Available)
        ->and($published->published_at->equalTo($publishedAt))->toBeTrue();
});

it('handles reservation release and sale lifecycle timestamps', function () {
    $car = app(TransitionCarStatus::class)->execute(createPublishableCar(), CarStatus::Available);
    $reserved = app(TransitionCarStatus::class)->execute($car, CarStatus::Reserved, now()->addDay());

    expect($reserved->reserved_at)->not->toBeNull()
        ->and($reserved->reservation_expires_at)->not->toBeNull();

    $available = app(TransitionCarStatus::class)->execute($reserved, CarStatus::Available);
    $sold = app(TransitionCarStatus::class)->execute($available, CarStatus::Sold);

    expect($available->reserved_at)->toBeNull()
        ->and($available->reservation_expires_at)->toBeNull()
        ->and($sold->sold_at)->not->toBeNull();
});

it('archives an available car with the archive timestamp', function () {
    $available = app(TransitionCarStatus::class)->execute(createPublishableCar(), CarStatus::Available);
    $archived = app(TransitionCarStatus::class)->execute($available, CarStatus::Archived);

    expect($archived->status)->toBe(CarStatus::Archived)
        ->and($archived->archived_at)->not->toBeNull();
});

it('rejects incomplete, invalid, and direct status changes', function () {
    $incomplete = Car::factory()->create(['description' => null]);

    expect(fn () => app(TransitionCarStatus::class)->execute($incomplete, CarStatus::Available))
        ->toThrow(DomainException::class, 'cannot be published')
        ->and(fn () => app(TransitionCarStatus::class)->execute($incomplete, CarStatus::Sold))
        ->toThrow(DomainException::class, 'cannot transition');

    $incomplete->forceFill(['status' => CarStatus::Available])->save();
})->throws(LogicException::class, 'must use the status transition action');
