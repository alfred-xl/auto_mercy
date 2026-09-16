<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewLeadEnquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Lead $lead) {}

    public function envelope(): Envelope
    {
        $subject = $this->lead->car
            ? 'New vehicle enquiry: '.$this->lead->car->display_name
            : 'New website enquiry from '.$this->lead->customer_name;

        return new Envelope(
            subject: $subject,
            replyTo: filled($this->lead->email) ? [new Address($this->lead->email)] : [],
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.leads.new-enquiry');
    }

    public function attachments(): array
    {
        return [];
    }
}
