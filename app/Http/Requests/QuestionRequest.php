<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title' => 'required|string|min:10',
            'content' => 'required|string|min:10',
            'email' => 'required|email|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'alpha_num',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Please provide the question title.',
            'content.required' => 'Please provide the question content.',
            'email.required' => 'Please provide your email address.',
            'category_id.exists' => 'The selected category is invalid.',
        ];
    }
}
