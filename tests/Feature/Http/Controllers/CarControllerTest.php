<?php

use App\Actions\Cars\TransitionCarStatus;
use App\Enums\CarStatus;
use App\Enums\FuelType;
use App\Enums\MileageUnit;
use App\Enums\TransmissionType;
use App\Models\Car;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

function makeAvailableInventoryCar(array $attributes = []): Car
{
    return app(TransitionCarStatus::class)->execute(createPublishableCar($attributes), CarStatus::Available);
}

function makeReservedInventoryCar(array $attributes = []): Car
{
    $car = makeAvailableInventoryCar($attributes);

    return app(TransitionCarStatus::class)->execute($car, CarStatus::Reserved);
}

it('renders the shared vehicle card with vehicle facts and no card-level whatsapp action', function () {
    $car = makeAvailableInventoryCar([
        'trim' => 'Inventory Card Trim',
        'mileage' => 54_321,
    ]);

    $response = $this->get(route('cars.index'));

    $response->assertOk()
        ->assertSee('Inventory Card Trim')
        ->assertSee('54,321 km')
        ->assertSee($car->transmission->label())
        ->assertSee($car->fuel_type->label())
        ->assertSee('₦'.number_format((float) $car->price_amount))
        ->assertSee('View Details')
        ->assertSee('data-save-vehicle="'.$car->slug.'"', false)
        ->assertSee('aria-pressed="false"', false)
        ->assertSee(config('automercy.business.whatsapp_url'), false);

    preg_match_all('/<article data-vehicle-card.*?<\/article>/s', $response->getContent(), $cards);

    expect($cards[0])->toHaveCount(1)
        ->and($cards[0][0])->toContain('View Details')
        ->toContain('>Available</span>')
        ->toContain('54,321 km')
        ->toContain('Automatic')
        ->toContain('Petrol')
        ->not->toContain('truncate')
        ->not->toContain($car->carStand->name)
        ->not->toContain('WhatsApp')
        ->not->toContain('wa.me');
});

it('keeps inventory pagination at twelve vehicles and uses the normal card grid', function () {
    $firstCar = makeAvailableInventoryCar(['trim' => 'Paginated Vehicle 1']);

    foreach (range(2, 13) as $index) {
        makeAvailableInventoryCar([
            'make_id' => $firstCar->make_id,
            'car_model_id' => $firstCar->car_model_id,
            'body_type_id' => $firstCar->body_type_id,
            'car_stand_id' => $firstCar->car_stand_id,
            'trim' => "Paginated Vehicle {$index}",
        ]);
    }

    $response = $this->get(route('cars.index'));

    $response->assertOk()
        ->assertViewHas('cars', function (LengthAwarePaginator $cars): bool {
            return $cars->perPage() === 12
                && $cars->total() === 13
                && $cars->count() === 12;
        })
        ->assertSee('grid gap-grid md:grid-cols-2 xl:grid-cols-3', false)
        ->assertDontSee('data-latest-cars-carousel', false);

    expect(substr_count($response->getContent(), 'data-vehicle-card'))->toBe(12);
});

it('renders the complete inventory browsing interface', function () {
    makeAvailableInventoryCar(['trim' => 'Inventory Interface Car']);

    $response = $this->get(route('cars.index'));

    $response->assertOk()
        ->assertSee('<h1 class="mt-2 font-display text-h1">Available Cars</h1>', false)
        ->assertSee('Browse quality foreign-used cars currently available from our Lagos car stands.')
        ->assertSee('data-inventory-filter-drawer', false)
        ->assertSee('name="body_type"', false)
        ->assertSee('name="year_min"', false)
        ->assertSee('name="price_min"', false)
        ->assertSee('name="fuel_type"', false)
        ->assertSee('name="availability"', false)
        ->assertSee('data-share-inventory', false)
        ->assertSee('<meta name="robots" content="index,follow">', false);
});

