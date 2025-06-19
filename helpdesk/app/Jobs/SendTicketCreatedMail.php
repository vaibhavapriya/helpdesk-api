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
use App\Helpers\MailHelper;

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
        MailHelper::setMailConfig();

        Mail::mailer('smtp')->to($this->userEmail)->send(new TicketCreatedMail($this->ticket));
    }
}
