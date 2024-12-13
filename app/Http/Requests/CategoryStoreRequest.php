<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust if using policies or authentication.
    }

    public function rules()
    {
        return [
            'name' => 'required|string|unique:categories',
        ];
    }
}
