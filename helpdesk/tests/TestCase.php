<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // Run fresh migrations before each test (or you can run once per test suite)
        Artisan::call('migrate:fresh');

        // Insert Passport clients manually
        DB::table('oauth_clients')->insert([
            [
                'id' => '0197a5f1-968a-7104-b890-18b8e8edae92',
                'owner_type' => null,
                'owner_id' => null,
                'name' => 'Laravel',
                'secret' => '$2y$12$61do0t3rhMitcx3/', // truncated, put full secret here
                'provider' => null, // or 'users' if needed
                'redirect_uris' => json_encode(['http://localhost']),  // JSON string
                'grant_types' => json_encode(['authorization_code', 'refresh_token', 'personal_access', 'password']), // example array, encode as JSON
                'revoked' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]

        ]);
    }
}