it('shows only currently published available cars by default', function () {
    $available = makeAvailableInventoryCar(['trim' => 'Visible Available Car']);
    $sharedAttributes = [
        'make_id' => $available->make_id,
        'car_model_id' => $available->car_model_id,
        'body_type_id' => $available->body_type_id,
        'car_stand_id' => $available->car_stand_id,
    ];
    createPublishableCar([...$sharedAttributes, 'trim' => 'Hidden Draft Car']);

    $reserved = makeReservedInventoryCar([...$sharedAttributes, 'trim' => 'Hidden Reserved Car']);
    $sold = makeAvailableInventoryCar([...$sharedAttributes, 'trim' => 'Hidden Sold Car']);
    app(TransitionCarStatus::class)->execute($sold, CarStatus::Sold);
    $archived = makeAvailableInventoryCar([...$sharedAttributes, 'trim' => 'Hidden Archived Car']);
    app(TransitionCarStatus::class)->execute($archived, CarStatus::Archived);
    $future = makeAvailableInventoryCar([...$sharedAttributes, 'trim' => 'Hidden Future Car']);
    $future->forceFill(['published_at' => now()->addDay()])->saveQuietly();

    $response = $this->get(route('cars.index'));

    $response->assertSee('Visible Available Car')
        ->assertDontSee('Hidden Draft Car')
        ->assertDontSee($reserved->trim)
        ->assertDontSee('Hidden Sold Car')
        ->assertDontSee('Hidden Archived Car')
        ->assertDontSee('Hidden Future Car');
});

it('shows published reserved cars only when explicitly requested', function () {
    makeAvailableInventoryCar(['trim' => 'Available Only Car']);
    makeReservedInventoryCar(['trim' => 'Explicit Reserved Car']);

    $response = $this->get(route('cars.index', ['availability' => 'reserved']));

    $response->assertSee('Explicit Reserved Car')
        ->assertDontSee('Available Only Car')
        ->assertSee('>Reserved</span>', false)
        ->assertSee('<meta name="robots" content="noindex,follow">', false);
});

it('searches inventory by keyword and preserves the submitted value', function () {
    makeAvailableInventoryCar(['trim' => 'Distinctive Needle Trim']);
    makeAvailableInventoryCar(['trim' => 'Unrelated Vehicle']);

    $response = $this->get(route('cars.index', ['q' => 'Needle']));

    $response->assertSee('Distinctive Needle Trim')
        ->assertDontSee('Unrelated Vehicle')
        ->assertSee('value="Needle"', false)
        ->assertSee('Search: “Needle”');
});

it('filters by make and a compatible model', function () {
    $matching = makeAvailableInventoryCar(['trim' => 'Matching Make Model']);
    makeAvailableInventoryCar(['trim' => 'Different Make Model']);

    $response = $this->get(route('cars.index', [
        'make' => $matching->make->slug,
        'model' => $matching->carModel->slug,
    ]));

    $response->assertSee('Matching Make Model')
        ->assertDontSee('Different Make Model')
        ->assertSee($matching->make->name)
        ->assertSee($matching->carModel->name);
});

it('rejects a model that does not belong to the selected make', function () {
    $first = makeAvailableInventoryCar(['trim' => 'First Taxonomy Car']);
    $second = makeAvailableInventoryCar(['trim' => 'Second Taxonomy Car']);

    $response = $this->from(route('cars.index'))->get(route('cars.index', [
        'make' => $first->make->slug,
        'model' => $second->carModel->slug,
    ]));

    $response->assertRedirect(route('cars.index'))
        ->assertSessionHasErrors(['model' => 'The selected model does not belong to the selected make.']);
});

it('filters each supported vehicle specification', function () {
    $matching = makeAvailableInventoryCar([
        'trim' => 'Specification Match',
        'year' => 2021,
        'price_amount' => 24_000_000,
        'mileage' => 52_000,
        'mileage_unit' => MileageUnit::Kilometres,
        'transmission' => TransmissionType::Manual,
        'fuel_type' => FuelType::Diesel,
    ]);
    makeAvailableInventoryCar([
        'trim' => 'Specification Miss',
        'year' => 2016,
        'price_amount' => 42_000_000,
        'mileage' => 130_000,
        'transmission' => TransmissionType::Automatic,
        'fuel_type' => FuelType::Petrol,
    ]);

    $queries = [
        ['body_type' => $matching->bodyType->slug],
        ['year_min' => 2020, 'year_max' => 2022],
        ['price_min' => 20_000_000, 'price_max' => 30_000_000],
        ['transmission' => 'manual'],
        ['fuel_type' => 'diesel'],
        ['mileage_min' => 50_000, 'mileage_max' => 60_000],
        ['car_stand' => $matching->carStand->slug],
    ];

    foreach ($queries as $query) {
        $this->get(route('cars.index', $query))
            ->assertSee('Specification Match')
            ->assertDontSee('Specification Miss');
    }
});

