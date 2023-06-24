<?php

declare(strict_types = 1);

namespace App\Http\Resources\Calendar\Month\Row;

use App\Models\MonthRow;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin MonthRow
 */
final class MonthRowResource extends JsonResource
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
            'amount' => $this->amount,
            'currency_id' => $this->currency_id,
        ];
    }
}
