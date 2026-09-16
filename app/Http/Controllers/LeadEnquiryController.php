<?php

namespace App\Http\Controllers;

use App\Actions\Leads\SendAdminLeadNotification;
use App\Enums\CarStatus;
use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Http\Requests\StoreLeadEnquiryRequest;
use App\Models\Car;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;

class LeadEnquiryController extends Controller
{
    public function __invoke(StoreLeadEnquiryRequest $request, Car $car, SendAdminLeadNotification $notification): RedirectResponse
    {
        abort_if(
            ! in_array($car->status, [CarStatus::Available, CarStatus::Reserved, CarStatus::Sold], true),
            404,
        );

        $lead = Lead::query()->create([
            ...$request->safe()->except('company'),
            'car_id' => $car->getKey(),
            'source' => LeadSource::Website,
            'status' => LeadStatus::New,
        ]);

        $notification->execute($lead);

        return back()->with([
            'enquiry_success' => 'Thanks. Our team will contact you shortly.',
            'analytics_event' => [
                'name' => 'enquiry_submitted',
                'event_uuid' => (string) str()->uuid(),
                'parameters' => [
                    'car_id' => (string) $car->getKey(),
                    'stock_number' => $car->stock_number,
                    'make' => str($car->make)->slug()->toString(),
                    'model' => str($car->model)->slug()->toString(),
                    'year' => (int) $car->year,
                    'status' => $car->status->value,
                    'page_type' => 'car_detail',
                    'enquiry_type' => $car->status === CarStatus::Sold ? 'similar_vehicle' : 'vehicle',
                ],
            ],
        ]);
    }
}
