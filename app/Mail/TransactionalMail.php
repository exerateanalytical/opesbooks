<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Flexible transactional email — powers invoice-sent, payment-received,
 * team-invite, subscription-renewal, DSF reminder, overdue notices, etc.
 */
class TransactionalMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param array<int,string> $lines  Paragraphs (HTML allowed)
     * @param array{url:string,label:string}|null $cta
     */
    public function __construct(
        public string $subjectLine,
        public string $heading,
        public array $lines,
        public ?array $cta = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->subjectLine);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.generic', with: [
            'heading' => $this->heading,
            'lines'   => $this->lines,
            'cta'     => $this->cta,
        ]);
    }
}
