<?php

namespace App\Http\Controllers;

use App\Enums\CarStatus;
use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Http\Requests\StoreLeadEnquiryRequest;
use App\Models\Car;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;

class LeadEnquiryController extends Controller
{
    public function __invoke(StoreLeadEnquiryRequest $request, Car $car): RedirectResponse
    {
        abort_if(
            ! in_array($car->status, [CarStatus::Available, CarStatus::Reserved, CarStatus::Sold], true),
            404,
        );

        Lead::query()->create([
            ...$request->safe()->except('company'),
            'car_id' => $car->getKey(),
            'source' => LeadSource::Website,
            'status' => LeadStatus::New,
        ]);

        return back()->with('enquiry_success', 'Thanks. Our team will contact you shortly.');
    }
}
