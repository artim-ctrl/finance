<?php

namespace App\Http\Resources\Exchange;

use App\Http\Resources\Currency\CurrencyResource;
use App\Http\Resources\User\UserResource;
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
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user' => UserResource::make($this->user),
            'currency_from' => CurrencyResource::make($this->currencyFrom),
            'amount_from' => $this->amount_from,
            'currency_to' => CurrencyResource::make($this->currencyTo),
            'amount_to' => $this->amount_to,
            'exchanged_at' => $this->exchanged_at->format('d.m.Y H:i:s'),
        ];
    }
}
