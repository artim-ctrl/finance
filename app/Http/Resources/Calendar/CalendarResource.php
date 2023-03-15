<?php

namespace App\Http\Resources\Calendar;

use App\Http\Resources\Calendar\Month\CalendarMonthResource;
use App\Models\Calendar;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Calendar
 */
class CalendarResource extends JsonResource
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
            'months' => CalendarMonthResource::collection($this->whenLoaded('months')),
        ];
    }
}
