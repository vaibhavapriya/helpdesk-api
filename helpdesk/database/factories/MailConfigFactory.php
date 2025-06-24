<?php

namespace Database\Factories;

use App\Models\MailConfig;
use Illuminate\Database\Eloquent\Factories\Factory;

class MailConfigFactory extends Factory
{
    protected $model = MailConfig::class;

    public function definition(): array
    {
        return [
            'mail_from_name' => $this->faker->name,
            'mail_from_address' => $this->faker->safeEmail,
            'host' => 'smtp.test.com',
            'port' => 587,
            'encryption' => 'tls',
            'username' => $this->faker->userName,
            'password' => 'secret',
            'active' => false,
        ];
    }
}
