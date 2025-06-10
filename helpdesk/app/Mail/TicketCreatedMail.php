<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\MailConfig;
use App\Models\Ticket; 

class TicketCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;

    /**
     * Create a new message instance.
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ticket Created Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket_created',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    public function build()
    {
        $activeMail = \App\Models\MailConfig::where('active', true)->first();
        config([
            'mail.mailers.smtp.host' => $activeMail->host,
            'mail.mailers.smtp.port' => $activeMail->port,
            'mail.mailers.smtp.encryption' => $activeMail->encryption,
            'mail.mailers.smtp.username' => $activeMail->username,
            'mail.mailers.smtp.password' => $activeMail->password, // decrypt if encrypted
            'mail.from.address' => $activeMail->mail_from_address,
            'mail.from.name' => $activeMail->mail_from_name,
        ]);
        
        return $this->from($activeMail->mail_from_address, $activeMail->mail_from_name)
                    ->subject('New Ticket Created: ' . $this->ticket->title)
                    ->view('emails.ticket_created')
                    ->with(['ticket' => $this->ticket]);
    }
}
