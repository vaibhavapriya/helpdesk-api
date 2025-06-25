<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Ticket;
use App\Models\Image;
use App\Models\Reply;
use Tests\TestCase;


class TicketTest extends TestCase
{
    use RefreshDatabase;

    /*ticket controller:: index */
    public function test_client_can_get_paginated_tickets()
    {
        // Create a user and authenticate with Passport
        $user = User::factory()->create();
        Passport::actingAs($user);

        // Create some tickets for the user
        Ticket::factory()->count(20)->create([
            'requester_id' => $user->id
        ]);

        // Act: Hit the API route
        $response = $this->getJson('/api/mytickets');

        // Assert: Check status and structure
        $response->assertStatus(200)
                 ->assertJsonStructure([
                    'success',
                    'data',
                    'meta' => [
                        'current_page',
                        'next_page_url',
                        'per_page',
                        'prev_page_url',
                    ]
                 ]);

        // Optional: Check data count
        $this->assertCount(15, $response->json('data')); // simplePaginate(15)
    }

    /*ticket controller:: adminIndex */
    public function test_index_admin_filters_by_query_and_status()
    {
        $admin = User::factory()->create(['role' => 'admin']);
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

        $response->assertOk()
                ->assertJsonCount(1, 'data')
                ->assertJsonFragment(['title' => 'Network Issue']);
    }

