<?php

declare(strict_types = 1);

namespace App\Http\Resources\Balance;

use App\Http\Resources\Balance\History\HistoryResource;
use App\Models\Balance;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Balance
 */
final class BalanceResource extends JsonResource
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
            'currency' => $this->currency->code,
            'amount' => $this->amount,
            'history' => HistoryResource::collection($this->whenLoaded('history')),
        ];
    }
}
