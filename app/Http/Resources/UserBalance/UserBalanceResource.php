<?php

namespace App\Http\Resources\UserBalance;

use App\Models\UserBalance;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin UserBalance
 */
class UserBalanceResource extends JsonResource
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
