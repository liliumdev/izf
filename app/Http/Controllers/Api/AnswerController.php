<?php

namespace App\Http\Controllers\Api;

use App\Models\Answer;
use App\Models\Question;
use App\Http\Controllers\Controller;
use App\Http\Requests\AnswerRequest;

class AnswerController extends Controller
{
    public function index(Question $question)
    {
        $answers = $question->answers()->paginate(10);

        return response()->json($answers);
    }

    public function update(AnswerRequest $request, Question $question, Answer $answer)
    {
        if ($request->has('state') && $request->input('state') === 'published') {
            $this->authorize('publish', $answer);
        }

        $answer->update($request->validated());

        return response()->json($answer);
    }

    public function store(Question $question, AnswerRequest $request)
    {
        $answer = new Answer($request->validated());
        $answer->drafted_by = auth()->id();
        $answer->state = 'draft';

        $question->answers()->save($answer);

        return response()->json($answer, 201);
    }
}
