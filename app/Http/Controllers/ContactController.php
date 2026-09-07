<?php

namespace App\Http\Controllers;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Http\Requests\StoreLeadEnquiryRequest;
use App\Models\Lead;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ContactController extends Controller
{
    public function show(): View
    {
        $business = (array) config('automercy.business');
        $locations = (array) config('automercy.locations');
        $whatsappUrl = $business['whatsapp_url'].'?text='.rawurlencode('Hello Auto Mercy, I would like to speak with your team.');

        return view('contact', compact('business', 'locations', 'whatsappUrl'));
    }

    public function store(StoreLeadEnquiryRequest $request): RedirectResponse
    {
        Lead::query()->create([
            ...$request->safe()->except('company'),
            'source' => LeadSource::Website,
            'status' => LeadStatus::New,
        ]);

        return to_route('contact')->with('contact_success', 'Thanks for reaching out. Our team will contact you shortly.');
    }
}
