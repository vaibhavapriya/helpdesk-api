<?php

namespace Tests\Browser\Admin;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ConfigTest extends DuskTestCase
{

    public function test_admin_can_view_and_set_mail_config()
    {
        $this->browse(function (Browser $browser) {
            $admin = User::where('role', 'admin')->first();

            $this->loginAsUser($browser, $admin);

            $browser->visit('/admin/mailconfig')
                ->pause(2000)
                ->assertSee('Email Configuration')
                ->assertVisible('#emailTableBody')
                ->with('#emailTableBody tr:first-child', function ($row) {
                    $row->assertPresent('input[type="radio"].set-active-email');
                    $row->script("document.querySelector('label.custom-control-label').scrollIntoView()");
                    $row->script("document.querySelector('label.custom-control-label').click()");
                })
                ->pause(3000)
                ->assertSeeIn('#success', 'Email deleted successfully.');
        });
    }

    public function test_admin_can_create_and_delete_mail_config()
    {
        $this->browse(function (Browser $browser) {
            $testEmail = 'testdemo@example.com';
            $browser->visit('/admin/mailconfig')
                ->pause(1000)
                ->press('Add Email')
                ->pause(1000)
                ->whenAvailable('#addEmailModal', function (Browser $modal) use ($testEmail) {
                    $modal->type('email', $testEmail)
                        ->type('name', 'Dusk Test Sender')
                        ->type('passcode', 'test-password')
                        ->press('Save');
                })
                ->pause(2000)
                ->assertSeeIn('#success', 'Email configuration added');
        });
    }

    public function test_admin_can_delete_mail_config()
    {
        $this->browse(function (Browser $browser) {
            $testEmail = 'testdemo@example.com';

            $browser->visit('/admin/mailconfig')
                ->waitFor('.table-bordered tbody tr td', 5)
                ->assertSeeIn('table.table-bordered tbody', $testEmail)
                ->waitForText($testEmail, 5);
            $browser->script("Array.from(document.querySelectorAll('tbody tr')).forEach(row => {
                    if (row.innerText.includes('{$testEmail}')) {
                        const btn = row.querySelector('button.delete-email');
                        if (btn) btn.click();
                    }
                });");
            $browser->pause(5000)
                ->assertSeeIn('#success', 'Email deleted successfully.');
        });
    }


    // public function test_admin_can_view_and_set_queue_config()
    // {
    //     $this->browse(function (Browser $browser) {
    //     });
    // }

    // public function test_admin_can_view_and_set_cache_config()
    // {
    //     $this->browse(function (Browser $browser) {
    //     });
    // }

    // public function test_admin_can_view_errorlogs()
    // {
    //     $this->browse(function (Browser $browser) {
    //     });
    // }
}