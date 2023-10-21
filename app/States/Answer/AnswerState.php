<?php

namespace App\States\Answer;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;
use App\States\Answer\Transitions\ReviewedToPublished;

abstract class AnswerState extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Draft::class)
            ->allowTransition(Draft::class, ReadyForReview::class)
            ->allowTransition(ReadyForReview::class, Reviewed::class)
            ->allowTransition(Reviewed::class, Published::class, ReviewedToPublished::class);
    }
}
