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
        public array $hashes,
        public string $pageUrl,
    ) {}

    public function envelope(): Envelope
    {
        $count = count($this->hashes);
        $subject = $count === 1
            ? "New HTML variant detected: {$this->component->name}"
            : "{$count} new HTML variants detected: {$this->component->name}";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-hash-detected',
        );
    }
}
