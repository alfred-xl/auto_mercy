<?php

use App\Mail\NewLeadEnquiryMail;
use Illuminate\Support\Facades\Mail;

beforeEach(function (): void {
    Mail::fake();
    config()->set('automercy.notifications.enquiry_email', 'sales@example.test');
});

it('emails the configured administrator after accepting a general enquiry', function () {
    $this->post(route('contact.store'), [
        'customer_name' => 'Ada Customer',
        'phone' => '+2348000000000',
        'email' => 'ada@example.test',
        'message' => 'Please contact me about available cars.',
    ])->assertRedirect(route('contact'));

    Mail::assertSent(NewLeadEnquiryMail::class, function (NewLeadEnquiryMail $mail): bool {
        return $mail->hasTo('sales@example.test')
            && $mail->hasReplyTo('ada@example.test')
            && $mail->lead->car_id === null
            && $mail->lead->customer_name === 'Ada Customer';
    });
});

it('emails the configured administrator with vehicle details after accepting a vehicle enquiry', function () {
    $car = createPublicCar(attributes: ['make' => 'Toyota', 'model' => 'Camry']);

    $this->post(route('cars.enquiries.store', $car), [
        'customer_name' => 'Tunde Customer',
        'phone' => '+2348111111111',
        'email' => 'tunde@example.test',
        'message' => 'I would like to inspect this car.',
    ])->assertRedirect();

    Mail::assertSent(NewLeadEnquiryMail::class, function (NewLeadEnquiryMail $mail) use ($car): bool {
        return $mail->hasTo('sales@example.test')
            && $mail->lead->car?->is($car)
            && str_contains($mail->envelope()->subject, $car->display_name);
    });
});
