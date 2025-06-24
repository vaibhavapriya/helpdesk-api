<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use App\Models\User;
use App\Models\Ticket;
use Tests\TestCase;


class TicketTest extends TestCase
{
    use RefreshDatabase;

    /*ticket controller:: index */
    public function test_user_can_get_paginated_tickets()
    {
        // Create a user and authenticate with Passport
        $user = User::factory()->create();
        Passport::actingAs($user);

        // Create some tickets for the user
        Ticket::factory()->count(20)->create([
            'requester_id' => $user->id
        ]);

        // Act: Hit the API route
        $response = $this->getJson('/api/mytickets');

        // Assert: Check status and structure
        $response->assertStatus(200)
                 ->assertJsonStructure([
                    'success',
                    'data',
                    'meta' => [
                        'current_page',
                        'next_page_url',
                        'per_page',
                        'prev_page_url',
                    ]
                 ]);

        // Optional: Check data count
        $this->assertCount(15, $response->json('data')); // simplePaginate(15)
    }

}
