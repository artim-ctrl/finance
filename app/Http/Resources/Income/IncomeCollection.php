<?php

namespace App\Http\Resources\Income;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class IncomeCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return ['data' => $this->collection];
    }
}
