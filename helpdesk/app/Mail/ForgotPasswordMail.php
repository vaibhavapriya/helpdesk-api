<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;

    public function __construct(User $user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function build()
    {
        $activeMail = \App\Models\MailConfig::where('active', true)->first();

        return $this->from($activeMail->mail_from_address, $activeMail->mail_from_name)
                    ->subject('Reset Password for ' . $this->user->email)
                    ->view('emails.forgot_password')
                    ->with([
                        'user' => $this->user,
                        'token' => $this->token,
                    ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Forgot Password Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
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
    public function buildOLd()
    {
        $activeMail = \App\Models\MailConfig::where('active', true)->first();

        return $this->from($activeMail->mail_from_address, $activeMail->mail_from_name)
                    ->subject('Forgot Passward for the account: ' . $this->user->email)
                    ->view('emails.forgot_password')
                    ->with(['user' => $this->user]);
    }
}
