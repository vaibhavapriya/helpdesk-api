<?php

namespace App\Helpers;

use App\Models\MailConfig;

class MailHelper
{
    public static function setMailConfig(): void
    {
        $activeMail = MailConfig::where('active', true)->first();

        if (!$activeMail) {
            return;
        }

        config([
            'mail.mailers.smtp.host' => $activeMail->host,
            'mail.mailers.smtp.port' => $activeMail->port,
            'mail.mailers.smtp.encryption' => $activeMail->encryption,
            'mail.mailers.smtp.username' => $activeMail->username,
            'mail.mailers.smtp.password' => $activeMail->password,
            'mail.from.address' => $activeMail->mail_from_address,
            'mail.from.name' => $activeMail->mail_from_name,
        ]);
    }
}
