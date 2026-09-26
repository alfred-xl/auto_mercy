<?php

it('renders every business phone number in the shared footer', function () {
    $this->get(route('services'))
        ->assertSee('href="tel:+2348061731673"', false)
        ->assertSee('0806 173 1673')
        ->assertSee('href="tel:+2349033524982"', false)
        ->assertSee('0903 352 4982');
});

it('renders every business phone number in the contact options and footer', function () {
    $response = $this->get(route('contact'));

    expect(substr_count($response->getContent(), 'href="tel:+2348061731673"'))->toBe(2)
        ->and(substr_count($response->getContent(), 'href="tel:+2349033524982"'))->toBe(2);
});

it('publishes every business phone number in structured data', function () {
    $this->get(route('home'))
        ->assertSee('"telephone":["+2348061731673","+2349033524982"]', false);
});
