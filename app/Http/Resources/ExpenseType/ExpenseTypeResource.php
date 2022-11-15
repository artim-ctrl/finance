<?php

namespace App\Http\Resources\ExpenseType;

use App\Http\Resources\User\UserResource;
use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ExpenseType
 */
class ExpenseTypeResource extends JsonResource
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
            'user' => UserResource::make($this->user),
        ];
    }
}
