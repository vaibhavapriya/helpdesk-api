<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthProfileTest extends TestCase
{
    use RefreshDatabase;

    // #[Test]
    // public function user_can_update_password_with_correct_old_password()
    // {
    //     // Create user with known password
    //     $user = User::factory()->create([
    //         'password' => Hash::make('oldpassword123'),
    //     ]);

    //     // Act as the user
    //     $this->actingAs($user);

    //     // Send update password request
    //     $response = $this->json('PUT', route('api.auth.updatePassword', ['id' => $user->id]), [
    //         'old_password' => 'oldpassword123',
    //         'new_password' => 'newpassword456',
    //         'new_password_confirmation' => 'newpassword456',
    //     ]);

    //     // Assert success true in response
    //     $response->assertStatus(200)
    //              ->assertJson([
    //                  'success' => true,
    //                  'message' => 'Password updated successfully.',
    //              ]);

    //     // Confirm password changed in DB
    //     $user->refresh();
    //     $this->assertTrue(Hash::check('newpassword456', $user->password));
    // }

    // #[Test]
    // public function update_password_fails_with_invalid_old_password()
    // {
    //     $user = User::factory()->create([
    //         'password' => Hash::make('correctoldpassword'),
    //     ]);

    //     $this->actingAs($user);

    //     $response = $this->json('PUT', route('api.auth.updatePassword', ['id' => $user->id]), [
    //         'old_password' => 'wrongoldpassword',
    //         'new_password' => 'newpassword456',
    //         'new_password_confirmation' => 'newpassword456',
    //     ]);

    //     $response->assertStatus(422)
    //              ->assertJson([
    //                  'success' => false,
    //                  'message' => 'Old password does not match.',
    //              ]);

    //     // Password should NOT change
    //     $user->refresh();
    //     $this->assertTrue(Hash::check('correctoldpassword', $user->password));
    // }
}

