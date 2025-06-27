<?php

namespace Tests\Browser\Client;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class ProfileTest extends DuskTestCase
{
    public function test_client_can_view_profile()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first() ;
            $user = $this->loginAsUser($browser, $user);

            $browser->visit('/myProfile');
            $this->injectTestMarker($browser, __FUNCTION__);
            $browser->waitForText('User Profile', 5)
                    ->pause(2000)
                    ->assertSee('User Profile')
                    ->assertVisible('#firstname_display')
                    ->assertVisible('#lastname_display')
                    ->assertVisible('#email_display')
                    ->assertVisible('#phone_display')
                    ->assertVisible('#avatar_display');
        });
    }
    public function test_client_can_edit_and_save_profile()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/myProfile');
            $this->injectTestMarker($browser, __FUNCTION__);
            $browser->pause(2000)
                    ->waitForText('User Profile', 5)
                    ->waitFor('#editBtn', 5)
                    ->scrollTo('#editBtn')
                    ->press('#editBtn')
                    ->pause(5000)

                    // Fill in the form fields
                    ->type('#firstname', 'NewFirst')
                    ->type('#lastname', 'NewLast')
                    ->type('#phone', '1234567890')
                    ->type('#email', 'autumn00@example.org')

                    // Submit the form
                    ->scrollTo('#saveBtn')
                    ->press('#saveBtn')
                    ->pause(5000)

                    ->assertDialogOpened('Profile updated successfully!') // if your JS uses alert('Deleted')
                    ->acceptDialog()

                    // Confirm values are updated
                    ->pause(5000)
                    ->assertSee('NewFirst')
                    ->assertSee('NewLast')
                    ->assertSee('1234567890')
                    ->assertSee('autumn00@example.org');
        });
    }
}