<?php

namespace App\Http\Controllers\Api;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Request;
use Meilisearch\Endpoints\Indexes;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $query = $request->input('query');

        $meilisearchConfigFn = function ($attributes) {
            return function (Indexes $meilisearch, $query, $options) use ($attributes) {
                $meilisearch->updateSearchableAttributes($attributes);

                $meilisearch->updateRankingRules([
                    'typo',
                    'words',
                    'proximity',
                    'attribute',
                    'exactness',
                    'sort',
                ]);

                return $meilisearch->search($query, $options);
            };
        };

        $questions = Question::search($query, $meilisearchConfigFn(['title', 'content']))
            ->where('is_public', true)
            ->when($request->category_id, fn ($query) => $query->where('category_id', $request->category_id))
            ->take(20)
            ->get();

        $answers = Answer::search($query, $meilisearchConfigFn(['content']))
            ->where('state', 'published')
            ->take(20)
            ->get();

        return response()->json([
            'questions' => $questions,
            'answers' => $answers,
        ]);
    }
}
