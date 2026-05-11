<?php

namespace App\Mail;

use App\Models\Alert;
use App\Models\Leader;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AlertAssigned extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Alert $alert,
        public readonly Leader $leader,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[BCC Alert] You have been assigned: ' . $this->alert->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.alert-assigned',
        );
    }
}
