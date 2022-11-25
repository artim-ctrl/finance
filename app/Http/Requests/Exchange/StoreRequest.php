<?php

namespace App\Http\Requests\Exchange;

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
            'balance_id_from' => 'required|integer',
            'amount_from' => 'required|numeric',
            'balance_id_to' => 'required|integer',
            'amount_to' => 'required|numeric',
            'exchanged_at' => 'required|date',
        ];
    }
}
