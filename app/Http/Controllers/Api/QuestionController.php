<?php

namespace App\Http\Controllers\Api;

use App\Models\Tag;
use App\Models\Question;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\QuestionRequest;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = Question::with('tags')->paginate(10);

        return response()->json($questions);
    }

    public function show(Question $question)
    {
        $question->load('tags', 'answers');

        return response()->json($question);
    }

    public function store(QuestionRequest $request)
    {
        $question = Question::create($request->validated());

        return response()->json($question, Response::HTTP_CREATED);
    }

    public function update(QuestionRequest $request, Question $question)
    {
        $question->fill($request->validated());
        $question->category_id = $request->get('category_id');
        $question->save();

        if ($request->has('tags')) {
            $tagIds = [];

            foreach ($request->tags as $tag) {
                if (is_numeric($tag)) {
                    $tagIds[] = $tag;
                } else {
                    $newTag = Tag::firstOrCreate(['name' => $tag]);
                    $tagIds[] = $newTag->id;
                }
            }

            $question->tags()->sync($tagIds);
        }

        return response()->json($question, Response::HTTP_OK);
    }
}
