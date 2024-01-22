<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionIndexRequest extends FormRequest
{
    public function rules()
    {
        return [
            'state' => 'nullable|in:draft,ready_for_review,reviewed,published',
        ];
    }
}
