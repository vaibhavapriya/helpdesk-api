<?php

namespace Tests\Browser\Admin;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ProfileTest extends DuskTestCase
{
    public function test_admin_can_view_users_with_filters()
    {
        $this->browse(function (Browser $browser) {
            $admin = User::where('role', 'admin')->first(); // Ensure an admin exists

            $this->loginAsUser($browser, $admin);

            $browser->visit('/admin/profiles') // adjust route if different
                    ->pause(2000)
                    ->assertSee('Users')
                    ->assertPresent('#searchInput')
                    ->type('#searchInput', '9') // assuming a user with "John" exists
                    ->pause(1000)
                    ->assertSee('weissnat.elmira@example.net') // adjust name to match a seeded user
                    ->select('#roleFilter', 'client')
                    ->pause(2000)
                    ->assertSeeIn('#userTableBody','client')
                    ->assertDontSeeIn('#userTableBody', 'admin');
        });
    }
    public function test_admin_can_click_user_email_and_redirect()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/profiles')
                    ->pause(2000) // Wait for JS to render users
                    ->assertSee('Users') // Page heading
                    ->click('.clickable-name') // Click on user email cell
                    ->pause(1000)
                    ->assertPathBeginsWith('/admin/profile/') // Confirm redirect
                    ->assertSee('Edit') // Check content of profile page
                    ->assertSee('Email'); // Or other field from the profile
        });
    }
    public function test_admin_can_view_and_edit_user_profile()
    {
        $this->browse(function (Browser $browser) {
            $admin = User::where('role', 'admin')->first(); 
            $browser->visit("/admin/profile/{$admin->id}")
                ->pause(2000) // wait for JS to load
                ->assertSee('User Profile')
                ->assertSee($admin->email)
                ->click('#editBtn')
                ->pause(500)
                ->type('#lastname', 'UpdatedLast')
                ->press('#saveBtn')
                ->pause(2000)
                ->assertDialogOpened('Profile updated successfully!')
                ->acceptDialog()
                ->assertSee('UpdatedLast');
        });
    }
    public function test_admin_can_view_and_edit_his_profile()
    {
        $this->browse(function (Browser $browser) { 
            $browser->visit("/admin/profile")
                ->pause(2000) // wait for JS to load
                ->assertSee('User Profile')
                ->click('#editBtn')
                ->pause(500)
                ->type('#lastname','Priya')
                ->press('#saveBtn')
                ->pause(2000)
                ->assertDialogOpened('Profile updated successfully!')
                ->acceptDialog()
                ->assertSee('Priya');
        });
    }
    public function test_admin_can_view_and_edit_his_password()
    {
        $this->browse(function (Browser $browser) {
            $admin = User::where('role', 'admin')->first();

            $admin->password = bcrypt('oldpassword123');
            $admin->save();

            $browser->visit('/admin/profile')
            ->assertSee('User Profile')
            ->type('#old_Password', 'oldpassword123')
            ->type('input[name="new_password"]', 'password')
            ->type('input[name="new_password_confirmation"]', 'password')
            ->press('#cp')
            ->pause(2000)
            ->assertDialogOpened('Password changed successfully!')
            ->acceptDialog();
        });
    }
}