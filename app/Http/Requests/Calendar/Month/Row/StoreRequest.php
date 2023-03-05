<?php

namespace App\Http\Requests\Calendar\Month\Row;

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
            'name' => 'required|string|max:50',
            'amount' => 'required|numeric',
            'currency_id' => 'required|integer|exists:currencies,id',
        ];
    }
}
