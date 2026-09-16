<x-mail::message>
# New {{ $lead->car ? 'vehicle' : 'website' }} enquiry

A customer submitted an enquiry through the Auto Mercy website.

<x-mail::table>
| Detail | Information |
| :-- | :-- |
| Name | {{ $lead->customer_name }} |
| Phone | {{ $lead->phone }} |
| Email | {{ $lead->email ?: 'Not provided' }} |
@if ($lead->car)
| Vehicle | {{ $lead->car->display_name }} |
| Stock number | {{ $lead->car->stock_number }} |
| Availability | {{ $lead->car->status->label() }} |
@endif
| Submitted | {{ $lead->created_at?->timezone(config('app.timezone'))->format('j M Y, g:i A') }} |
</x-mail::table>

## Message

{{ $lead->message }}

<x-mail::button :url="route('filament.admin.resources.leads.view', ['record' => $lead])">
View enquiry in admin
</x-mail::button>

Regards,<br>
{{ config('app.name') }} Website
</x-mail::message>
