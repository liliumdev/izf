<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnswerRequest extends FormRequest
{
    public function rules()
    {
        return [
            'content' => 'sometimes|required|string',
            'drafted_by' => 'sometimes|required|exists:users,id',
            'reviewed_by' => 'sometimes|required|exists:users,id',
            'state' => 'sometimes|required|in:draft,ready_for_review,reviewed,published',
        ];
    }

    public function messages()
    {
        return [
            'content.required' => 'The content field is required.',
            'drafted_by.required' => 'The drafted_by field is required.',
            'drafted_by.exists' => 'The selected drafted_by is invalid.',
            'reviewed_by.required' => 'The reviewed_by field is required.',
            'reviewed_by.exists' => 'The selected reviewed_by is invalid.',
            'state.required' => 'The state field is required.',
            'state.in' => 'The selected state is invalid.',
        ];
    }
}
