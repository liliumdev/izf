<?php

namespace Feature;

use App\Models\Tag;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_create_tag(): void
    {
        $tagData = ['name' => 'Laravel'];
        $response = $this->postJson('/api/tags', $tagData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tags', $tagData);
    }

    /** @test */
    public function can_list_tags(): void
    {
        $tag = Tag::factory()->create();

        $response = $this->getJson('/api/tags');

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $tag->name]);
    }

    /** @test */
    public function can_delete_tag(): void
    {
        $tag = Tag::factory()->create();

        $response = $this->deleteJson("/api/tags/{$tag->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }
}
