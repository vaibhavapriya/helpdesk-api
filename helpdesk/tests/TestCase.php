<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Passport\Client;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // Run fresh migrations (clears all tables and migrates again)
        $this->artisan('migrate:fresh')->run();

        // Install Passport keys and clients non-interactively, only if needed
        if (!Client::where('provider', 'users')->exists()) {
            $this->artisan('passport:install', ['--no-interaction' => true, '--force' => true])->run();
        }
    }
}
