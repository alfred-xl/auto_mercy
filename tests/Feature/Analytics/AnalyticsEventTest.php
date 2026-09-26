<?php

it('renders the installed Google tags in their required positions with privacy-safe vehicle analytics context', function () {
    $car = createPublicCar(attributes: ['make' => 'Toyota', 'model' => 'Camry']);

    $this->get(route('cars.show', $car))
        ->assertSeeInOrder([
            '<head>',
            '<!-- Google Tag Manager -->',
            "'GTM-MS2ZK24B'",
            '<meta charset="utf-8">',
            '<body class=',
            '<!-- Google Tag Manager (noscript) -->',
            'https://www.googletagmanager.com/ns.html?id=GTM-MS2ZK24B',
        ], false)
        ->assertSee('https://www.googletagmanager.com/gtag/js?id=AW-18469766544', false)
        ->assertSee("gtag('config', 'AW-18469766544')", false)
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

it('queues the privacy-safe contact form event only after server acceptance', function () {
    $this->followingRedirects()->post(route('contact.store'), [
        'customer_name' => 'Private Customer',
        'phone' => '+2348000000000',
        'email' => 'private@example.test',
        'message' => 'Please contact me about available cars.',
    ])
        ->assertOk()
        ->assertSee('"name":"contact_form_submit"', false)
        ->assertSee('"page_type":"contact"', false)
        ->assertSee('"enquiry_type":"general"', false)
        ->assertDontSee('private@example.test')
        ->assertDontSee('Please contact me about available cars.');

    $this->assertDatabaseHas('leads', ['customer_name' => 'Private Customer', 'car_id' => null]);
});

it('does not queue the contact form event when a general enquiry is rejected', function () {
    $this->from(route('contact'))
        ->post(route('contact.store'), [])
        ->assertRedirect(route('contact'))
        ->assertSessionHasErrors(['customer_name', 'phone', 'message'])
        ->assertSessionMissing('analytics_event');

    $this->assertDatabaseCount('leads', 0);
});
