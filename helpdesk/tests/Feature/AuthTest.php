<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Queue;
use App\Jobs\SendForgotPasswordMail;
use App\Models\Ticket;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '1234567890',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => ['msg' => 'User registered successfully']
            ]);

        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
        $this->assertDatabaseHas('profiles', ['firstname' => 'John']);
    }

    #[Test]
    public function user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
                'email' => 'test@example.com',
                'password' => bcrypt('password123'),
            ]);

            $response = $this->postJson('/oauth/token', [
                'grant_type' => 'password',
                'client_id' => 99,
                'client_secret' => 'test-secret',
                'username' => 'test@example.com',
                'password' => 'password123',
                'scope' => '',
            ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['meta' => ['token', 'token_type']]);
    }

    #[Test]
    public function user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('correct-password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'jane@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Invalid credentials.']);
    }

    #[Test]
    public function user_cannot_update_password_with_wrong_old_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('oldpassword'),
        ]);

        $this->actingAs($user, 'api');

        $response = $this->putJson("/api/profile/{$user->id}/updatePassword", [
            'old_password' => 'wrongold',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Old password does not match.',
                 ]);
    }

    #[Test]
    public function user_can_update_password_with_correct_old_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('oldpassword'),
        ]);

        $this->actingAs($user, 'api');

        $response = $this->putJson("/api/profile/{$user->id}/updatePassword", [
            'old_password' => 'oldpassword',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response->assertOk()
                 ->assertJson([
                     'success' => true,
                     'message' => 'Password updated successfully.',
                 ]);

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    #[Test]
    public function forgot_password_sends_email()
    {
        Queue::fake(); // Prevent actual job dispatching

        $user = User::factory()->create();

        $response = $this->postJson('/api/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertStatus(200)
                ->assertJson(['message' => 'Password reset link sent.']);

        // Assert the job was dispatched
        Queue::assertPushed(SendForgotPasswordMail::class, function ($job) use ($user) {
            return $job->getUser()->is($user) && !empty($job->getToken());
        });

    }

    #[Test]
    public function forgot_password_fails_with_invalid_email()
    {
        $response = $this->postJson('/api/forgot-password', [
            'email' => 'notfound@example.com',
        ]);

        $response->assertStatus(422) // validation error
                ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function reset_password_with_valid_token()
    {
        $user = User::factory()->create();

        // Generate token for user
        $token = Password::createToken($user);

        $newPassword = 'newsecurepassword';

        $response = $this->postJson('/api/reset-password', [
            'email' => $user->email,
            'token' => $token,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $response->assertStatus(200)
                ->assertJson(['message' => 'Password reset successful.']);

        // Assert password was updated
        $this->assertTrue(Hash::check($newPassword, $user->fresh()->password));
    }

    #[Test]
    public function reset_password_fails_with_invalid_token()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/reset-password', [
            'email' => $user->email,
            'token' => 'invalid-token',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(400)
                ->assertJson(['error' => 'Invalid token or email.']);
    }

    #[Test]
    public function reset_password_validation_errors()
    {
        $response = $this->postJson('/api/reset-password', [
            // missing all fields
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email', 'token', 'password']);
    }

    #[Test]
    public function rolemiddleware_works()
    {
        $admin = User::factory()->create(['role' => 'client']);
        Passport::actingAs($admin);

        Ticket::factory()->create([
            'title' => 'Network Issue',
            'status' => 'open'
        ]);

        Ticket::factory()->create([
            'title' => 'Server Downtime',
            'status' => 'closed'
        ]);

        $response = $this->getJson('/api/admin/tickets?query=Network&status=open');

        $response->assertStatus(403)
                ->assertJsonFragment(['message' => 'Unauthorized.']);
    }

}