    /*ticket controller:: store */
    public function test_client_can_store_ticket_with_attachment()
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'client']);
        Passport::actingAs($user);

        $file = UploadedFile::fake()->image('doc1.png');
        $expectedFilename = time() . '_doc1.png';

        $response = $this->postJson('/api/mytickets', [
            'title' => 'New Problem',
            'description' => 'Details here',
            'priority' => 'high',
            'department' => 'IT',
            'attachment' => $file,
        ]);

        $response->assertStatus(201)
                ->assertJson(['success' => true]);

        $this->assertDatabaseHas('tickets', [
            'title' => 'New Problem',
            'requester_id' => $user->id,
        ]);

        // Assert file was saved
        Storage::disk('public')->assertExists('uploads/' . $expectedFilename);
    }

    public function test_client_cannot_store_incomplete_form_ticket()
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'client']);
        Passport::actingAs($user);

        $response = $this->postJson('/api/mytickets', [
            'title' => 'New Problem',
            'description' => 'Details here',
        ]);

        $response->assertStatus(422) // Validation failed
                ->assertJsonValidationErrors(['priority', 'department']);
    }

    public function test_admin_can_store_ticket_for_any_user()
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'admin']);
        $client = User::factory()->create(['role' => 'client']);
        Passport::actingAs($admin);

        // $file = UploadedFile::fake()->create('file.jpg', 100, 100);
        $file = UploadedFile::fake()->image('file.jpg', 100, 100);

        $expectedFilename = time() . '_file.jpg'; // simulate controller naming

        $response = $this->postJson('/api/admin/tickets', [
            'title' => 'Admin Issue',
            'description' => 'desc',
            'priority' => 'medium',
            'department' => 'Support',
            'requester_id' => $client->id,
            'attachment' => $file,
        ]);

        $response->assertStatus(201)
                ->assertJson(['success' => true]);

        // Assert ticket in DB
        $this->assertDatabaseHas('tickets', [
            'title' => 'Admin Issue',
            'requester_id' => $client->id,
        ]);

        // Fetch the created ticket
        $ticket = \App\Models\Ticket::where('title', 'Admin Issue')->first();

        // Assert image record exists
        $this->assertDatabaseHas('images', [
            'name' => $expectedFilename,
            'link' => 'uploads/' . $expectedFilename,
            'filetype' => 'jpg',
            'imageable_id' => $ticket->id,
            'imageable_type' => \App\Models\Ticket::class,
        ]);

        // Assert file was saved
        Storage::disk('public')->assertExists('uploads/' . $expectedFilename);
    }

    /*ticket controller:: show */
    public function test_show_ticket_with_relations()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $ticket = Ticket::factory()->for($user, 'requester')->create();
        Image::factory()->create(['imageable_id' => $ticket->id, 'imageable_type' => Ticket::class]);
        Reply::factory()->count(2)->for($ticket)->create();

        $response = $this->getJson("/api/tickets/{$ticket->id}");

        $response->assertOk()
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'title',
                        'replies',
                        // you can add more keys you expect here like 'image'
                    ]
                ])
                ->assertJsonCount(2, 'data.replies');
    }

    public function test_client_cannot_view_others_ticket()
    {
        $user = User::factory()->create(['role' => 'client']);
        Passport::actingAs($user);

        $otherUser = User::factory()->create();
        $ticket = Ticket::factory()->for($otherUser, 'requester')->create();

        $response = $this->getJson("/api/tickets/{$ticket->id}");

        $response->assertForbidden(); // 403
    }

    public function test_admin_can_view_any_ticket()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Passport::actingAs($admin);

        $otherUser = User::factory()->create();
        $ticket = Ticket::factory()->for($otherUser, 'requester')->create();

        $response = $this->getJson("/api/tickets/{$ticket->id}");

        $response->assertOk()
                ->assertJsonStructure(['success', 'data' => ['id', 'title', 'replies']]);
    }

    /*ticket controller:: update */
    public function test_update_ticket_and_replace_attachment()
    {
        Storage::fake('public'); // <-- fake storage to intercept file operations

        $user = User::factory()->create();
        Passport::actingAs($user);

        // Create ticket with old image saved on fake disk
        $ticket = Ticket::factory()->for($user, 'requester')->create();
        $old = UploadedFile::fake()->image('old.png');
        $oldPath = $old->store('uploads', 'public');

        $ticket->image()->create([
            'name' => 'old.png',
            'link' => $oldPath,
            'filetype' => 'png',
        ]);

        Storage::disk('public')->assertExists($oldPath);

        // New fake image to replace old
        $new = UploadedFile::fake()->image('new.png');

        $response = $this->putJson("/api/tickets/{$ticket->id}/update", [
            'title' => 'Updated',
            'description' => 'Updated desc',
            'priority' => 'low',
            'department' => 'HR',
            'status' => 'open',
            'attachment' => $new,
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        // Refresh the ticket to get latest data and relationships
        $ticket->refresh();

        // Get the updated image related to the ticket
        $image = $ticket->image;

        // Old file should be deleted
        Storage::disk('public')->assertMissing($oldPath);

        // New file should exist
        //Storage::disk('public')->assertExists('uploads/' . $new->hashName());
        Storage::disk('public')->assertExists($image->link);
    }

    /*ticket controller:: delete */
    public function test_destroy_ticket_also_deletes_image()
    {
        Storage::fake('public'); // <-- fake storage to intercept file operations

        $user = User::factory()->create();
        Passport::actingAs($user);

        $ticket = Ticket::factory()->for($user, 'requester')->create();

        $file = UploadedFile::fake()->image('del.png');
        $filePath = $file->store('uploads', 'public');

        $ticket->image()->create([
            'name' => 'del.png',
            'link' => $filePath,
            'filetype' => 'png',
        ]);

        Storage::disk('public')->assertExists($filePath);

        $response = $this->deleteJson("/api/tickets/{$ticket->id}/delete");
        $response->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseMissing('tickets', ['id' => $ticket->id]);

        // Image file must be deleted from storage
        Storage::disk('public')->assertMissing($filePath);
    }

    public function test_user_can_add_reply_to_ticket()
    {
        // Arrange
        $user = User::factory()->create();
        $ticket = Ticket::factory()->for($user, 'requester')->create();

        Passport::actingAs($user);

        $replyContent = 'This is a reply from the user.';

        // Act
        $response = $this->postJson("/api/tickets/{$ticket->id}/comment", [
            'reply' => $replyContent,
        ]);

        // Assert
        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Reply added successfully',
                ]);

        $this->assertDatabaseHas('replies', [
            'ticket_id' => $ticket->id,
            'replier_id' => $user->id,
            'reply' => $replyContent,
        ]);
    }

    public function test_reply_content_is_required()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->for($user, 'requester')->create();

        Passport::actingAs($user);

        $response = $this->postJson("/api/tickets/{$ticket->id}/comment", [
            // 'reply' is missing
        ]);

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'Reply content is required',
                ]);
    }

}
