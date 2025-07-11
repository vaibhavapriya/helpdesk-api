<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;
use App\Models\User;
use App\Models\MailConfig;

class ConfigTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Acting as admin for all requests
        $admin = User::factory()->create(['role' => 'admin']);
        Passport::actingAs($admin);
    }

    #[Test]
    public function it_can_list_mail_configurations()
    {
        MailConfig::factory()->count(3)->create();

        $response = $this->getJson('/api/admin/mails');

        $response->assertOk()
                 ->assertJsonStructure([
                     'success',
                     'data' => [['id', 'mail_from_name', 'mail_from_address']]
                 ]);
    }

    #[Test]
    public function it_can_store_mail_configuration()
    {
        $payload = [
            'email' => 'test@example.com',
            'name' => 'Test Sender',
            'passcode' => 'secure-passcode',
        ];

        $response = $this->postJson('/api/admin/mails/post', $payload);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('mailconfigs', [
            'mail_from_name' => 'Test Sender',
            'mail_from_address' => 'test@example.com',
        ]);
    }

    #[Test]
    public function it_can_update_mail_configuration_active_status()
    {
        $m1 = MailConfig::factory()->create(['active' => true]);
        $m2 = MailConfig::factory()->create(['active' => false]);

        $response = $this->patchJson("/api/admin/mails/{$m2->id}");

        $response->assertOk()
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('mailconfigs', ['id' => $m1->id, 'active' => false]);
        $this->assertDatabaseHas('mailconfigs', ['id' => $m2->id, 'active' => true]);
    }

    #[Test]
    public function it_can_delete_mail_configuration()
    {
        $mail = MailConfig::factory()->create();

        $response = $this->deleteJson("/api/admin/mails/delete/{$mail->id}");

        $response->assertOk()
                 ->assertJson(['status' => 'success']);

        $this->assertDatabaseMissing('mailconfigs', ['id' => $mail->id]);
    }

    #[Test]
    public function it_can_get_queue_driver()
    {
        $getResponse = $this->getJson('/api/admin/queue-driver');
        $getResponse->assertOk()->assertJsonStructure(['driver']);

    }

    #[Test]
    public function it_can_set_queue_driver()
    {
        $getResponse = $this->getJson('/api/admin/queue-driver');
        $getResponse->assertOk()->assertJsonStructure(['driver']);

        $setResponse = $this->postJson('/api/admin/queue-driver', ['driver' => 'sync']);
        $setResponse->assertOk()->assertJson(['driver' => 'sync']);
    }

    #[Test]
    public function it_can_get_cache_driver()
    {
        $getResponse = $this->getJson('/api/admin/cache-driver');
        $getResponse->assertOk()->assertJsonStructure(['driver']);
    }

    #[Test]
    public function it_can_set_cache_driver()
    {
        $getResponse = $this->getJson('/api/admin/cache-driver');
        $getResponse->assertOk()->assertJsonStructure(['driver']);

        $setResponse = $this->postJson('/api/admin/cache-driver', ['driver' => 'file']);
        $setResponse->assertOk()->assertJson(['driver' => 'file']);
    }
}
