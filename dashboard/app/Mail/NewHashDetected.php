<?php

namespace App\Mail;

use App\Models\Component;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewHashDetected extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Component $component,
        public string $hash,
        public string $pageUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New HTML variant detected: {$this->component->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-hash-detected',
        );
    }
}
