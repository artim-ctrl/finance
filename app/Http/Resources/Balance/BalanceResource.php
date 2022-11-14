<?php

namespace App\Http\Resources\Balance;

use App\Models\Balance;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Balance
 */
class BalanceResource extends JsonResource
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
            'currency' => $this->currency->code,
            'amount' => $this->amount,
        ];
    }
}
