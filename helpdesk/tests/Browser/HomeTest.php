<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\Pages\HomePage;
class HomeTest extends DuskTestCase
{
    public function test_home(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/');
            $this->injectTestMarker($browser, __FUNCTION__);
            $browser->pause(5000)
                ->waitFor('#home-cards-container', 5) // wait for container
                ->assertPresent('#link-submit-ticket') // check for navbar link(header)
                ->assertPresent('#link-knowledgebase')
                ->assertVisible('#home-cards-container .card h5')//check the tag exists
                ->assertSee('Register') // fallback or real translation
                ->assertSee('Submit Ticket') // fallback: text check
                ->assertSee('Knowledgebase') ;// might be translated
                // ->screenshot('home-page');
        });
    }
    // public function testBasicExample(): void
    // {
    //     $this->browse(function (Browser $browser) {
    //         $browser->visit(new HomePage)
    //                 ->waitFor('@homeCardsContainer', 5)        // wait for container to appear
    //                 ->assertPresent('@linkSubmitTicket')        // check navbar or card link
    //                 ->assertPresent('@linkKnowledgebase')       // check knowledgebase link presence
    //                 ->assertVisible('@cardTitles')               // check card <h5> elements exist
    //                 ->assertSee('Register')                       // check visible text (case-sensitive)
    //                 ->assertSee('Submit Ticket')
    //                 ->assertSee('Knowledgebase')
    //                 ->screenshot('home-page');
    //     });
    // }
}
