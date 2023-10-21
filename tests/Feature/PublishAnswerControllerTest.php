<?php

namespace Feature;

use Tests\TestCase;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Response;
use Tests\Concerns\WithUsers;
use App\States\Answer\Published;
use Illuminate\Support\Facades\Notification;
use App\Notifications\QuestionAnswerPublished;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PublishAnswerControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithUsers;

    /** @test */
    public function it_can_publish_answer_if_authenticated_as_admin_user(): void
    {
        Notification::fake();

        $answer = Answer::factory()->create(['state' => 'reviewed']);

        $this->actingAs($this->adminUser)
            ->patchJson(route('answers.publish', $answer))
            ->assertSuccessful();

        $this->assertDatabaseHas('answers', ['id' => $answer->id, 'state' => 'published']);
    }

    /** @test */
    public function it_sends_email_to_question_author_on_publish(): void
    {
        Notification::fake();

        $question = Question::factory()->create();
        $answer = Answer::factory()->create([
            'question_id' => $question->id,
            'state' => 'reviewed',
        ]);

        $answer->state->transitionTo(Published::class);
        $answer->save();

        Notification::assertSentTo(
            [new \Illuminate\Notifications\AnonymousNotifiable],
            QuestionAnswerPublished::class,
            function ($notification, $channels, $notifiable) use ($question) {
                return $notifiable->routes['mail'] === $question->email;
            }
        );
    }

    /** @test */
    public function it_can_not_publish_an_unreviewed_answer(): void
    {
        $answer = Answer::factory()->create(['state' => 'ready_for_review']);

        $this
            ->actingAs($this->adminUser)
            ->patchJson(route('answers.publish', $answer))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertDatabaseHas('answers', ['id' => $answer->id, 'state' => 'ready_for_review']);
    }

    /** @test */
    public function it_can_not_publish_answer_if_not_authenticated_as_admin_user(): void
    {
        $answer = Answer::factory()->create(['state' => 'reviewed']);

        // Without being authenticated at all
        $this->patchJson(route('answers.publish', $answer))
            ->assertUnauthorized();

        // Moderator can't publish either
        $this->actingAs($this->moderatorUser)
            ->patchJson(route('answers.publish', $answer))
            ->assertForbidden();

        $this->assertDatabaseHas('answers', ['id' => $answer->id, 'state' => 'reviewed']);
    }
}
