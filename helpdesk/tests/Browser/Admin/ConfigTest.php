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

            $browser->visit('/admin/mailconfig');
            $this->injectTestMarker($browser, __FUNCTION__);
            $browser->pause(5000)
                ->assertSee('Email Configuration')
                ->assertVisible('#emailTableBody')
                ->with('#emailTableBody tr:first-child', function ($row) {
                    $row->assertPresent('input[type="radio"].set-active-email');
                    $row->script("document.querySelector('label.custom-control-label').scrollIntoView()");
                    $row->script("document.querySelector('label.custom-control-label').click()");
                })
                ->pause(3000)
                ->assertSeeIn('#success', 'Email marked as active.');
        });
    }

    public function test_admin_can_create_and_delete_mail_config()
    {
        $this->browse(function (Browser $browser) {
            $testEmail = 'testdemo@example.com';
            $browser->visit('/admin/mailconfig');
            $this->injectTestMarker($browser, __FUNCTION__);
            $browser->pause(5000)
                ->press('Add Email')
                ->pause(5000)
                ->whenAvailable('#addEmailModal', function (Browser $modal) use ($testEmail) {
                    $modal->type('email', $testEmail)
                        ->type('name', 'Dusk Test Sender')
                        ->type('passcode', 'test-password')
                        ->pause(5000)
                        ->press('Save');
                })
                ->pause(5000)
                ->assertSeeIn('#success', 'Email configuration added');
        });
    }

    public function test_admin_can_delete_mail_config()
    {
        $this->browse(function (Browser $browser) {
            $testEmail = 'testdemo@example.com';

            $browser->visit('/admin/mailconfig');
            $this->injectTestMarker($browser, __FUNCTION__);
            $browser->waitFor('.table-bordered tbody tr td', 5)
                ->pause(5000)
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

    public function test_admin_can_view_and_set_queue_config()
    {
        $this->browse(function (Browser $browser) {

            $browser->visit('/admin/qconfig'); 
            $this->injectTestMarker($browser, __FUNCTION__);
            $browser->pause(5000)
                    ->assertSee('Queue Driver Configuration');

            $browser->script("document.querySelector('label[for=\"queue_database\"]').scrollIntoView();");
            $browser->pause(500);
            $browser->script("document.querySelector('label[for=\"queue_database\"]').click();");

            // Wait and verify success message
            $browser->pause(5000)
                    ->assertVisible('#statusMessage')
                    ->assertSeeIn('#statusMessage', 'Queue driver updated to "database"');
        });
    }

    public function test_admin_can_view_and_set_cache_config()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/cconfig') ;
            $this->injectTestMarker($browser, __FUNCTION__);
            $browser->pause(5000)
                    ->assertSee('Cache Driver Configuration');

            $browser->script("document.querySelector('label[for=\"cache_redis\"]').scrollIntoView();");
            $browser->pause(5000);
            $browser->script("document.querySelector('label[for=\"cache_redis\"]').click();");

            $browser->pause(5000)
                    ->assertVisible('#statusMessage')
                    ->assertSeeIn('#statusMessage', 'Cache driver updated to "redis"');
        });
    }

    public function test_admin_can_view_error_logs_and_paginate()
    {
        $this->browse(function (Browser $browser) {

            $browser->visit('/admin/errorlogs')
                ->waitFor('#ticketTable', 10)
                ->assertVisible('#ticketTable')
                ->assertPresent('#tickets-table-body tr')
                ->assertVisible('#pagination');

            // Check if active "Next" link exists
            $nextLinkSelector = '//ul[@id="pagination"]//a[contains(text(), "Next")]';

            if ($browser->element($nextLinkSelector)) {
                $browser->waitFor($nextLinkSelector, 10)
                    ->click($nextLinkSelector)
                    ->pause(5000)
                    ->assertPresent('#tickets-table-body tr');
            } else {
                $browser->assertSeeIn('#pagination', 'Next'); // Next is disabled (span)
            }
        });
    }


}