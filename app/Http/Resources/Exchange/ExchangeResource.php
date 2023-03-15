<?php

namespace App\Http\Resources\Exchange;

use App\Http\Resources\Balance\BalanceResource;
use App\Models\Exchange;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Exchange
 */
class ExchangeResource extends JsonResource
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
            'balance_from' => BalanceResource::make($this->balanceFrom),
            'amount_from' => $this->amount_from,
            'balance_to' => BalanceResource::make($this->balanceTo),
            'amount_to' => $this->amount_to,
            'exchanged_at' => $this->exchanged_at->format('d.m.Y H:i:s'),
        ];
    }
}
