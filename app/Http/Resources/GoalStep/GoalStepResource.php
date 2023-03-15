<?php

namespace App\Http\Resources\GoalStep;

use App\Models\GoalStep;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin GoalStep
 */
class GoalStepResource extends JsonResource
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
            'goal' => $this->goal->name,
            'estimated_currency' => $this->estimatedCurrency->code,
            'estimated_amount' => $this->estimated_amount,
            'currency' => $this->currency?->code,
            'amount' => $this->amount,
        ];
    }
}
