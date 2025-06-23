<?php

use Tests\TestCase;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_post()
    {
        $post = Post::factory()->create();
        $response = $this->get(route('posts.show', $post));
        $response->assertStatus(200);
    }

    public function test_can_edit_post()
    {
        $post = Post::factory()->create();
        $response = $this->get(route('posts.edit', $post));
        $response->assertStatus(200);
    }

    public function test_can_update_post()
    {
        $post = Post::factory()->create();
        $response = $this->put(route('posts.update', $post), [
            'title' => 'Updated Title',
            'body' => 'Updated body',
        ]);
        $response->assertRedirect(route('posts.show', $post));
        $this->assertDatabaseHas('posts', ['title' => 'Updated Title']);
    }

    public function test_can_delete_post()
    {
        $post = Post::factory()->create();
        $response = $this->delete(route('posts.destroy', $post));
        $response->assertRedirect(route('posts.index'));
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }
}
