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
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $nextIncrease = null;
        if (null !== $this->increase_month) {
            $nextIncrease = now()->setDay($this->day_receiving)->setMonth($this->increase_month);
        }

        $nextReceiving = now()->setDay($this->day_receiving);
        if ($nextReceiving->day < now()->day) {
            $nextReceiving->addMonth();
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'next_receiving' => $nextReceiving->format('d.m.Y'),
            'currency' => $this->currency->code,
            'amount' => $this->amount,
            'next_increase' => $nextIncrease?->format('d.m.Y'),
            'increase_amount' => $this->increase_amount,
        ];
    }
}
