<?php

namespace App\Http\Requests\Goal\Totals;

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
            'courses' => 'sometimes|array',
        ];
    }
}