it('combines multiple inventory filters', function () {
    $matching = makeAvailableInventoryCar([
        'trim' => 'Combined Match',
        'year' => 2022,
        'price_amount' => 28_000_000,
        'transmission' => TransmissionType::Automatic,
    ]);
    makeAvailableInventoryCar([
        'trim' => 'Combined Miss',
        'make_id' => $matching->make_id,
        'car_model_id' => $matching->car_model_id,
        'body_type_id' => $matching->body_type_id,
        'car_stand_id' => $matching->car_stand_id,
        'year' => 2014,
        'price_amount' => 48_000_000,
        'transmission' => TransmissionType::Manual,
    ]);

    $response = $this->get(route('cars.index', [
        'make' => $matching->make->slug,
        'year_min' => 2020,
        'price_max' => 30_000_000,
        'transmission' => 'automatic',
    ]));

    $response->assertSee('Combined Match')->assertDontSee('Combined Miss');
});

it('rejects inverted ranges with an accessible validation message', function (array $query, string $field, string $message) {
    $response = $this->from(route('cars.index'))->get(route('cars.index', $query));

    $response->assertRedirect(route('cars.index'))
        ->assertSessionHasErrors([$field => $message]);
})->with([
    'year range' => [['year_min' => 2022, 'year_max' => 2020], 'year_max', 'The minimum year cannot be greater than the maximum year.'],
    'price range' => [['price_min' => 30_000_000, 'price_max' => 20_000_000], 'price_max', 'The minimum price cannot be greater than the maximum price.'],
    'mileage range' => [['mileage_min' => 80_000, 'mileage_max' => 50_000], 'mileage_max', 'The minimum mileage cannot be greater than the maximum mileage.'],
]);

it('renders a controlled zero state after invalid filters', function () {
    makeAvailableInventoryCar(['trim' => 'Must Not Be Exposed']);

    $response = $this->followingRedirects()->get(route('cars.index', [
        'price_min' => 30_000_000,
        'price_max' => 20_000_000,
    ]));

    $response->assertSee('Some filters need attention')
        ->assertSee('The minimum price cannot be greater than the maximum price.')
        ->assertSee('No inventory has been shown for the invalid request.')
        ->assertDontSee('Must Not Be Exposed');
});

it('rejects invalid controlled and taxonomy filter values', function (array $query, string $field) {
    $response = $this->from(route('cars.index'))->get(route('cars.index', $query));

    $response->assertRedirect(route('cars.index'))->assertSessionHasErrors($field);
})->with([
    'make slug' => [['make' => 'not-a-real-make'], 'make'],
    'body type slug' => [['body_type' => 'not-a-real-body'], 'body_type'],
    'car stand slug' => [['car_stand' => 'not-a-real-stand'], 'car_stand'],
    'transmission' => [['transmission' => 'semi-magical'], 'transmission'],
    'fuel type' => [['fuel_type' => 'water'], 'fuel_type'],
    'availability' => [['availability' => 'sold'], 'availability'],
    'sort' => [['sort' => 'random'], 'sort'],
]);

