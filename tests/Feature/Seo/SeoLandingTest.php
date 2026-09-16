<?php

use Illuminate\Support\Facades\Schema;

beforeEach(function (): void {
    config()->set('automercy.seo.base_url', 'https://www.example.test');
    config()->set('automercy.seo.landings.makes/toyota.published', true);
});

it('publishes only configured landings backed by the shared inventory', function () {
    $toyota = createPublicCar(attributes: ['make' => 'Toyota', 'model' => 'Camry']);
    $honda = createPublicCar(attributes: ['make' => 'Honda', 'model' => 'Accord']);

    $response = $this->get('/cars/makes/toyota')
        ->assertOk()
        ->assertSee('Toyota cars for sale in Lagos')
        ->assertSee($toyota->display_name)
        ->assertDontSee($honda->display_name)
        ->assertSee('/cars/'.$toyota->slug, false)
        ->assertSee('<meta name="robots" content="index,follow">', false);

    expect(Schema::hasColumn('cars', 'location_id'))->toBeFalse()
        ->and(Schema::hasColumn('cars', 'branch_id'))->toBeFalse();

    $response->assertSee('one shared inventory', false);
    $this->get('/cars/makes/not-published')->assertNotFound();
});

it('normalizes landing pagination and self canonicalizes real later pages', function () {
    foreach (range(1, 13) as $index) {
        createPublicCar(attributes: ['make' => 'Toyota', 'model' => 'Model '.$index]);
    }

    $this->get('/cars/makes/toyota?page=1&utm_campaign=launch')
        ->assertStatus(301)
        ->assertRedirect('/cars/makes/toyota?utm_campaign=launch');

    $this->get('/cars/makes/toyota?page=02&utm_campaign=launch&unsupported=drop')
        ->assertStatus(301)
        ->assertRedirect('/cars/makes/toyota?page=2&utm_campaign=launch');

    $this->get('/cars/makes/toyota?page=2&utm_campaign=launch')
        ->assertOk()
        ->assertSee('<link rel="canonical" href="https://www.example.test/cars/makes/toyota?page=2">', false)
        ->assertDontSee('launch');

    $this->get('/cars/makes/toyota?page=3')->assertNotFound();
});

it('keeps a temporarily empty published page useful but noindexed and out of the sitemap', function () {
    $response = $this->get('/cars/makes/toyota')
        ->assertOk()
        ->assertSee('No matching cars are currently published')
        ->assertSee('<meta name="robots" content="noindex,follow">', false);

    $response->assertSee('Contact Auto Mercy');
    $this->get('/sitemap.xml')->assertDontSee('/cars/makes/toyota', false);
});
