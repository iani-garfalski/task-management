<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:Pending,In Progress,Completed', // Make status optional
            'priority' => 'in:High,Medium,Low', // Make priority optional
            'due_date' => 'date|after_or_equal:today', // Make due_date optional
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
        ];
    }
}