it('sorts inventory with deterministic secondary ordering', function (string $sort, array $expectedOrder) {
    $shared = makeAvailableInventoryCar([
        'trim' => 'Sort Alpha',
        'price_amount' => 30_000_000,
        'year' => 2018,
        'mileage' => 80_000,
    ]);
    $bravo = makeAvailableInventoryCar([
        'trim' => 'Sort Bravo',
        'make_id' => $shared->make_id,
        'car_model_id' => $shared->car_model_id,
        'body_type_id' => $shared->body_type_id,
        'car_stand_id' => $shared->car_stand_id,
        'price_amount' => 10_000_000,
        'year' => 2022,
        'mileage' => 40_000,
    ]);
    $charlie = makeAvailableInventoryCar([
        'trim' => 'Sort Charlie',
        'make_id' => $shared->make_id,
        'car_model_id' => $shared->car_model_id,
        'body_type_id' => $shared->body_type_id,
        'car_stand_id' => $shared->car_stand_id,
        'price_amount' => 20_000_000,
        'year' => 2020,
        'mileage' => 60_000,
    ]);
    $shared->forceFill(['published_at' => now()->subDays(3)])->saveQuietly();
    $bravo->forceFill(['published_at' => now()->subDays(2)])->saveQuietly();
    $charlie->forceFill(['published_at' => now()->subDay()])->saveQuietly();

    $query = $sort === 'latest' ? [] : ['sort' => $sort];
    $response = $this->get(route('cars.index', $query));

    $response->assertSeeInOrder($expectedOrder);
})->with([
    'latest' => ['latest', ['Sort Charlie', 'Sort Bravo', 'Sort Alpha']],
    'price ascending' => ['price_asc', ['Sort Bravo', 'Sort Charlie', 'Sort Alpha']],
    'price descending' => ['price_desc', ['Sort Alpha', 'Sort Charlie', 'Sort Bravo']],
    'year descending' => ['year_desc', ['Sort Bravo', 'Sort Charlie', 'Sort Alpha']],
    'mileage ascending' => ['mileage_asc', ['Sort Bravo', 'Sort Charlie', 'Sort Alpha']],
]);

it('uses the newest publication and highest id to break equal sort values', function () {
    $first = makeAvailableInventoryCar(['trim' => 'Older Tie', 'price_amount' => 20_000_000]);
    $second = makeAvailableInventoryCar([
        'trim' => 'Newer Tie',
        'make_id' => $first->make_id,
        'car_model_id' => $first->car_model_id,
        'body_type_id' => $first->body_type_id,
        'car_stand_id' => $first->car_stand_id,
        'price_amount' => 20_000_000,
    ]);
    $publishedAt = now()->subDay();
    $first->forceFill(['published_at' => $publishedAt])->saveQuietly();
    $second->forceFill(['published_at' => $publishedAt])->saveQuietly();

    $response = $this->get(route('cars.index', ['sort' => 'price_asc']));

    $response->assertSeeInOrder(['Newer Tie', 'Older Tie']);
});

it('filters and sorts mileage in normalized kilometres', function () {
    $kilometreCar = makeAvailableInventoryCar([
        'trim' => 'Kilometre Match',
        'mileage' => 55_000,
        'mileage_unit' => MileageUnit::Kilometres,
    ]);
    makeAvailableInventoryCar([
        'trim' => 'Converted Miles Miss',
        'make_id' => $kilometreCar->make_id,
        'car_model_id' => $kilometreCar->car_model_id,
        'body_type_id' => $kilometreCar->body_type_id,
        'car_stand_id' => $kilometreCar->car_stand_id,
        'mileage' => 40_000,
        'mileage_unit' => MileageUnit::Miles,
    ]);

    $response = $this->get(route('cars.index', ['mileage_max' => 60_000]));

    $response->assertSee('Kilometre Match')->assertDontSee('Converted Miles Miss');
});

it('renders human-readable chips whose removal preserves other filters', function () {
    $car = makeAvailableInventoryCar(['trim' => 'Chip Vehicle', 'transmission' => TransmissionType::Automatic]);
    $query = [
        'make' => $car->make->slug,
        'body_type' => $car->bodyType->slug,
        'transmission' => 'automatic',
    ];

    $response = $this->get(route('cars.index', $query));
    $removeBodyTypeUrl = route('cars.index', [
        'make' => $car->make->slug,
        'transmission' => 'automatic',
    ]);

    $response->assertSee($car->make->name)
        ->assertSee($car->bodyType->name)
        ->assertSee('Automatic')
        ->assertSee('aria-label="Remove '.$car->bodyType->name.' filter"', false)
        ->assertSee('href="'.e($removeBodyTypeUrl).'"', false)
        ->assertSee('value="'.$car->make->slug.'" selected', false);
});

