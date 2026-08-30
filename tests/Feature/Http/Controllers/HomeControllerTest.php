<?php

use App\Actions\Cars\TransitionCarStatus;
use App\Enums\CarStatus;
use App\Models\Car;

function makeAvailableHomepageCar(array $attributes = []): Car
{
    return app(TransitionCarStatus::class)->execute(createPublishableCar($attributes), CarStatus::Available);
}

it('renders the complete public homepage with business facts and metadata', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('<title>Foreign-Used Cars in Lagos | Auto Mercy</title>', false)
        ->assertSee('Drive home a car you can trust.')
        ->assertSee('CAC Registered')
        ->assertSee('7328497')
        ->assertSee('Since 2023')
        ->assertSee('Nationwide vehicle delivery')
        ->assertSee('"@type":"Organization"', false)
        ->assertSee('"@type":"AutoDealer"', false);
});

it('renders exact contact links, locations, hours, and reservation terms', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('href="tel:+2348061731673"', false)
        ->assertSee('https://wa.me/2348061731673', false)
        ->assertSee('08061731673')
        ->assertSee('automercyofgod19@gmail.com')
        ->assertSee('9 Moshalashi Alao Street, Oyemukun Bus Stop, Iju Road, Lagos')
        ->assertSee('6/8 Ogunnisi Road, Bamboo Plaza, adjacent Omole Phase 1')
        ->assertSee('Monday–Saturday, 8:00 AM–6:00 PM')
        ->assertSee('₦500,000')
        ->assertSee('14 days')
        ->assertSee('non-refundable');
});

it('submits the homepage vehicle search to the inventory contract', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('action="'.route('cars.index').'"', false)
        ->assertSee('method="GET"', false)
        ->assertSee('name="make"', false)
        ->assertSee('name="model"', false)
        ->assertSee('name="price_max"', false)
        ->assertSee('name="car_stand"', false);
});

it('lists only currently published available cars', function () {
    makeAvailableHomepageCar(['trim' => 'Homepage Available']);
    createPublishableCar(['trim' => 'Homepage Draft']);

    $sold = makeAvailableHomepageCar(['trim' => 'Homepage Sold']);
    app(TransitionCarStatus::class)->execute($sold, CarStatus::Sold);

    $future = makeAvailableHomepageCar(['trim' => 'Homepage Future']);
    $future->forceFill(['published_at' => now()->addDay()])->saveQuietly();

    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('Homepage Available')
        ->assertDontSee('Homepage Draft')
        ->assertDontSee('Homepage Sold')
        ->assertDontSee('Homepage Future');
});

it('lists the three newest eligible cars in deterministic order', function () {
    $oldest = makeAvailableHomepageCar(['trim' => 'Oldest Homepage Car']);
    $third = makeAvailableHomepageCar(['trim' => 'Third Newest Homepage Car']);
    $second = makeAvailableHomepageCar(['trim' => 'Second Newest Homepage Car']);
    $newest = makeAvailableHomepageCar(['trim' => 'Newest Homepage Car']);

    $oldest->forceFill(['published_at' => now()->subDays(4)])->saveQuietly();
    $third->forceFill(['published_at' => now()->subDays(3)])->saveQuietly();
    $second->forceFill(['published_at' => now()->subDays(2)])->saveQuietly();
    $newest->forceFill(['published_at' => now()->subDay()])->saveQuietly();

    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSeeInOrder([
            'Newest Homepage Car',
            'Second Newest Homepage Car',
            'Third Newest Homepage Car',
        ])
        ->assertDontSee('Oldest Homepage Car')
        ->assertSee('data-latest-cars-carousel', false)
        ->assertSee('data-latest-cars-previous', false)
        ->assertSee('data-latest-cars-next', false);

    expect(substr_count($response->getContent(), 'data-vehicle-card'))->toBe(3);
});

it('renders an honest empty inventory state', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('New arrivals are being prepared')
        ->assertSee('online inventory is being updated')
        ->assertDontSee('data-latest-cars-carousel', false)
        ->assertDontSee('data-latest-cars-previous', false);
});

it('does not render unsupported business language', function () {
    $response = $this->get(route('home'));

    expect(strtolower($response->getContent()))->not->toContain('show'.'room');
});
