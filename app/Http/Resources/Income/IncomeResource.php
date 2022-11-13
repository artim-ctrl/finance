<?php

namespace App\Http\Resources\Income;

use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Income
 */
class IncomeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'day_receiving' => $this->day_receiving,
            'currency' => $this->currency->code,
            'amount' => $this->amount,
        ];
    }
}
