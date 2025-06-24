<?php

namespace Tests\Feature;

use Tests\TestCase;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Profile;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_get_paginated_profiles_with_filters()
    {
        Passport::actingAs(User::factory()->create(['role' => 'admin']));

        $user1 = User::factory()->create(['role' => 'client']);
        $user2 = User::factory()->create(['role' => 'admin']);

        Profile::factory()->create([
            'firstname' => 'Alice',
            'lastname' => 'Smith',
            'user_id' => $user1->id,
        ]);

        Profile::factory()->create([
            'firstname' => 'Bob',
            'lastname' => 'Brown',
            'user_id' => $user2->id,
        ]);

        $response = $this->getJson('/api/admin/profiles?query=Alice&role=client');

        $response->assertStatus(200)
                 ->assertJsonStructure(['success', 'data', 'meta'])
                 ->assertJsonFragment(['firstname' => 'Alice'])
                 ->assertJsonMissing(['firstname' => 'Bob']);
    }

    /** @test */
    public function admin_can_store_new_profile()
    {
        Passport::actingAs(User::factory()->create(['role' => 'admin']));

        $response = $this->postJson('/api/admin/profiles/post', [
            'firstname' => 'Jane',
            'lastname' => 'Doe',
            'email' => 'jane@example.com',
            'phone' => '1234567890',
            'role' => 'client',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('profiles', ['firstname' => 'Jane']);
        $this->assertDatabaseHas('users', ['email' => 'jane@example.com']);
    }

    /** @test */
    public function user_can_view_own_profile()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->for($user)->create();

        Passport::actingAs($user);

        $response = $this->getJson("/api/profile/{$user->id}");

        $response->assertOk()
                 ->assertJsonStructure(['success', 'data' => ['id', 'firstname', 'lastname']]);
    }

    /** @test */
    public function user_can_update_profile()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->for($user)->create();

        Passport::actingAs($user);

        $response = $this->putJson("/api/profile/{$user->id}/update", [
            'firstname' => 'Updated',
            'lastname' => 'Name',
            'email' => $user->email,
            'phone' => '555-9999',
        ]);

        $response->assertOk()
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('profiles', [
            'firstname' => 'Updated',
            'lastname' => 'Name',
        ]);
    }

    /** @test */
    public function admin_can_delete_user_and_profile()
    {
        Passport::actingAs(User::factory()->create(['role' => 'admin']));

        $user = User::factory()->create();
        Profile::factory()->for($user)->create();

        $response = $this->deleteJson("/api/admin/profiles/delete/{$user->id}");

        $response->assertOk()
                 ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('profiles', ['user_id' => $user->id]);
    }

    /** @test */
    public function admin_can_get_user_id_and_email_list()
    {
        Passport::actingAs(User::factory()->create(['role' => 'admin']));

        User::factory()->count(3)->create();

        $response = $this->getJson('/api/admin/useridemail');

        $response->assertOk()
                 ->assertJsonStructure([
                     'success',
                     'data' => [['id', 'email']]
                 ]);
    }
}
