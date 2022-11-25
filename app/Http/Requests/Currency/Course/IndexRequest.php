<?php

namespace App\Http\Requests\Currency\Course;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'currencies' => 'required|array',
        ];
    }
}
