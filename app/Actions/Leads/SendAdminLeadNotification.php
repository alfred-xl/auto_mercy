<?php

namespace App\Actions\Leads;

use App\Mail\NewLeadEnquiryMail;
use App\Models\Lead;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendAdminLeadNotification
{
    public function execute(Lead $lead): void
    {
        $recipient = (string) config('automercy.notifications.enquiry_email');

        if (! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            Log::warning('Admin enquiry email was not sent because AUTOMERCY_ENQUIRY_EMAIL is not configured with a valid address.', [
                'lead_id' => $lead->getKey(),
            ]);

            return;
        }

        try {
            Mail::to($recipient)->send(new NewLeadEnquiryMail($lead->loadMissing('car')));
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
