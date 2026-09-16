<?php

use App\Enums\CarStatus;
use App\Models\Car;
use App\Models\User;

beforeEach(function (): void {
    config()->set('automercy.seo.base_url', 'https://www.example.test');
});

it('normalizes page one and preserves only allowlisted attribution parameters', function () {
    $this->get('/cars?page=1&utm_source=google&gclid=click-123&unexpected=drop-me')
        ->assertStatus(301)
        ->assertRedirect('/cars?utm_source=google&gclid=click-123');

    $response = $this->get('/cars?utm_source=google&gclid=click-123');

    $response->assertOk()
        ->assertSee('<link rel="canonical" href="https://www.example.test/cars">', false)
        ->assertSee('<meta property="og:url" content="https://www.example.test/cars">', false)
        ->assertDontSee('click-123');

    $this->get('/cars?page=02&utm_source%5B0%5D=malformed')
        ->assertStatus(301)
        ->assertRedirect('/cars?page=2');
});

it('self canonicalizes unfiltered pagination and returns a real 404 out of range', function () {
    foreach (range(1, 13) as $index) {
        createPublicCar(attributes: ['make' => 'Toyota', 'model' => 'Model '.$index]);
    }

    $this->get('/cars?page=2&utm_campaign=launch')
        ->assertOk()
        ->assertSee('<meta name="robots" content="index,follow">', false)
        ->assertSee('<link rel="canonical" href="https://www.example.test/cars?page=2">', false)
        ->assertDontSee('launch');

    $this->get('/cars?page=3')->assertNotFound();
    $this->get('/cars?page=0')->assertNotFound();
    $this->get('/cars?page=not-a-number')->assertNotFound();
});

it('keeps materially different filters self canonical while excluding them from indexing', function () {
    createPublicCar(attributes: ['make' => 'Toyota']);

    $this->get('/cars?make=Toyota&sort=price_asc')
        ->assertOk()
        ->assertSee('<meta name="robots" content="noindex,follow">', false)
        ->assertSee('<link rel="canonical" href="https://www.example.test/cars?make=Toyota&amp;sort=price_asc">', false);
});

it('noindexes an empty main inventory and omits it from the sitemap', function () {
    $this->get('/cars')
        ->assertOk()
        ->assertSee('<meta name="robots" content="noindex,follow">', false);

    $this->get('/sitemap.xml')->assertDontSee('<loc>https://www.example.test/cars</loc>', false);
});

it('represents one organization and two linked physical locations', function () {
    foreach (['/', '/contact'] as $path) {
        $response = $this->get($path)->assertOk();
        $html = $response->getContent();

        expect(substr_count($html, '"@type":"Organization"'))->toBe(1)
            ->and(substr_count($html, '"@type":"AutoDealer"'))->toBe(2)
            ->and(substr_count($html, '"parentOrganization"'))->toBe(2);

        $response
            ->assertSee('9 Moshalashi Alao Street')
            ->assertSee('6/8 Ogunnisi Road')
            ->assertSee('Monday-Saturday, 8:00 AM-6:00 PM')
            ->assertSee('confirm', false);
    }
});

it('enforces draft archived unknown and authenticated preview behavior', function () {
    $draft = Car::factory()->create();
    $archived = createPublicCar(CarStatus::Archived);

    $this->get(route('cars.show', $draft))->assertNotFound();
    $this->get(route('cars.show', $archived))->assertStatus(410);
    $this->get('/cars/not-a-real-car')->assertNotFound();
    $this->get(route('admin.cars.preview', $draft->getKey()))->assertRedirect();

    $this->actingAs(User::factory()->create())
        ->get(route('admin.cars.preview', $draft->getKey()))
        ->assertOk()
        ->assertSee('<meta name="robots" content="noindex,nofollow">', false)
        ->assertDontSee('view_car');
});

it('includes only indexable shared inventory in the sitemap', function () {
    $available = createPublicCar();
    $reserved = createPublicCar(CarStatus::Reserved);
    $sold = createPublicCar(CarStatus::Sold);
    $draft = Car::factory()->create();
    $archived = createPublicCar(CarStatus::Archived);

    $response = $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

    foreach ([$available, $reserved, $sold] as $car) {
        $response->assertSee('https://www.example.test/cars/'.$car->slug, false);
    }

    foreach ([$draft, $archived] as $car) {
        $response->assertDontSee('https://www.example.test/cars/'.$car->slug, false);
    }

    $response->assertSee('<lastmod>', false)
        ->assertDontSee('/admin', false)
        ->assertDontSee('?utm_', false);
});
