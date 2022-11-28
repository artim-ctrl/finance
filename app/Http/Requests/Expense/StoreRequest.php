<?php

namespace App\Http\Requests\Expense;

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
            'expense_type_id' => 'required|integer',
            'balance_id' => 'required|integer',
            'amount' => 'required|numeric',
            'spent_at' => 'nullable|date',
            'planned_at' => 'nullable|date',
            'for_history' => 'required|boolean',
        ];
    }
}
