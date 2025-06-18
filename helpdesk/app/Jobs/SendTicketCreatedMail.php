<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketCreatedMail;
use App\Models\MailConfig;

class SendTicketCreatedMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ticket;
    protected $userEmail;

    public function __construct($ticket, $userEmail)
    {
        $this->ticket = $ticket;
        $this->userEmail = $userEmail;
    }

    public function handle(): void
    {
        $activeMail = MailConfig::where('active', true)->first();

        config([
            'mail.mailers.smtp.host' => $activeMail->host,
            'mail.mailers.smtp.port' => $activeMail->port,
            'mail.mailers.smtp.encryption' => $activeMail->encryption,
            'mail.mailers.smtp.username' => $activeMail->username,
            'mail.mailers.smtp.password' => $activeMail->password,
            'mail.from.address' => $activeMail->mail_from_address,
            'mail.from.name' => $activeMail->mail_from_name,
        ]);

        Mail::mailer('smtp')->to($this->userEmail)->send(new TicketCreatedMail($this->ticket));
    }
}
