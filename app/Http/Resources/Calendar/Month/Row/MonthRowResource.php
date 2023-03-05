<?php

namespace App\Http\Resources\Calendar\Month\Row;

use App\Models\MonthRow;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin MonthRow
 */
class MonthRowResource extends JsonResource
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
            'amount' => $this->amount,
            'currency_id' => $this->currency_id,
        ];
    }
}
