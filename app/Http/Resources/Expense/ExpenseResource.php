<?php

declare(strict_types = 1);

namespace App\Http\Resources\Expense;

use App\Http\Resources\Balance\BalanceResource;
use App\Http\Resources\ExpenseType\ExpenseTypeResource;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Expense
 */
final class ExpenseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'type' => ExpenseTypeResource::make($this->type), // TODO: user is not required here
            'balance' => BalanceResource::make($this->balance),
            'amount' => $this->amount,
            'spent_at' => $this->spent_at?->format('d.m.Y'),
            'planned_at' => $this->planned_at?->format('d.m.Y'),
        ];
    }
}
