<?php

namespace Tests\Browser\Client;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class TicketTest extends DuskTestCase
{
    public function test_client_can_view_tickets()
    {
        $this->browse(function (Browser $browser) {
            // Get any existing user or create one dynamically
            $user = User::first() ;
            $user = $this->loginAsUser($browser, $user);

            $browser->visit('/tickets')
                ->assertSee('Ticket List');

        });
    }

    
}