<?php

namespace Feature;

use Tests\TestCase;
use App\Models\Answer;
use App\Models\Question;
use Tests\Concerns\WithUsers;
use App\States\Answer\Reviewed;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AnswerControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithUsers;

    /** @test */
    public function it_can_draft_answer_to_question(): void
    {
        $question = Question::factory()->create();
        $answerData = ['content' => 'You can start with the Laravel documentation.'];

        $this
            ->actingAs($this->moderatorUser)
            ->postJson(route('questions.answers.store', $question), $answerData)
            ->assertSuccessful();

        $this->assertDatabaseHas('answers', ['content' => 'You can start with the Laravel documentation.', 'state' => 'draft']);
    }

    /** @test */
    public function it_can_not_draft_answer_to_question_with_empty_content(): void
    {
        $question = Question::factory()->create();
        $answerData = ['content' => ''];

        $this
            ->actingAs($this->moderatorUser)
            ->postJson(route('questions.answers.store', $question), $answerData)
            ->assertJsonValidationErrors('content');
    }

    /** @test */
    public function it_can_update_answer_state(): void
    {
        $question = Question::factory()->create();
        $answer = Answer::factory()->create([
            'question_id' => $question->id,
            'state' => 'draft',
        ]);

        $this->actingAs($this->adminUser)
            ->putJson(route('questions.answers.update', [$question->id, $answer->id]), [
                'state' => 'reviewed',
                'reviewed_by' => $this->moderatorUser->id,
            ])
            ->assertSuccessful();

        $this->assertInstanceOf(Reviewed::class, $answer->fresh()->state);
        $this->assertEquals($this->moderatorUser->id, $answer->fresh()->reviewed_by);
    }

    /** @test */
    public function it_can_not_draft_answer_to_question_if_unauthenticated(): void
    {
        $question = Question::factory()->create();
        $answerData = ['content' => 'Test answer.'];

        $this->postJson(route('questions.answers.store', $question), $answerData)
            ->assertUnauthorized();
    }
}