it('preserves active filters throughout pagination', function () {
    $firstCar = makeAvailableInventoryCar(['trim' => 'Filtered Page Vehicle 1']);

    foreach (range(2, 13) as $index) {
        makeAvailableInventoryCar([
            'make_id' => $firstCar->make_id,
            'car_model_id' => $firstCar->car_model_id,
            'body_type_id' => $firstCar->body_type_id,
            'car_stand_id' => $firstCar->car_stand_id,
            'trim' => "Filtered Page Vehicle {$index}",
        ]);
    }

    $response = $this->get(route('cars.index', ['make' => $firstCar->make->slug]));

    $response->assertViewHas('cars', function (LengthAwarePaginator $cars) use ($firstCar): bool {
        return str_contains($cars->url(2), 'make='.$firstCar->make->slug)
            && str_contains($cars->url(2), 'page=2');
    });
    expect(substr_count($response->getContent(), 'data-vehicle-card'))->toBe(12);
});

it('returns not found for an out-of-range inventory page', function () {
    makeAvailableInventoryCar();

    $this->get(route('cars.index', ['page' => 2]))->assertNotFound();
});

it('normalizes defaults and unsupported parameters to a clean URL', function (array $query) {
    $this->get(route('cars.index', $query))
        ->assertRedirect(route('cars.index'))
        ->assertStatus(301);
})->with([
    'page one' => [['page' => 1]],
    'latest sort' => [['sort' => 'latest']],
    'available status' => [['availability' => 'available']],
    'unsupported parameter' => [['utm_source' => 'test']],
]);

it('renders separate global and filtered empty states', function () {
    $this->get(route('cars.index'))
        ->assertSee('Available cars are being updated')
        ->assertSee('Ask on WhatsApp');

    makeAvailableInventoryCar(['trim' => 'Existing Public Car']);

    $this->get(route('cars.index', ['q' => 'No Such Vehicle']))
        ->assertSee('No cars match these filters')
        ->assertSee('Clear All')
        ->assertSee('No Such Vehicle');
});

it('uses filtered SEO directives and clean canonical URLs', function () {
    makeAvailableInventoryCar(['trim' => 'SEO Filter Car']);

    $response = $this->get(route('cars.index', ['q' => 'SEO']));

    $response->assertSee('<meta name="robots" content="noindex,follow">', false)
        ->assertSee('<link rel="canonical" href="'.route('cars.index').'">', false);
});

it('self-canonicalizes an unfiltered pagination page', function () {
    $firstCar = makeAvailableInventoryCar(['trim' => 'Canonical Page Vehicle 1']);

    foreach (range(2, 13) as $index) {
        makeAvailableInventoryCar([
            'make_id' => $firstCar->make_id,
            'car_model_id' => $firstCar->car_model_id,
            'body_type_id' => $firstCar->body_type_id,
            'car_stand_id' => $firstCar->car_stand_id,
            'trim' => "Canonical Page Vehicle {$index}",
        ]);
    }

    $pageTwoUrl = route('cars.index', ['page' => 2]);
    $response = $this->get($pageTwoUrl);

    $response->assertSee('<meta name="robots" content="index,follow">', false)
        ->assertSee('<link rel="canonical" href="'.$pageTwoUrl.'">', false);
});

it('does not add relationship queries for each rendered card', function () {
    $firstCar = makeAvailableInventoryCar(['trim' => 'Query Count Vehicle 1']);

    DB::flushQueryLog();
    DB::enableQueryLog();
    $this->get(route('cars.index'))->assertOk();
    $singleCardQueryCount = count(DB::getQueryLog());

    foreach (range(2, 12) as $index) {
        makeAvailableInventoryCar([
            'make_id' => $firstCar->make_id,
            'car_model_id' => $firstCar->car_model_id,
            'body_type_id' => $firstCar->body_type_id,
            'car_stand_id' => $firstCar->car_stand_id,
            'trim' => "Query Count Vehicle {$index}",
        ]);
    }

    DB::flushQueryLog();
    $this->get(route('cars.index'))->assertOk();
    $twelveCardQueryCount = count(DB::getQueryLog());
    DB::disableQueryLog();

    expect($twelveCardQueryCount)->toBe($singleCardQueryCount);
});
