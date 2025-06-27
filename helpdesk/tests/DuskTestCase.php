<?php

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Support\Collection;
use Laravel\Dusk\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\BeforeClass;

use Laravel\Dusk\Browser;
use App\Models\User;


abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Prepare for Dusk test execution.
     */
    #[BeforeClass]
    public static function prepare(): void
    {
        if (! static::runningInSail()) {
            static::startChromeDriver(['--port=9515']);
        }
    }

    /**
     * Create the RemoteWebDriver instance.
     */
    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments(collect([
            $this->shouldStartMaximized() ? '--start-maximized' : '--window-size=1920,1080',
            '--disable-search-engine-choice-screen',
            '--disable-smooth-scrolling',
        ])->unless($this->hasHeadlessDisabled(), function (Collection $items) {
            return $items->merge([
                '--disable-gpu',
                '--headless=new',
            ]);
        })->all());

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? env('DUSK_DRIVER_URL') ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }

    protected function loginAsUser(Browser $browser, User $user = null)
    {
        $user = $user ?? User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $browser->visit('/login')
                ->type('email', $user->email)
                ->type('password', 'password')
                ->press('Login')
                ->pause(1000)
                ->assertPathIs('/');
        
        return $user;
    }

    protected function injectTestMarker(Browser $browser, string $testName)
    {
        $escapedName = addslashes($testName);

        $browser->script("
            const existing = document.getElementById('CATION');
            if (!existing) {
                const marker = document.createElement('div');
                marker.id = 'CATION';
                marker.textContent = '$escapedName';
                Object.assign(marker.style, {
                    position: 'fixed',
                    bottom: '10px',
                    right: '10px',
                    background: 'linear-gradient(135deg, #007BFF, #00C851)',
                    color: '#fff',
                    padding: '8px 16px',
                    fontSize: '14px',
                    fontFamily: 'monospace',
                    borderRadius: '6px',
                    boxShadow: '0 2px 10px rgba(0,0,0,0.3)',
                    zIndex: 9999,
                    opacity: 0.9
                });
                document.body.appendChild(marker);
            }
        ");
    }


}
