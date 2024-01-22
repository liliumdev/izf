<?php

namespace App\Http\Controllers\Api;

use App\Models\Tag;
use App\Models\Question;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\QuestionRequest;
use App\Http\Requests\QuestionIndexRequest;

class QuestionController extends Controller
{
    public function index(QuestionIndexRequest $request)
    {
        $questions = Question::with('tags')
            ->when($request->has('state'), function ($query) use ($request): void {
                $query->whereHas('answers', function ($query) use ($request): void {
                    $query->where('state', $request->get('state'));
                });
            })
            ->paginate(10);

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
