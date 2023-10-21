<?php

namespace Feature;

use Tests\TestCase;
use App\Models\Category;
use Illuminate\Http\Response;
use Tests\Concerns\WithUsers;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithUsers;

    /** @test */
    public function it_can_store_a_category(): void
    {
        $this->actingAs($this->moderatorUser)
            ->postJson(route('categories.store'), ['name' => 'Tech'])
            ->assertSuccessful()
            ->assertJson(['name' => 'Tech']);

        $this->assertDatabaseHas('categories', ['name' => 'Tech']);
    }

    /** @test */
    public function it_can_update_a_category(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->moderatorUser)
            ->putJson(route('categories.update', $category->id), ['name' => 'UpdatedName'])
            ->assertSuccessful()
            ->assertJson(['name' => 'UpdatedName']);

        $this->assertDatabaseHas('categories', ['name' => 'UpdatedName']);
    }

    /** @test */
    public function it_can_list_categories(): void
    {
        Category::factory()->count(3)->create();

        $this->actingAs($this->moderatorUser)
            ->getJson(route('categories.index'))
            ->assertSuccessful()
            ->assertJsonCount(3);
    }

    /** @test */
    public function it_can_list_categories_with_their_children_recursively(): void
    {
        $parentCategory = Category::factory()->create();
        $childCategory = Category::factory()->create(['parent_id' => $parentCategory->id]);
        $grandchildCategory = Category::factory()->create(['parent_id' => $childCategory->id]);

        $response = $this->getJson(route('categories.index'))
            ->assertSuccessful();

        // Assert the parent category is present in the response
        $response->assertJsonPath('0.id', $parentCategory->id);

        // Assert the child category is present under the parent
        $response->assertJsonPath('0.children.0.id', $childCategory->id);

        // Assert the grandchild category is present under the child
        $response->assertJsonPath('0.children.0.children.0.id', $grandchildCategory->id);
    }

    /** @test */
    public function it_can_show_a_category(): void
    {
        $category = Category::factory()->create(['name' => 'ShowMe']);

        $this->actingAs($this->moderatorUser)
            ->getJson(route('categories.show', $category->id))
            ->assertSuccessful()
            ->assertJson(['name' => 'ShowMe']);
    }

    /** @test */
    public function it_can_delete_a_category(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->moderatorUser)
            ->deleteJson(route('categories.destroy', $category->id))
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
