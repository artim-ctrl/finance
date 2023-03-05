<?php

namespace App\Http\Requests\Calendar\Month;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'to' => 'required|string|in:first,left,right',
        ];
    }
}
