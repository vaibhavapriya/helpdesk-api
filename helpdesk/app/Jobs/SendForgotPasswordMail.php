<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPasswordMail;
use App\Models\MailConfig;
use App\Helpers\MailHelper;

class SendForgotPasswordMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $token;

    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }
    public function getUser()
    {
        return $this->user;
    }

    public function getToken()
    {
        return $this->token;
    }
    public function handle(): void
    {
        MailHelper::setMailConfig();

        Mail::mailer('smtp')->to($this->user->email)->send(new ForgotPasswordMail($this->user, $this->token));
    }
}
