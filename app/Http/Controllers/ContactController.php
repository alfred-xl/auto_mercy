<?php

namespace App\Http\Controllers;

use App\Actions\Leads\SendAdminLeadNotification;
use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Http\Requests\StoreLeadEnquiryRequest;
use App\Models\Lead;
use App\Support\SeoStructuredData;
use App\Support\SeoUrl;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ContactController extends Controller
{
    public function show(SeoUrl $urls, SeoStructuredData $structuredData): View
    {
        $business = (array) config('automercy.business');
        $locations = (array) config('automercy.locations');
        $whatsappUrl = $business['whatsapp_url'].'?text='.rawurlencode('Hello Auto Mercy, I would like to speak with your team.');
        $canonical = $urls->route('contact');
        $structuredData = $structuredData->businessGraph($canonical);

        return view('contact', compact('business', 'locations', 'whatsappUrl', 'canonical', 'structuredData'));
    }

    public function store(StoreLeadEnquiryRequest $request, SendAdminLeadNotification $notification): RedirectResponse
    {
        $lead = Lead::query()->create([
            ...$request->safe()->except('company'),
            'source' => LeadSource::Website,
            'status' => LeadStatus::New,
        ]);

        $notification->execute($lead);

        return to_route('contact')->with([
            'contact_success' => 'Thanks for reaching out. Our team will contact you shortly.',
            'analytics_event' => [
                'name' => 'enquiry_submitted',
                'event_uuid' => (string) str()->uuid(),
                'parameters' => ['page_type' => 'contact', 'enquiry_type' => 'general'],
            ],
        ]);
    }
}
