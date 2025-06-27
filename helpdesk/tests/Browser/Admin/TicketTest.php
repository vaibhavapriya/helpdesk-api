<?php

namespace Tests\Browser\Admin;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TicketTest extends DuskTestCase
{
    
    public function test_admin_can_view_tickets_with_filter()
    {
        $this->browse(function (Browser $browser) {
            $admin = User::where('role', 'admin')->first();
            $this->loginAsUser($browser, $admin);

            $browser->visit('/admin/tickets')
                ->pause(2000) // Let JS load tickets
                ->assertSee('Ticket List')
                ->type('#searchInput', 'Sample Ticket')
                ->pause(1000)
                ->select('#statusFilter', 'Open')
                ->pause(2000)
                ->assertSeeIn('#tickets-table-body', 'Open')
                ->assertDontSeeIn('#tickets-table-body', 'Closed');
        });
    }

    public function test_admin_can_edit_ticket()
    {
        $this->browse(function (Browser $browser) {
            $admin = User::where('role', 'admin')->first();
            $browser->visit('/admin/tickets')
                ->pause(2000)
                ->clickLink('Edit') // First ticket's Edit button
                ->pause(2000)
                ->assertPathBeginsWith('/admin/tickets/')
                ->assertSee('Edit Ticket') // Adjust as needed
                ->type('title', 'Updated Ticket Title')
                ->press('Update Ticket') // Or whatever your submit button says
                ->pause(2000)
                ->assertDialogOpened('Ticket updated successfully!')
                ->acceptDialog()
                ->assertSee('Updated Ticket Title');
        });
    }

    public function test_admin_can_delete_ticket()
    // $browser->click("#userTableBody tr[data-id='{$userIdToDelete}'] button.btn-danger");
    {
        $this->browse(function (Browser $browser) {
            $admin = User::where('role', 'admin')->first();

            $browser->visit('/admin/tickets')
                ->pause(2000)
                ->click('.btn-delete') // Assumes first Delete button
                ->pause(2000)
                ->assertDialogOpened('Ticket deleted')
                ->acceptDialog()
                ->pause(2000)
                ->assertDontSee('Updated Ticket Title');
        });
    }

    public function test_admin_can_create_ticket_for_client()
    {
        $this->browse(function (Browser $browser) {
            $admin = User::where('role', 'admin')->first();
            $targetUser = User::where('email', 'vaibhavapriyand@gmail.com')->first();

            $browser->visit('/admin/tickets/create')
                ->pause(2000) // Wait for form JS
                ->assertSee('New Ticket')

                // Select "Another User"
                ->select('#user-toggle', 'other')
                ->pause(1000)

                // Type and select the user
                ->type('#requester_search', $targetUser->email)
                ->pause(1500)

                // Wait for the requester_id select to become visible and select value
                ->waitFor('#requester_id:not(.d-none)', 3)
                ->select('#requester_id', (string) $targetUser->id)
                // Fill in the rest of the form
                ->type('#title', 'Test Ticket for Vaibhav')
                ->select('#priority', 'high')
                ->type('#department', 'Support')
                ->type('#description', 'This is a test ticket created by admin for another user.')

                ->press('#submit-button')
                ->pause(10000) // Wait for submission and redirect

                ->assertDialogOpened('Ticket created successfully!')
                ->acceptDialog();

        });
    }
}