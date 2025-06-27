<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class LoginTest extends DuskTestCase
{

    /**
     * Test login fails with invalid credentials.
     */
    public function test_login_fails_with_invalid_credentials()
    {
        $this->browse(function (Browser $browser) {
            
            $browser->visit('/login')  // or wherever it redirects
                ->type('email', 'dane41@example.com')
                ->type('password', 'wrongpassword')
                ->press('Login')
                ->pause(2000)
                ->assertDialogOpened('Invalid credentials.')
                ->acceptDialog();// your JS alert says "Login failed" on error

        });
    }

    public function test_user_can_login()
    {
        $this->browse(function (Browser $browser) {
            // Get any existing user or create one dynamically
            $user = User::first() ?? User::factory()->create([
                'password' => bcrypt('password'), // Make sure you know password
            ]);

            $browser->visit('/login');
            $this->injectTestMarker($browser, __FUNCTION__);
            $browser->pause(5000)
                ->type('email', $user->email)
                ->type('password', 'password')
                ->press('Login')
                ->pause(5000)
                ->assertPathIs('/')->pause(5000);
        });
    }
    /**
     * Test user can login successfully and redirect.
     */
    // public function test_user_cannot_login_if_already_login()
    // {
    //     $this->browse(function (Browser $browser) {
            
    //         $browser->visit('/login');
    //         $this->injectTestMarker($browser, __FUNCTION__);
    //         $browser->pause(5000)
    //             ->assertDialogOpened('You are logged user.')
    //             ->acceptDialog()->pause(5000);
    //             // ->type('email', 'dane41@example.com')         // replace with valid test user email
    //             // ->type('password', 'password')              // replace with valid test user password
    //             // ->check('remember')                          // if you want to test "remember me"
    //             // ->press('Login')
    //             // ->pause(5000)                               // wait for JS API call and redirect
    //             // ->assertPathIs('/');                         // check redirected path (home)
    //     });
    // }
        public function test_logout()
    {
        $this->browse(function (Browser $browser) {
            
            $browser->click('#userDropdown');   // open dropdown
            $this->injectTestMarker($browser, __FUNCTION__);
            $browser->pause(5000)
                ->click('button[type=submit]')  // submit logout form button
                ->pause(1000)
                ->assertPathIs('/login');  // or wherever it redirects

        });
    }

}
