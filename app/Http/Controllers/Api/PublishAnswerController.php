<?php

namespace App\Http\Controllers\Api;

use App\Models\Answer;
use App\States\Answer\Published;
use App\Http\Controllers\Controller;

class PublishAnswerController extends Controller
{
    public function __invoke(Answer $answer)
    {
        $this->authorize('publish', $answer);

        if ($answer->state->canTransitionTo(Published::class) === false) {
            return response()->json(['message' => 'Answer cannot be published'], 422);
        }

        $answer->state->transitionTo(Published::class);

        $answer->save();

        return response()->json(['message' => 'Answer published successfully'], 200);
    }
}
