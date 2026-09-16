<?php

it('renders the installed Google tag and privacy-safe vehicle analytics context', function () {
    $car = createPublicCar(attributes: ['make' => 'Toyota', 'model' => 'Camry']);

    $this->get(route('cars.show', $car))
        ->assertOk()
        ->assertSee('https://www.googletagmanager.com/gtag/js?id=G-TCW2YWKXRS', false)
        ->assertSee("gtag('config', 'G-TCW2YWKXRS')", false)
        ->assertSee('"page_type":"car_detail"', false)
        ->assertSee('"car_id":"'.$car->getKey().'"', false);
});

it('queues enquiry success only after a vehicle enquiry is accepted', function () {
    $car = createPublicCar();
    $payload = [
        'customer_name' => 'Private Customer',
        'phone' => '+2348000000000',
        'email' => 'private@example.test',
        'message' => 'Private enquiry details',
    ];

    $this->followingRedirects()->post(route('cars.enquiries.store', $car), $payload)
        ->assertOk()
        ->assertSee('"name":"enquiry_submitted"', false)
        ->assertSee('"enquiry_type":"vehicle"', false)
        ->assertDontSee('Private Customer')
        ->assertDontSee('private@example.test')
        ->assertDontSee('Private enquiry details');

    $this->assertDatabaseHas('leads', ['car_id' => $car->getKey(), 'customer_name' => 'Private Customer']);
});

it('does not report rejected enquiries as successful', function () {
    $car = createPublicCar();

    $this->from(route('cars.show', $car))
        ->post(route('cars.enquiries.store', $car), [])
        ->assertRedirect(route('cars.show', $car))
        ->assertSessionHasErrors(['customer_name', 'phone', 'message'])
        ->assertSessionMissing('analytics_event');
});

it('queues a privacy-safe general enquiry event only after server acceptance', function () {
    $this->followingRedirects()->post(route('contact.store'), [
        'customer_name' => 'Private Customer',
        'phone' => '+2348000000000',
        'email' => 'private@example.test',
        'message' => 'Please contact me about available cars.',
    ])
        ->assertOk()
        ->assertSee('"name":"enquiry_submitted"', false)
        ->assertSee('"page_type":"contact"', false)
        ->assertSee('"enquiry_type":"general"', false)
        ->assertDontSee('private@example.test')
        ->assertDontSee('Please contact me about available cars.');

    $this->assertDatabaseHas('leads', ['customer_name' => 'Private Customer', 'car_id' => null]);
});
