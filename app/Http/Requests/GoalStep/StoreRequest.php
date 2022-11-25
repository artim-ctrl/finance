<?php

namespace App\Http\Requests\GoalStep;

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
            'name' => 'required|string|max:255',
            'estimated_currency_id' => 'required|integer|exists:currencies,id',
            'estimated_amount' => 'required|numeric',
            'currency_id' => 'nullable|integer|exists:currencies,id',
        ];
    }
}
