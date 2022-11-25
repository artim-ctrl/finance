<?php

namespace App\Http\Requests\Income;

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
            'day_receiving' => 'required|integer|max:31',
            'currency_id' => 'required|exists:currencies,id',
            'amount' => 'required|numeric',
        ];
    }
}
