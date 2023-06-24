<?php

declare(strict_types = 1);

namespace App\Http\Resources\Balance\History;

use App\Http\Resources\Balance\BalanceResource;
use App\Models\BalanceHistory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin BalanceHistory
 */
final class HistoryResource extends JsonResource
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
            'action' => $this->action->value,
            'balance' => BalanceResource::make($this->balance),
            'amount_from' => $this->amount_from,
            'amount_to' => $this->amount_to,
            'entity_type' => $this->entity_type->value,
            'entity' => $this->entity,
            'done_at' => $this->done_at->format('Y-m-d H:i:s'),
        ];
    }
}
