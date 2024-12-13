<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:Pending,In Progress,Completed',
            'priority' => 'required|in:High,Medium,Low',
            'due_date' => 'required|date|after_or_equal:today',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
        ];
    }
}
