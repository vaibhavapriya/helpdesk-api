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
    public function test_client_can_view_single_ticket()
    {
        $this->browse(function (Browser $browser) {
            // Assume there's at least one ticket and visit it
            $browser->visit('/tickets')
                    ->waitFor('a[href^="/tickets/"]', 5)
                    ->click('a[href^="/tickets/"]') // clicks the first ticket link
                    ->pause(1000)
                    ->assertSee('Description')   // or something unique to the show page
                    ->assertPathBeginsWith('/tickets/');
        });
    }

    public function test__client_can_edit_ticket()
    {
        $this->browse(function (Browser $browser) {
            
            $browser->visit('/tickets')
                    ->pause(2000) // wait for JS to render
                    ->clickLink('Edit') // or use ->click('.btn-warning') if button
                    ->assertPathBeginsWith('/tickets/') // confirm navigation to edit
                    ->waitFor('#edit-ticket-form', 5)
                    ->assertSee('Edit Ticket'); // or some label on the edit page
        });
    }

     public function test_client_can_create_ticket()
    {
        $this->browse(function (Browser $browser) {

            // Visit the create ticket page and fill the form
            $browser->visit('/tickets/create') // Adjust route as needed
                ->pause(1000)
                ->type('title', 'Sample Ticket')
                ->select('priority', 'medium')
                ->type('department', 'Support')
                ->type('description', 'This is a test ticket created by Dusk.')
                // ->attach('attachment', __DIR__.'/files/sample.jpg') // Make sure this file exists
                ->press('Submit');
        });
    }
    // public function test_client_can_delete_ticket()
    // {
    //     $this->browse(function (Browser $browser) {

    //         $browser->visit('/tickets')
    //                 ->pause(3000) // allow time for tickets to load
    //                 ->script("document.querySelector('.btn-delete')?.click();");

    //         $browser->pause(2000)
    //                 ->assertDialogOpened('Deleted') // if your JS uses alert('Deleted')
    //                 ->acceptDialog();

    //         // Optional: Check the ticket is gone by checking the DOM or message
    //         // $browser->assertDontSee('Ticket Title');
    //     });
    // }

}