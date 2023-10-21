<?php

namespace Feature;

use App\Models\Tag;
use Tests\TestCase;
use App\Models\Category;
use App\Models\Question;
use Illuminate\Http\Response;
use Tests\Concerns\WithUsers;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QuestionControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithUsers;

    /** @test */
    public function it_can_create_question(): void
    {
        $questionData = [
            'email' => 'tester@example.com',
            'title' => 'Kako uzeti abdest?',
            'content' => 'Tek sam prešao na islam. Kako se uzima abdest?',
            'is_public' => true,
        ];

        $this->postJson(route('questions.store'), $questionData)
            ->assertSuccessful();

        $this->assertDatabaseHas('questions', [
            'email' => 'tester@example.com',
            'title' => 'Kako uzeti abdest?',
        ]);
    }

    /** @test */
    public function it_cannot_create_invalid_questions(): void
    {
        $this->postJson(route('questions.store'), [])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['content', 'email']);
    }

    /** @test */
    public function it_can_show_question_with_tags_and_answers(): void
    {
        $question = Question::factory()->create();
        $tag = Tag::factory()->create();

        $question->tags()->attach($tag);

        $question->answers()->create([
            'content' => 'Ovo je odgovor na pitanje.',
            'user_id' => $this->moderatorUser->id,
        ]);

        $this->getJson(route('questions.show', $question))
            ->assertSuccessful()
            ->assertJsonFragment(['name' => $tag->name])
            ->assertJsonCount(1, 'answers')
            ->assertJsonPath('answers.0.content', 'Ovo je odgovor na pitanje.');
    }

    /** @test */
    public function it_can_update_a_question(): void
    {
        $question = Question::factory()->create();
        $category = Category::factory()->create();

        $updateData = array_merge($question->toArray(), [
            'content' => 'Updated content',
            'email' => 'updated@example.com',
            'category_id' => $category->id,
        ]);

        $this->actingAs($this->moderatorUser)
            ->putJson(route('questions.update', $question->id), $updateData)
            ->assertSuccessful()
            ->assertJson(['content' => 'Updated content', 'category_id' => $category->id]);

        $this->assertDatabaseHas('questions', $updateData);
    }

    /** @test */
    public function it_can_not_update_a_question_if_not_authenticated(): void
    {
        $question = Question::factory()->create();

        $updateData = array_merge($question->toArray(), [
            'content' => 'Updated content',
            'email' => 'updated@example.com',
        ]);

        $this->putJson(route('questions.update', $question->id), $updateData)
            ->assertUnauthorized();

        $this->assertDatabaseMissing('questions', $updateData);
    }

    /** @test */
    public function it_can_create_tags_while_updating_a_question(): void
    {
        $question = Question::factory()->create();
        $tag = Tag::factory()->create();

        $updateData = array_merge($question->toArray(), [
            'content' => 'Updated content with tags',
            'email' => 'tagged@example.com',
            'tags' => [$tag->id, 'NewTag'],
        ]);

        $this->actingAs($this->moderatorUser)
            ->putJson(route('questions.update', $question->id), $updateData)
            ->assertSuccessful();

        $this->assertDatabaseHas('tags', ['name' => 'NewTag']);
        $this->assertDatabaseHas('question_tag', ['tag_id' => $tag->id, 'question_id' => $question->id]);
    }

    /** @test */
    public function it_can_detach_tags_from_a_question(): void
    {
        $question = Question::factory()->create();

        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();

        $question->tags()->attach([$tag1->id, $tag2->id]);

        $updateData = array_merge($question->toArray(), [
            'content' => 'Updated content after detaching tags',
            'email' => 'detached@example.com',
            'tags' => [$tag1->id],
        ]);

        $this->actingAs($this->moderatorUser)
            ->putJson(route('questions.update', $question->id), $updateData)
            ->assertSuccessful();

        // tag1 should still be attached, but tag2 should be detached
        $this->assertDatabaseHas('question_tag', ['tag_id' => $tag1->id, 'question_id' => $question->id]);
        $this->assertDatabaseMissing('question_tag', ['tag_id' => $tag2->id, 'question_id' => $question->id]);

        // tag2 still exists in the database
        $this->assertDatabaseHas('tags', ['id' => $tag2->id]);
    }
}
