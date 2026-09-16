<?php

use App\Enums\CarStatus;
use App\Enums\ListingCategory;

beforeEach(function (): void {
    config()->set('automercy.seo.base_url', 'https://www.example.test');
});

it('renders accurate available vehicle metadata and structured data', function () {
    $car = createPublicCar(attributes: [
        'listing_category' => ListingCategory::BrandNew,
        'year' => 2025,
        'make' => 'Toyota',
        'model' => 'Camry',
        'trim' => 'XLE',
        'body_type' => 'Sedan',
        'price_amount' => 24_500_000,
        'description' => '<p>Clean   verified&nbsp;description.</p>',
    ]);
    $car->images()->update(['alt_text' => '2025 Toyota Camry XLE front view']);

    $this->get(route('cars.show', $car))
        ->assertOk()
        ->assertSee('<title>2025 Toyota Camry XLE for Sale in Lagos | Auto Mercy</title>', false)
        ->assertSee('<meta property="og:type" content="product">', false)
        ->assertSee('2025 Toyota Camry XLE front view')
        ->assertSee('aria-label="Breadcrumb"', false)
        ->assertSee('"@type":["Product","Car"]', false)
        ->assertSee('"price":24500000', false)
        ->assertSee('"priceCurrency":"NGN"', false)
        ->assertSee('https://schema.org/InStock', false)
        ->assertSee('https://schema.org/NewCondition', false)
        ->assertDontSee('https://schema.org/UsedCondition', false)
        ->assertSee('Clean verified description.', false);
});

it('shows reserved status consistently on cards and structured data', function () {
    $car = createPublicCar(CarStatus::Reserved, ['make' => 'Lexus', 'model' => 'RX 350']);

    $this->get('/cars')->assertOk()->assertSee('Reserved');
    $this->get(route('cars.show', $car))
        ->assertOk()
        ->assertSee('<title>Reserved:', false)
        ->assertSee('This vehicle is reserved.')
        ->assertSee('https://schema.org/OutOfStock', false);
});

it('retains sold facts and replaces purchase wording with available alternatives', function () {
    $available = createPublicCar(attributes: ['make' => 'Toyota', 'model' => 'Corolla']);
    $sold = createPublicCar(CarStatus::Sold, ['make' => 'Toyota', 'model' => 'Camry']);

    $this->get('/cars')->assertOk()->assertDontSee($sold->display_name);
    $this->get(route('cars.show', $sold))
        ->assertOk()
        ->assertSee('<title>Sold:', false)
        ->assertSee('This vehicle has been sold.')
        ->assertSee('Enquire about similar cars')
        ->assertSee('Similar available cars')
        ->assertSee($available->display_name)
        ->assertSee('https://schema.org/OutOfStock', false);
});

it('uses used condition only for explicitly foreign used vehicles', function () {
    $foreignUsed = createPublicCar(attributes: ['listing_category' => ListingCategory::ForeignUsed]);
    $preOrder = createPublicCar(attributes: ['listing_category' => ListingCategory::PreOrder]);

    $this->get(route('cars.show', $foreignUsed))->assertSee('https://schema.org/UsedCondition', false);
    $this->get(route('cars.show', $preOrder))
        ->assertDontSee('https://schema.org/UsedCondition', false)
        ->assertDontSee('https://schema.org/NewCondition', false);
});
