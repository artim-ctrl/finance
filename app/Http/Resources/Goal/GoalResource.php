<?php

declare(strict_types = 1);

namespace App\Http\Resources\Goal;

use App\Http\Resources\GoalStep\GoalStepResource;
use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Goal
 */
final class GoalResource extends JsonResource
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
            'steps' => GoalStepResource::collection($this->whenLoaded('steps')),
        ];
    }
}
