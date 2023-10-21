<?php

namespace App\States\Answer\Transitions;

use App\Models\Answer;
use Spatie\ModelStates\Transition;
use Illuminate\Support\Facades\Notification;
use App\Notifications\QuestionAnswerPublished;

class ReviewedToPublished extends Transition
{
    public function __construct(public Answer $answer)
    {
    }

    public function handle(): Answer
    {
        $this->answer->state = 'published';

        Notification::route('mail', $this->answer->question->email)
            ->notify(new QuestionAnswerPublished($this->answer->question));

        return $this->answer;
    }
}
