<?php

namespace App\Http\Resources\Calendar\Month;

use App\Http\Resources\Calendar\Month\Row\MonthRowResource;
use App\Models\CalendarMonth;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CalendarMonth
 */
class CalendarMonthResource extends JsonResource
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
            'year' => $this->year,
            'month' => $this->month,
            'rows' => MonthRowResource::collection($this->whenLoaded('rows')),
        ];
    }
}
